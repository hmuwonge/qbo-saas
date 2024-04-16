<?php

namespace App\Models;

use App\Services\QuickBooksServiceHelper;
use App\Traits\DataServiceConnector;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorCredit extends Model
{
    use HasFactory, DataServiceConnector;

    protected $appends = [
        'fdjustmentType',
        'recordOptions',
        'fiscalStatus',
    ];

    public static function prepareEfrisRequestObject($id): array
    {
        //Vendor Credit details
        $vcredit = self::getQuickbooksVendorCredit($id);
        $dbrecord = static::find($id);
        $items = []; //Items in this Transaction
        $efris = [
            'adjustType' => $dbrecord->adjust_type,
            'remarks' => $dbrecord->adjust_reason,
        ];

        //2. prepare Items
        $lineitems = $vcredit->Line;
        foreach ($lineitems as $stk) {
            $item_id = $stk->ItemBasedExpenseLineDetail->ItemRef->value;
            $efrisItem = EfrisItem::find($item_id);
            $items[] = [
                'itemCode' => $efrisItem->itemCode,
                'quantity' => abs($stk->ItemBasedExpenseLineDetail->Qty), //Convert to a positive number
                'unitPrice' => QuickBooksServiceHelper::calculateTaxInclusivePrice($stk->ItemBasedExpenseLineDetail->UnitPrice, $stk->ItemBasedExpenseLineDetail->TaxCodeRef->value),
            ];
        }

        //3. Add Items to the EFRIS Object
        $efris['stockInItem'] = $items;

        //Return the Object
        return $efris;
    }

    /**
     * Details of this VendorCredit
     */
    public static function getQuickbooksVendorCredit($id)
    {
        $qb = new self();
        $response = $qb->urlQueryBuilderById('VendorCredit', $id);
        $vcredit = json_decode($response);

        return $vcredit->VendorCredit;
    }

    public static function prepareEfrisItemLines($data)
    {
        $lines = $data['orderNumber'];
        $itemLines = [];
        foreach ($lines as $ln) {
            $itemLines[] = [
                'itemCode' => $data['itemCode'][$ln],
                'quantity' => round($data['quantity'][$ln], 2),
                'unitPrice' => round($data['unitprice'][$ln], 4),
                'total' => $data['totalprice'][$ln],
                'orderNumber' => $data['orderNumber'][$ln],
            ];
        }

        return $itemLines;
    }

    public function getFiscalStatusAttribute()
    {
        if ($this->fiscal_status == 1) {
            return 'Fiscalised';
        } else {
            return 'Not Yet Fiscalised';
        }
    }

    /**
     * Options Available for this record
     */
    public function getRecordOptionsAttribute()
    {
        // //Record is not yet fiscalised
        if ($this->fiscal_status == 0) {
            return 'fiscalise-record';
        //            $details = Url::to(['quickbooks/reduce-stock', 'id' => $this->id,'src'=>'vcredit']);
        //            return "<a href='{$details}' class='btn btn-primary btn-sm'>Fiscalise Record</a>";
        } else { //The invoice has been fiscalised
            return 'fiscalised';
        }
    }

    /**
     * Stock Adjustment type for this transaction
     *
     * @return string
     */
    public function getAdjustmentTypeAttribute()
    {
        $types = $this->getEfrisAdjustmentTypes();

        return (intval($this->adjust_type) < 100) ? ('') : ($types[$this->adjust_type]);
    }

    /**
     * List of Stock Adjustment Types
     */
    protected function getEfrisAdjustmentTypes()
    {
        return [
            101 => 'Expired Goods',
            102 => 'Damaged Goods',
            103 => 'Personal Uses',
            104 => 'Other',
            105 => 'Raw Materials',
        ];
    }
}
