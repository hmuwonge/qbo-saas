<?php

namespace App\Models;

use App\Services\ApiRequestHelper;
use App\Services\QuickBooksServiceHelper;
use App\Traits\DataServiceConnector;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditMemo extends Model
{
    use HasFactory, DataServiceConnector;

    protected $fillable = [
        'id',
        'qb_refNumber',
        'invoice_fdn',
        'efris_refNumber',
        'fiscalStatus',
        'record_type',
    ];

    protected $appends = ['oriInvoiceNo', 'sellersReferenceNo', 'creditMemoItems', 'originalInvoice', 'quickbooksCreditMemo'];

    protected $invoiceApplyCategoryCode;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->init();
    }

    protected function init()
    {
        // parent::init();
        $this->invoiceApplyCategoryCode = '101';
    }

    public function getOriInvoiceNoAttribute()
    {
        return $this->originalInvoice()->data->basicInformation->invoiceNo;
    }

    public function getSellersReferenceNoAttribute()
    {
        $qbCreditmemo = property_exists(
            $this->quickbooksCreditMemo(),
            'CreditMemo'
        )
            ? $this->quickbooksCreditMemo()->CreditMemo
            : $this->quickbooksCreditMemo()->RefundReceipt;
        $no = $qbCreditmemo->DocNumber;

        return $no;
    }

    /**
     * Build the Request Object to create a credit note from EFRIS
     */
    public static function buildEfrisRequestObject($data)
    {

        // dd($data);
        try {
            return [
                'generalInfo' => [
                    'oriInvoiceNo' => $data['CreditMemo']['oriInvoiceNo'],
                    'reasonCode' => $data['CreditMemo']['reasonCode'],
                    'reason' => $data['CreditMemo']['reason'],
                    'invoiceApplyCategoryCode' => $data['CreditMemo']['invoiceApplyCategoryCode'],
                    'remarks' => $data['CreditMemo']['remarks'],
                    'sellersReferenceNo' => $data['CreditMemo']['sellersReferenceNo'],
                ],
                'itemsBought' => self::prepareEEfrisItemLines($data),
            ];
        } catch (\Throwable $th) {
            QuickBooksServiceHelper::logToFile($th->getMessage());

            return $th->getMessage();
        }

    }

    public function getCreditMemoItemsAttribute()
    {
        $qbCreditmemo = property_exists($this->quickbooksCreditMemo(), 'CreditMemo') ? $this->quickbooksCreditMemo()->CreditMemo : $this->quickbooksCreditMemo()->ReceiptRefund;
        $lines = [];
        foreach ($qbCreditmemo->Line as $ln) {
            //Add Products Sold
            if ($ln->DetailType == 'SalesItemLineDetail') {
                $lines[] = $ln;
            }
        }

        return $lines;
    }

    /**
     * Details of the originalInvoice
     */
    public function originalInvoice()
    {
        $invoice_no = $this->invoice_fdn;
        $efris = new ApiRequestHelper('efris');
        $invoice = $efris->makeGet('invoice-details/'.$invoice_no);

        return json_decode($invoice);
    }

    /**
     * Details of the originalInvoice
     */
    public function getOriginalInvoiceAttribute()
    {
        $invoice_no = $this->invoice_fdn;
        $efris = new ApiRequestHelper('efris');
        $invoice = $efris->makeGet('invoice-details/'.$invoice_no);

        return json_decode($invoice);
    }

    /**
     * Details of the originalInvoice
     */
    public function getQuickbooksCreditMemoAttribute()
    {
        $id = $this->id;
        if ($this->record_type == 'CN') {
            $memo = $this->urlQueryBuilderById('creditmemo', $id);
        } else {
            $memo = $this->urlQueryBuilderById('RefundReceipt', $id);
        }

        return json_decode(json_encode($memo))->original;
    }

    /**
     * Details of the originalInvoice
     */
    public function quickbooksCreditMemo()
    {
        $id = $this->id;
        if ($this->record_type == 'CN') {
            $memo = $this->urlQueryBuilderById('creditmemo', $id);
        } else {
            $memo = $this->urlQueryBuilderById('RefundReceipt', $id);
        }

        return json_decode(json_encode($memo))->original;
    }

    public static function prepareEEfrisItemLines($data)
    {

        $lines = $data['orderNumber'];
        //
        // dd($lines);
        $itemLines = [];
        foreach ($lines as $key => $ln) {
            $itemLines[] = [
                'itemCode' => $ln['itemCode'],
                'quantity' => round($ln['qty'], 2),
                //     'quantity' => $data['quantity'][$ln],
                'unitPrice' => $ln['unitPrice'],
                //     //QuickBooksHelper::calculateTaxInclusivePrice($data['unitprice'][$ln],$data['taxCode'][$ln]),
                'total' => $ln['total'],
                'orderNumber' => $ln['orderNumber'],
            ];
            QuickBooksServiceHelper::logToFile($ln);
        }

        return $itemLines;
    }
}
