<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuickBooksInvoicesDatatable extends Model
{
    use HasFactory;

    protected $fillable = [
        'AllowIPNPayment',
        'AllowOnlinePayment',
        'AllowOnlineCreditCardPayment',
        'AllowOnlineACHPayment',
        'domain',
        'Id',
        'sparse',
        'CustomField',
        'DocNumber',
        'TxnDate',
        'CurrencyRef',
        'ExchangeRate',
        'LinkedTxn',
        'Line',
        'TxnTaxDetail',
        'CustomerRef',
        'BillAddr',
        'FreeFormAddress',
        'DueDate',
        'GlobalTaxCalculation',
        'TotalAmt',
        'HomeTotalAmt',
        'Balance',
        'HomeBalance',
    ];

    public $domain; //String

    public $sparse; //boolean

    public $Id; //String

    public $SyncToken; //String

    public $MetaData; //MetaData

    public $CustomField; //array( CustomField )

    public $DocNumber; //String

    public $TxnDate; //Date

    public $CurrencyRef; //CurrencyRef

    public $ExchangeRate; //double

    public $LinkedTxn;  //array( undefined )

    public $Line; //array( Line )

    public $TxnTaxDetail; //TxnTaxDetail

    public $CustomerRef; //CustomerRef

    public $DueDate; //Date

    public $TotalAmt; //int

    public $PrintStatus; //String

    public $EmailStatus; //String

    public $Balance; //int

    public $BillAddr;

    public $ShipAddr;

    public $BillEmail;

    public $databaseRecord;

    public $Deposit;

    public $PaymentMethodRef;

    public $ClassRef;

    public function __construct($dbInvoice=[], $config = [])
    {
        $this->databaseRecord = json_decode(json_encode($dbInvoice));
        $this->DocNumber = $config['DocNumber'] ??null;
        $this->CustomerRef = @$config['CustomerRef'];
        $this->TotalAmt = @$config['TotalAmt'];
        $this->TxnDate = @$config['TxnDate'];
        $this->DueDate = @$config['DueDate'] ??null;
        $this->CurrencyRef = @$config['CurrencyRef'];
        parent::__construct($config);
    }

    protected $appends = [
        'refNumber',
        'transactionDate',
        'industryCode',
        'customerDetails',
        'buyerType',
        'totalAmount',
        'fiscalStatus',
        'validationErrors',
        //        'receiptOptions'
        'invoiceOptions',
        'availableBalance',
    ];

    /**
     * The Invoice Reference Number
     */
    public function getRefNumberAttribute()
    {
        Log::debug($this->DocNumber);

        return $this->DocNumber;
    }

    /**
     * The Invoice validation errors Number
     */
    public function getValidationErrorsAttribute()
    {
        if (! is_null($this->databaseRecord)) {
            $validationErrors = explode(',', $this->databaseRecord->validationError);
            $listItems = '';
            foreach ($validationErrors as $error) {
                $listItems .= "<li>{$error}</li>";
            }

            $html = "<ul style='list-style-type:square;padding-left:10px;'>{$listItems}</ul>";

            return $html;
        }

    }

    /**
     * Invoice Transaction Date
     *
     * @return type
     */
    public function getTransactionDateAttribute()
    {
        return $this->TxnDate;
    }

    public function getIndustryCodeAttribute()
    {
        if (isset($this->databaseRecord) && !empty($this->databaseRecord->industryCode)) {
            $code = $this->databaseRecord->industryCode;
            $industryCode = [
                101 => 'General Industry',
                102 => 'Export',
                104 => 'Imported Service',
                105 => 'Telecom',
                106 => 'Stamp Duty',
                107 => 'Hotel Service',
                108 => 'Other Taxes',
            ];

            return isset($code) ? ($industryCode[$code]) : ('');
        }

        return null;
    }

    public function getCustomerDetailsAttribute()
    {
        if (isset($this->databaseRecord)) {
            $customfields = $this->CustomField;


            return '<span class="fw-bolder">Name: </span>'.$this->databaseRecord->customerName.'<br/><span class="fw-bolder">TIN: </span>'.$this->databaseRecord->buyerTin;
        }
        return '<span class="fw-bolder">Name: </span>'.@$this->databaseRecord->customerName.'<span class="fw-bolder">TIN: </span>'.@$this->databaseRecord->buyerTin;

    }

    /**
     * Buyer Type
     *
     * @return string
     */
    public function getBuyerTypeAttribute()
    {
        if (isset($this->databaseRecord)) {
            $type = $this->databaseRecord->buyerType;

            $buyer_type = [
                ''=>'Select buyer type',
                0 => 'Business(B2B)',
                1 => 'Consumer (B2C)',
                2 => 'Foreigner',
                3 => 'Government(B2G)',
            ];

            return isset($type) ? ($buyer_type[$type]) : ('');
        }
    }

    public function getTotalAmountAttribute()
    {
        return number_format($this->TotalAmt).' '.data_get($this->CurrencyRef, 'value');
    }

    public function getAvailableBalanceAttribute()
    {
        return number_format($this->Balance).' '.data_get($this->CurrencyRef, 'value');
    }

    public function getDueDateAttribute()
    {
        return Carbon::createFromDate($this->DueDate);
    }

    public function getFiscalStatusAttribute()
    {
        if (! empty($this->databaseRecord)) {
            if ($this->databaseRecord->fiscalStatus == 1) {
                return "<span class='badge bg-success'>Fiscalised</span>";
            } else {
                return "<span class='badge bg-danger '>Not Yet Fiscalised</span>";
            }
        }
    }

    /**
     * Options Available for this invoice
     */
    public function getInvoiceOptionsAttributke()
    {
        if (! empty($this->databaseRecord)) {
            //The Invoice has validation Errors
            if ($this->databaseRecord->validationStatus == 0) {
                return "<span class='badge bg-label-dark'>Has validation Errors</span>";
            }

            // //The invoice is valid, but not yet fiscalised
            if ($this->databaseRecord->fiscalStatus == 0 && $this->databaseRecord->validationStatus == 1) {
                $details2 = route('invoices.sample', ['id' => $this->attributes['Id'], 'invoice' => $this->databaseRecord->invoice_kind]);
                $buttons = "<a href='{$details2}' class='btn btn-sm btn-primary' target='_blank'>Preview</a>";

                return [
                    'fisStatus' => 'fiscalise',
                    'preview' => $buttons,
                ];

            } else { //The invoice has been fiscalised
                //                $details2 = route('invoice.download.rt', ['id' => $this->databaseRecord->fiscalNumber]);
                //                $buttons = "<a href='{$details2}' class='bg-sky-600 rounded text-white px-2 py-1' target='_blank'>Preview</a>";
                $buttons = "<span class='badge bg-success'>Fiscalised</span>";

                return $buttons;
            }
        }
    }

    public function getInvoiceOptionsAttribute() {
        if (!empty($this->databaseRecord)) {
            // The Invoice has validation Errors
            if ($this->databaseRecord->validationStatus == 0) {
                return "<span class='badge bg-dark'>Has validation Errors</span>";
            }

            // The invoice is valid, but not yet fiscalised
            if ($this->databaseRecord->fiscalStatus == 0 && $this->databaseRecord->validationStatus == 1) {
                $details = route('invoice.fiscalise', ['id' => $this->attributes['Id'], 'kind' => $this->databaseRecord->invoice_kind]);
                $details2 = route('invoices.sample', ['id' => $this->attributes['Id'], 'invoice' => $this->databaseRecord->invoice_kind]);
                $buttons = "<a href='{$details2}' class='btn btn-sm btn-info'>Preview</a>";
                $buttons .= "&nbsp;<a href='{$details}' class='btn btn-primary btn-sm'>Fiscalise</a>";
                return $buttons;
            } else { // The invoice has been fiscalised
                return "<span class='btn btn-sm btn-success'>Fiscalised</span>";
            }
        }
    }
}
