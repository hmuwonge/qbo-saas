<?php

namespace App\Models;

use App\Services\QuickBooksServiceHelper;
use App\Traits\DataServiceConnector;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class QuickBooksPurchase extends Model
{
    use HasFactory, DataServiceConnector;

    protected $fillable = [
        'uraSyncStatus',
        'stockInType',
        'validationStatus',
        'validationError',
    ];

    public static function getItemUnitPrice(int $unitPrice, int $taxCode): float|int
    {
        $finalPrice = 0;
        if ($taxCode == 28) {
            //$finalPrice = 1.18 * $unitPrice;
            $finalPrice = round((0.18 * $unitPrice) + $unitPrice, 2);
        } else {
            $finalPrice = $unitPrice;
        }
        //@TODO. Round of value to two decimal places
        return $finalPrice;
    }

    /**
     * Build EFRIS Request Params
     */
    public static function prepareEfrisStockIncrease($id)
    {
        $qbPurchase = self::getPurchaseDetails($id);
        $instance = new self();

        // Get Items bought in this purchase
        if ($qbPurchase) { //Do we have this record in QB?
            //Pick DB details
            $dbPurchase = $instance->find($id);
            //Get Items bought in this purchase
            $data = json_decode(json_encode($qbPurchase), true);

            $itemsBought = [];
            foreach ($data['Line'] as $line) {
                if ($line['DetailType'] == 'ItemBasedExpenseLineDetail') {
                    $itemsBought[] = $line['ItemBasedExpenseLineDetail'];
                }
            }

            //Log
//            QuickBooksServiceHelper::logToFile($itemsBought, 'Items bought');

            //supplier name
            $supplierName = ($dbPurchase->stockInType == 103) ? ('') : ($data['VendorRef']['name']);

//            dd($itemsBought);
            return [
                'supplierTin' => '',
                'supplierName' => $supplierName,
                'remarks' => @$data['PrivateNote'],
                'stockInDate' => $data['TxnDate'],
                'stockInType' => $dbPurchase->stockInType, //Import? Local Purchase? Manufacture?
                'productionBatchNo' => '',
                'productionDate' => '',
                'stockInItem' => self::prepareStockInItems($itemsBought),
            ];
        } else {
            return false;
        }
    }

    public static function prepareStockInItems($items)
    {
        $stock = [];

        foreach ($items as $item) {
            $vatCode = Arr::get($item, 'TaxCodeRef.value');
            $efrisItem = EfrisItem::where('id', Arr::get($item, 'ItemRef.value'))->first();
//            dd($efrisItem);

            if ($efrisItem instanceof EfrisItem) {
                $unitPrice = $item['UnitPrice'] ?? 0;
                $stock[] = [
                    'itemCode' => $efrisItem->itemCode ?? 'NO_CODE',
                    'quantity' => $item['Qty'] ?? 0,
                    'unitPrice' => QuickbooksPurchase::getItemUnitPrice($unitPrice, $vatCode),
                ];
            } else {
                // Exit if any of the items isn't registered
                return false;
            }
        }

        return $stock;
    }

    /**
     * Get QuickBooks Purchase Details
     */
    public static function getPurchaseDetails($id)
    {
        $intance = new self();
        // dd($id);
        $item = $intance->urlQueryBuilderById('bill', $id);

        $purchase = json_decode(json_encode($item))->original;

        if (is_object($purchase) && property_exists($purchase, 'Bill')) {
            return $purchase->Bill;
        } else {
            return false;
        }
    }
}
