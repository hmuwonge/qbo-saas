<?php

namespace App\Http\Controllers;

use App\Models\EfrisItem;
use App\Services\ApiRequestHelper;
use Illuminate\Support\Arr;
use Mockery\Exception;

class EfrisItemController extends Controller
{
    public static function getEfrisProductDetails($code)
    {
        $api = new ApiRequestHelper('efris');
        $response = $api->makePost($api->getUrl('get-one-product'), ['code' => $code]);

        return json_decode($response->body());
    }

    public static function batchInsert($records, $qbItems)
    {
        foreach ($records as $record) {
            if (Arr::has($qbItems, $record['goodsCode'])) {
                $id = Arr::get($qbItems, $record['goodsCode']);
                self::customInsert($record, $id);
            }
        }

        return true;
    }

    protected static function customInsert($fromefris, $qbId)
    {
        $is_exists = EfrisItem::find($qbId);
        try {
            if ($is_exists) {
                $new_item = new EfrisItem;
                $new_item->itemCode = $fromefris['goodsCode'];
                $new_item->currency = $fromefris['currency'];
                $new_item->unitOfMeasure = $fromefris['measureUnit'];
                $new_item->commodityCategoryId = $fromefris['commodityCategoryCode'];
                $new_item->havePieceUnit = $fromefris['havePieceUnit'];
                $new_item->pieceUnitPrice = $fromefris['pieceUnitPrice'];
                $new_item->haveExciseTax = $fromefris['haveExciseTax'];
                $new_item->stockStatus = $fromefris['statusCode'];
                $new_item->exciseDutyCode = $fromefris['exciseDutyCode'];
                $new_item->pieceScaledValue = $fromefris['pieceScaledValue'];
                $new_item->packageScaleValue = $fromefris['packageScaledValue'];
                $new_item->pieceMeasureUnit = $fromefris['pieceMeasureUnit'];
                $new_item->haveOtherUnit = $fromefris['haveOtherUnit'];
                $new_item->item_tax_rule = 'URA';
                $new_item->save();
            }

            return response()->json('There was a problem saving records to the database');
        } catch (Exception $exception) {
            return response()->json($exception->getMessage());
        }
    }
}
