<?php

namespace App\Models;

use App\Services\QuickBooksServiceHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class EfrisItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'qbo_id',
        'itemCode',
        'unitOfMeasure',
        'currency',
        'commodityCategoryId',
        'haveExciseTax',
        'havePieceUnit',
        'stockStatus',
        'haveOtherUnit',
        'pieceUnitPrice',
        'haveExciseTax',
        'hasOpeningStock',
        'haveOtherUnit',
        'stockin_quantity',
        'stockin_remarks',
        'opening_stock_remarks',
        'stockin_date',
        'exciseDutyCode',
        'pieceScaledValue',
        'pieceMeasureUnit',
        'stockin_supplier',
        'stockin_supplier_tin',
        'stockin_measureUnit',
        'otherUnit',
        'otherPrice',
        'otherScaled',
        'packageScaled',
        'packageScaledValue',
        'item_tax_rule',
    ];

    public static function getItemsRegistered()
    {
        return self::count();
    }

    /**
     * @param array $form Attributes submitted from the form
     * @param array $quickbk Quickbooks Item Object
     */
    public static function createEfrisProduct(array $form, array $quickbk): JsonResponse|array
    {
        try {
            $item = [];
            //Assign values submitted from the user form
            $item['measureUnit'] = $form['unitOfMeasure'];
            $item['currency'] = $form['currency'];
            $item['commodityCategoryId'] = $form['commodityCategoryId'];
            $item['havePieceUnit'] = $form['havePieceUnit'];
            $item['pieceUnitPrice'] = $form['pieceUnitPrice'];
            $item['pieceScaledValue'] = $form['pieceScaledValue'];
            $item['pieceMeasureUnit'] = $form['pieceMeasureUnit'];
            $item['packageScaledValue'] = $form['packageScaledValue'];
            $item['haveExciseTax'] = $form['haveExciseTax'];
            $item['haveOtherUnit'] = $form['haveOtherUnit'];
            $item['goodsOtherUnits'] = [
                [
                    'otherUnit' => (string) $form['otherUnit'],
                    'otherPrice' => (string) $form['otherPrice'],
                    'otherScaled' => (string) $form['otherScaled'],
                    'packageScaled' => (string) $form['packageScaled'],
                ],
            ]; //$form['otherUnit'];
            $item['exciseDutyCode'] = $form['exciseDutyCode'];
            //Map Quickbooks Item details
            $item['goodsName'] = $quickbk['Name'];
            $item['goodsCode'] = @$quickbk['Sku'];
            $item['unitPrice'] = ($quickbk['UnitPrice'] == 0) ? 1 : ($quickbk['UnitPrice']);
            $item['description'] = isset($quickbk['Description']) ? (strip_tags($quickbk['Description'])) : ('');
            $item['stockPrewarning'] = isset($quickbk['ReorderPoint']) ? ($quickbk['ReorderPoint']) : (0);

            return $item;
        } catch (\Throwable $throwable) {
            return response()->json($throwable->getMessage());
        }
    }

    /**
     * Prepare the opening stock Object
     */
    public static function prepareOpeningStockObject($form): array
    {
        return [
            'supplierTin' => (string) $form['stockinSupplierTin'],
            'supplierName' => (string) $form['stockInsupplier'],
            'remarks' => (string) $form['stockInRemarks'],
            'stockInDate' => (string) $form['stockInDate'],
            'stockInType' => '104', //Opening Stock
            'stockInItem' => [
                [
                    'itemCode' => (string) $form['itemCode'],
                    'quantity' => (string) $form['stockinQuantity'],
                    'unitPrice' => (string) $form['stockinPrice'],
                ],
            ],
        ];
    }

    /**
     * Prepare the modifying product Object
     */
    public static function prepareModifyStockObject($form): array
    {
        return [
            'products' => [[
                'operationType' => '102',
                'goodsName' => (string) $form['goodsName'],
                'goodsCode' => (string) $form['itemCode'],
                'measureUnit' => (string) $form['unitOfMeasure'],
                'unitPrice' => (string) $form['unitPrice'],
                'stockPrewarning' => (string) $form['stockPrewarning'],
                'description' => (string) $form['description'],
                'currency' => (string) $form['currency'],
                'haveExciseTax' => (string) $form['haveExciseTax'],
                'commodityCategoryId' => (string) $form['commodityCategoryId'],
                'havePieceUnit' => (string) $form['havePieceUnit'],
                'pieceUnitPrice' => (string) $form['pieceUnitPrice'],
                'pieceMeasureUnit' => (string) $form['pieceMeasureUnit'],
                'pieceScaledValue' => (string) $form['pieceScaledValue'],
                'packageScaledValue' => (string) $form['packageScaleValue'],
                'haveOtherUnit' => (string) $form['haveOtherUnit'],
                'goodsOtherUnits' => [
                    [
                        'otherUnit' => (string) $form['otherUnit'],
                        'otherPrice' => (string) $form['otherPrice'],
                        'otherScaled' => (string) $form['otherScaled'],
                        'packageScaled' => (string) $form['packageScaled'],
                    ],
                ],
            ]],
        ];
    }

    public static function batchInsert($records, $qbItems)
    {
        $data = $qbItems->toArray();
        foreach ($records as $record) {
            if (array_key_exists($record['goodsCode'], $data)) {
                $id = Arr::get($qbItems, $record['goodsCode']);
                self::customInsert($record, $id['Id']);
            }
        }
        return true;
    }

    public static function customInsert($fromefris, $qbId)
    {

//        Log::info($qbId);

//        dd($fromefris,$qbId);
        $item = new EfrisItem;
        $item->id = $qbId;
        $item->itemCode = $fromefris['goodsCode'];
        $item->currency = $fromefris['currency'];
        $item->unitOfMeasure = $fromefris['measureUnit'];
        $item->commodityCategoryId = $fromefris['commodityCategoryCode'];
        $item->havePieceUnit = $fromefris['havePieceUnit'];
        $item->pieceUnitPrice = @$fromefris['pieceUnitPrice'];
        $item->haveExciseTax = $fromefris['haveExciseTax'];
        $item->stockStatus = $fromefris['statusCode'];
        $item->stockin_remarks = @$fromefris['remarks'];
        $item->stockin_quantity = $fromefris['stock'];
        $item->exciseDutyCode = @$fromefris['exciseDutyCode'];
        $item->pieceScaledValue = @$fromefris['pieceScaledValue'];
        $item->packageScaleValue = @$fromefris['packageScaledValue'];
        $item->pieceMeasureUnit = @$fromefris['pieceMeasureUnit'];
        $item->haveOtherUnit = $fromefris['haveOtherUnit'];
        $item->item_tax_rule = 'URA';
        $item->registration_status = 1;

        //Check if we have record already
        if (! EfrisItem::where('id', $qbId)->exists()) {
            try {
                $item->save();
            } catch (QueryException $e) {
                QuickBooksServiceHelper::logToFile($e->getMessage());
            }
        }

//        dd('we can update record');
        $update_item =  EfrisItem::find($qbId);
//        $update_item->id = $qbId;
        $update_item->itemCode = $fromefris['goodsCode'];
        $update_item->currency = $fromefris['currency'];
        $update_item->unitOfMeasure = $fromefris['measureUnit'];
        $update_item->commodityCategoryId = $fromefris['commodityCategoryCode'];
        $update_item->havePieceUnit = $fromefris['havePieceUnit'];
        $update_item->pieceUnitPrice = @$fromefris['pieceUnitPrice'];
        $update_item->haveExciseTax = $fromefris['haveExciseTax'];
        $update_item->stockStatus = $fromefris['statusCode'];
        $update_item->stockin_remarks = @$fromefris['remarks'];
        $update_item->stockin_quantity = $fromefris['stock'];
        $update_item->exciseDutyCode = @$fromefris['exciseDutyCode'];
        $update_item->pieceScaledValue = @$fromefris['pieceScaledValue'];
        $update_item->packageScaleValue = @$fromefris['packageScaledValue'];
        $update_item->pieceMeasureUnit = @$fromefris['pieceMeasureUnit'];
        $update_item->haveOtherUnit = $fromefris['haveOtherUnit'];
        $update_item->item_tax_rule = 'URA';
        $update_item->registration_status = 1;
        $update_item->update();

        return true;
    }
}
