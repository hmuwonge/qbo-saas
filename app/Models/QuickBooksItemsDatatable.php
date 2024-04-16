<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class QuickBooksItemsDatatable extends Model
{
    use HasFactory;

    protected $fillable = [
        'Name',
        'Sku',
        'Active',
        'Description',
        'PurchaseDesc',
        'PrintGroupedItems',
        'ItemGroupDetail',
        'SubItem',
        'ParentRef',
        'Level',
        'ReorderPoint',
        'FullyQualifiedName',
        'Taxable',
        'SalesTaxIncluded',
        'UnitPrice',
        'Type',
        'IncomeAccountRef',
        'PurchaseTaxIncluded',
        'PurchaseCost',
        'ExpenseAccountRef',
        'AssetAccountRef',
        'PrefVendorRef',
        'TrackQtyOnHand',
        'QtyOnHand',
        'SalesTaxCodeRef',
        'PurchaseTaxCodeRef',
        'InvStartDate',
        'domain',
        'sparse',
        'Id',
        'SyncToken',
        'MetaData',
        'TaxClassificationRef',
        'ClassRef',
    ];

    public $databaseRecord;

    public $efrisRecord;

    //QB Columns
    public $Name;

    public $Sku;

    public $Active = true;

    public $Description;

    public $PurchaseDesc;

    public $PrintGroupedItems;

    public $ItemGroupDetail;

    public $SubItem;

    public $ParentRef;

    public $Level;

    public $ReorderPoint;

    public $FullyQualifiedName;

    public $Taxable = true;

    public $SalesTaxIncluded = true;

    public $UnitPrice = 0;

    public $Type = 'Inventory';

    public $IncomeAccountRef;

    public $PurchaseTaxIncluded;

    public $PurchaseCost;

    public $ExpenseAccountRef = [];

    public $AssetAccountRef = [];

    public $PrefVendorRef = [];

    public $TrackQtyOnHand = true;

    public $QtyOnHand;

    public $SalesTaxCodeRef = [];

    public $PurchaseTaxCodeRef = 0;

    public $InvStartDate;

    public $domain = 'QBO';

    public $sparse = false;

    public $Id;

    public $SyncToken = 0;

    public $MetaData = [];

    public $TaxClassificationRef;

    //
    public $ClassRef;

    public function __construct($dbItem = null, $efris = null, $attributes = [])
    {
        $this->databaseRecord = $dbItem;
        $this->efrisRecord = $efris;
        parent::__construct($attributes);
    }

    protected $appends = ['registeredDate', 'stockLevel', 'UnitPriceAmount', 'EfrisRegStatus', 'OpeningStock', 'ItemOptions'];

    /**
     * Invoice Transaction Date
     *
     * @return string
     */
    public function getRegisteredDateAttribute()
    {
        return $this->attributes['register_date'] = Date::parse($this->attributes['MetaData']['CreateTime'])->format('Y-m-d');
    }

    /**
     * Stock Level with EFRIS
     */
    public function getStockLevelAttribute()
    {
        if (array_key_exists('QtyOnHand', $this->attributes)) {
            // Key 'QtyOnHand' exists in the $this->attributes array
            return number_format($this->attributes['QtyOnHand']);
        }
         else {
           return "<badge class='btn btn-warning btn-sm me-1 my-1 fw-semibold'>Not Set</badge>";
         }


    }

    /**
     * The Total Invoice Amount
     */
    public function getUnitPriceAmountAttribute(): string
    {
      if (array_key_exists('UnitPrice', $this->attributes) && $this->attributes['UnitPrice'] !== null) {
        return 'UGX ' . number_format($this->attributes['UnitPrice']);
      }
      else {
        return "<badge class='btn btn-warning btn-sm me-1 my-1 fw-semibold'>Not Set</badge>";
      }

    }

    public function getEfrisRegStatusAttribute(): string
    {
        if (! empty($this->databaseRecord)) {
            return "<badge class='btn btn-success btn-sm me-1 my-1 fw-semibold'>Registered with URA</badge>";
        } else {
            return "<badge class='btn btn-dark btn-sm me-1 my-1 fw-semibold'>Not yet registered</badge>";
        }
    }

    /**
     * Links to add Opening stock
     */
    public function getOpeningStockAttribute(): string
    {
        if (! empty($this->databaseRecord)) {//Not yet registered
//          if ($this->databaseRecord['stockStatus'] == '101'){
//            return "<badge class='badge bg-label-success'>Already Stocked</badge>";
//          }
            $register_link = route('quickbooks.register-stock', ['id' => $this->attributes['Id']]);

            return "<a href='{$register_link}' class='btn btn-sm btn-outline-warning'>Register Opening Stock</a>";
        } else { //Huh?
            return "<badge class='btn btn-primary btn-sm mb-1'>First register item</badge>";
        }
    }

    /**
     * Options Available for this Item
     */
    public function getItemOptionsAttribute(): string
    {
        //        dd($this->attributes['Sku']);
        if (! empty($this->databaseRecord)) {//Not yet registered
            $details_link = route('goods.product-details', ['id' => $this->attributes['Id']]);

            return "<a href='{$details_link}' class='btn btn-info btn-sm mb-1'>View item details</a>";
        } else { //Huh?
            if (optional($this->attributes)['Sku']) {
                $register_link = route('quickbooks.register-product', ['id' => $this->attributes['Id']]);

                return "<a href='{$register_link}' class='btn btn-primary btn-sm mb-1'>Register with URA</a>";
            } else {
                return "<badge class='btn btn-danger btn-sm mb-1'>Missing Item Code</badge>";
            }
        }
    }
}
