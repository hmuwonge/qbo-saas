<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class QuickBooksInvoiceDataTable
{
    public $AllowIPNPayment; //boolean

    public $AllowOnlinePayment; //boolean

    public $AllowOnlineCreditCardPayment; //boolean

    public $AllowOnlineACHPayment; //boolean

    public $domain; //String

    public $sparse; //boolean

    public $Id; //String

    public $SyncToken; //String

    public $MetaData; //MetaData

    public $CustomField; //array( CustomField )

    public $DocNumber; //String

    public $TxnDate; //Date

    public $DepartmentRef; //DepartmentRef

    public $CurrencyRef; //CurrencyRef

    public $ExchangeRate; //double

    public $LinkedTxn;  //array( undefined )

    public $Line; //array( Line )

    public $TxnTaxDetail; //TxnTaxDetail

    public $CustomerRef; //CustomerRef

    public $CustomerMemo; //CustomerMemo

    public $FreeFormAddress; //boolean

    public $SalesTermRef; //SalesTermRef

    public $DueDate; //Date

    public $GlobalTaxCalculation; //String

    public $TotalAmt; //int

    public $HomeTotalAmt; //double

    public $PrintStatus; //String

    public $EmailStatus; //String

    public $Balance; //int

    public $HomeBalance; //double

    public $BillAddr;

    public $ShipAddr;

    public $BillEmail;

    public $DeliveryInfo;

    public $databaseRecord;

    public $ShipFromAddr;

    public $PrivateNote;

    public $EInvoiceStatus;

    public $ApplyTaxAfterDiscount;

    public $BillEmailCc;

    public $TaxExemptionRef;

    public $BillEmailBcc;

    public $Deposit;

    public $RecurDataRef;

    public $DepositToAccountRef;

    public $ShipDate;

    public $TrackingNum;

    public $PaymentMethodRef;

    public $TaxClassificationRef;

    public $ClassRef;

    public $PaymentRefNum;

    public function __construct($dbInvoice, $attributes = [])
    {
        $this->databaseRecord = $dbInvoice;
        parent::__construct($attributes);
    }

    /**
     * The Invoice Reference Number
     */
    public function getRefNumber()
    {
        Log::debug($this->DocNumber);

        return $this->DocNumber;
    }

    /**
     * Invoice Transaction Date
     *
     * @return type
     */
    public function getTransactionDate()
    {
        return $this->TxnDate;
    }

    public function getIndustryCode()
    {
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

    public function getCustomerDetails()
    {
        $customfields = $this->CustomField;

        return '<b>Name: </b>'.Arr::get($this->CustomerRef, 'name').'<br/><b>TIN: </b>'.$this->databaseRecord->buyerTin;
    }

    /**
     * The Total Invoice Amount
     *
     * @return type
     */
    public function getTotalAmount()
    {
        return number_format($this->TotalAmt, 2).array_get($this->CurrencyRef, 'value');
    }
}
