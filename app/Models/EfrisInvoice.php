<?php

namespace App\Models;

use App\Services\ApiRequestHelper;
use App\Services\EfrisInvoiceService;
use App\Services\QuickBooksServiceHelper;
use App\Traits\QboRequestHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EfrisInvoice extends Model
{
    use HasFactory, QboRequestHelper;

    protected $table = 'quick_books_invoices';

    public $discountAmount = 0;
    public $productsSold = [];
    public $errorMsg = [];
    //
    public $issue_date = '';
    public $customer_name = '';

    protected $fillable = [
        'refNumber',
        'fiscalNumber',
        'fiscalStatus',
        'verificationCode',
        'validationStatus',
        'buyerType',
        'validationError',
        'qrCode',
        'branchCode',
        'buyerTin',
        'qb_created_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Perform operations before creating the model
            $model->fiscalStatus = 0;
            $model->fiscalNumber = 0;
        });
    }

    public static function validInvoices()
    {
        return static::where('validationStatus', 1)->limit(500)->get()->toArray();
    }

    public static function saveInvoiceSummary($id, $inv)
    {
        $invoice = new EfrisInvoice;
        $invoice->id = $id;
        $invoice->refNumber = $inv->DocNumber;
        $invoice->customerName = $inv->CustomerRef->name;
        $invoice->totalAmount = $inv->TotalAmt;
        $invoice->balanceDue = $inv->Balance;
        $invoice->dueDate = $inv->TxnDate;
        $invoice->invoice_kind = 'INVOICE';
        $invoice->buyerType = 2;
        $invoice->qb_created_at = Carbon::now()->toDateString();
        QuickBooksServiceHelper::logToFile($id, $inv->DocNumber);

        //Valid records
        $valid = self::validInvoices();

        //If this record exists
        if (! in_array($id, array_column($valid, 'id'))) {
            $erros = self::getInvoiceValidationErrors($id);

            // dd($erros);
            if ($erros !== null) {
                // if (count($erros) > 0) {
                $invoice->validationError = implode(',', $erros);
                $invoice->validationStatus = 0;
            } else {
                $invoice->validationStatus = 1;
            }

            if (static::where('id', $id)->exists()) { //EfrisInvoice replaced with static
                $myInvoice = static::where('id', $id)->first();
                $myInvoice->validationError = implode(',', $erros);
                $myInvoice->refNumber = $inv->DocNumber;
                $myInvoice->validationStatus = (count($erros) > 0) ? (0) : (1);

                return $myInvoice->update();
            } else {
                if ($invoice->save()) {
                    session()->flash('success', 'Successfully Updated your DB');
                } else {
                    session()->flash('danger', 'Sorry, we could not save the invoice details');
                }
            }
          

        }

        return true;
    }

    /**
     * Invoices which have passed the validation test
     */

    /**
     * Validate Stock Item
     *
     * @param  int  $id
     * @param  int  $qty
     */
    public function validateStockItem($id, $qty)
    {
        $errors = [];
        //1. Check if the item is registered
        $item = EfrisItem::where('id', $id)->first();
        if (! $item) {
            $errors[] = "Item {$id} not registered in URA";
        }

        //2. check the quantity
        $api = new ApiRequestHelper('efris');
        $efris_item = $api->makePost('find-one-product', ['code' => $item->itemCode]);

        return $efris_item;
    }

    /**
     * Validation errors for this invoice
     *
     * @param  type  $id
     */
    public static function getInvoiceValidationErrors($id)
    {
        $inv = new self();

        $invoice = self::getInvoiceDetails($id);
        if ($invoice) {
            $efrisInvoice = $inv->createEfrisInvoice($invoice->Invoice);

            return $efrisInvoice['errors'];
        }
    }

    /**
     * Check if this invoice can be fiscalised by the EFRIS platform
     * and return Validation messages
     */
    public function getInvoiceValidationMessages($data)
    {
        if (! isset($data['buyerDetails']['buyerType'])) {
            $this->errorMsg[] = "The customer 'BuyerType' is not specified";
        }

        $tin = auth()->user()->company->tin;
        if ($tin != 1000156178) { //If this is NOT Shares Uganda. They only do exports
            //If we are dealing with B2G or B2C but the TIN is not specified...
            if (isset($data['buyerDetails']['buyerType']) && ($data['buyerDetails']['buyerType'] == 0) && (empty($data['buyerDetails']['buyerTin']))) {
                $this->errorMsg[] = 'The customer TIN is not specified';
            }
        }

        return $this->errorMsg;
    }

    /**
     * Details of an Invoice from an ID
     *
     * @param  int  $id
     * @return object
     */
    public static function getInvoiceDetails($id)
    {
        $api = new EfrisInvoiceService;
        $item = $api->urlQueryBuilderById('invoice', $id);

        return json_decode($item);
    }

      /**
     * Calculate the excise Tax by Percentage
     * @param $exciseRate The excice Tax rate, e.g. 0.12
     */
    public function getItemExciseTaxByPercentage($unitPrice,$qty,$exciseRate){
        $vat = $unitPrice-($unitPrice/1.18);
        $netRateWithVat = ($unitPrice/$vat)/(1+$exciseRate) + $vat;
       return $unitPrice-$netRateWithVat;
    }

    /**
     * The discount Flag
     * @param Number $unitPrice
     * @return int
     */
    public function getDiscountFlag($unitPrice) {
        $disc_flag = 2;
        if ($unitPrice == $this->discountAmount) { //Discount on whole Item
            $disc_flag = 1;
        }

        if ($this->discountAmount == 0) { //No Discount
            $disc_flag = 2;
        } else { //Part of the Item is discounted
            $disc_flag = 0;
        }
        return $disc_flag;
    }
}
