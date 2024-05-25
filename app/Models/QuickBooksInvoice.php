<?php

namespace App\Models;

use App\Facades\Utility;
use App\Facades\UtilityFacades;
use App\Services\EfrisInvoiceService;
use App\Services\QBOServices\QuickbooksApiClient;
use App\Services\QuickBooksServiceHelper;
use App\Traits\DataServiceConnector;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class QuickBooksInvoice extends Model
{
    use HasFactory, DataServiceConnector;

    protected $fillable = [
        'refNumber',
        'customerName',
        'totalAmount',
      'tax_amount',
        'buyerTin',
        'purchase_order',
        'balanceDue',
        'balanceDue',
        'buyerType',
        'industryCode',
        'invoice_kind',
        'qb_created_at',
      'validationError',
      'validationStatus',
        'fiscalStatus',
        'branchCode',
    ];

    private array $errorMsg;

    public $discountAmount = 0;

    public $invoiceLineDiscount = [];
    private string|int $tin = '';

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Perform operations before creating the model
            $model->fiscalStatus = 0;
            $model->fiscalNumber = 0;
        });
    }

    protected $appends = [
        'amount',
    ];

    // Accessor for the amount column
    public function getAmountAttribute()
    {
        // dd((int)($this->totalAmount));
        // Format the amount as currency
        $formattedAmount = (int)($this->totalAmount); // Assuming the integer value represents cents

        return 'UGX' . ' ' . number_format($formattedAmount, 2);
    }


    // QuickbooksInvoices.php
    public function scopeCustomerName($query, $customerName)
    {
        return $query->where('customerName', 'like', '%' . $customerName . '%');
    }

    public function scopeRefNumber($query, $refNumber)
    {
        return $query->where('refNumber', 'like', '%' . $refNumber . '%');
    }

    public function scopeInvoiceKind($query, $invoiceKind)
    {
        return $query->where('invoice_kind', 'like', '%' . $invoiceKind . '%');
    }

    public function scopeCreatedAt($query, $period)
    {
        if (!empty($period)) {
            $dates = explode(' to ', $period);
            $query->where('qb_created_at', '>=', $dates[0])
                ->where('qb_created_at', '<=', $dates[1]);
        }

        return $query;
    }

    //    usage
    // YourController.php

    //    public function search(Request $request)
    //    {
    //        $query = QuickbooksInvoices::query()
    //            ->customerName($request->input('customerName'))
    //            ->refNumber($request->input('refNumber'))
    //            ->invoiceKind($request->input('invoice_kind'))
    //            ->createdAt($request->input('qb_created_at'));
    //
    //        $dataProvider = new ActiveDataProvider([
    //            'query' => $query,
    //        ]);
    //
    //        return $dataProvider;
    //    }

    /**
     * Details we need to get this invoice fiscalised
     *
     * @param int $id
     * @throws Exception
     */
    public static function getFiscalInvoiceAtrributes($id, $inv_kind = 'INVOICE')
    {
        $invoice = self::getInvoiceDetails($id, $inv_kind);
        $qb_record = ($inv_kind == 'INVOICE') ? ($invoice->Invoice) : ($invoice->SalesReceipt);
        $efrisInvoice = (new EfrisInvoiceService())->createEfrisInvoiceQbo($qb_record[0]);

        return is_array($efrisInvoice) ? ($efrisInvoice['data']) : (false);
    }

  /**
   * used for preview invoices
   *
   * @param int $id
   * @throws Exception
   */
  public static function getFiscalInvoiceAtrributesPreview($id, $inv_kind = 'INVOICE')
  {
    $invoice = self::getInvoiceDetails($id, $inv_kind);
    $qb_record = ($inv_kind == 'INVOICE') ? ($invoice->Invoice) : ($invoice->SalesReceipt);
    $efrisInvoice = (new EfrisInvoiceService())->createEfrisInvoiceQbo($qb_record);

    return [
      "data" => is_array($efrisInvoice) ? ($efrisInvoice['data']) : (false),
      "errors"=> is_array($efrisInvoice) ? ($efrisInvoice['errors']) : (false),
      "is_errors" => count($efrisInvoice['errors'])>0
    ];
  }

    /**
     * Details of an Invoice from an ID
     *
     * @param  int  $id
     * @return object
     */
    public static function getInvoiceDetails($id, $inv_kind = 'RECEIPT')
    {
        if ($inv_kind == 'INVOICE') {
            $item = QuickbooksApiClient::getSingleInvoice($id);
        } else {
          $item = QuickbooksApiClient::getSingleReceipts($id);
        }
      return json_decode(json_encode($item), false);
    }

    /**
     * Branch code for the invoice
     *
     * @return string
     */
    public function getInvoiceBranch()
    {
        $str = explode('-', $this->refNumber);

        return (count($str) > 1) ? (substr($str[1], 0, 4)) : ('');
    }

    public static function validInvoices()
    {
        return static::where('validationStatus', 0)->limit(500)->get()->toArray();
    }

    /**
     * @throws Exception
     */
    public static function saveInvoiceSummary($id, $cols, $inv_kind = 'INVOICE')
    {
        // Allow up to 2GB for this action (if necessary)
        ini_set('memory_limit', '2048M');

        // Get validation errors
        $errors = self::getInvoiceValidationErrors($id, $inv_kind);

        $invoice = QuickBooksInvoice::where('id', $id)
            ->where('invoice_kind', $inv_kind)
            ->first();

        if ($invoice) {
            // Update existing invoice
            $invoice->validationError = empty($errors) ? null : implode(",", $errors);
            $invoice->refNumber = $cols['refNumber'];
            $invoice->customerName = $cols['customerName'];
            $invoice->totalAmount = $cols['totalAmount'];
            $invoice->buyerTin = $cols['tin'];
            $invoice->balanceDue = isset($cols['balance']) ? $cols['balance'] : null;

            if ($inv_kind == 'INVOICE') {
                $invoice->dueDate = isset($cols['dueDate']) ? $cols['dueDate'] : null;
                $invoice->purchase_order = $cols['po'];
            }

            $invoice->validationStatus = empty($errors) ? 1 : 0;
            $invoice->update();
        } else {
            // Create a new invoice/receipt
            $invoice = new QuickBooksInvoice;
            $invoice->id = $id;
            $invoice->refNumber = $cols['refNumber'];
            $invoice->customerName = $cols['customerName'];
            $invoice->totalAmount = $cols['totalAmount'];
            $invoice->buyerTin = $cols['tin'];
            $invoice->balanceDue = isset($cols['balance']) ? $cols['balance'] : null;
            $invoice->qb_created_at = now()->format('Y-m-d H:i:s');
            $invoice->buyerType = UtilityFacades::getsettings('buyer_type') ??1;
            $invoice->industryCode = UtilityFacades::getsettings('industry_code')??101;
            $invoice->invoice_kind = $inv_kind;
            $invoice->validationError = empty($errors) ? null : implode(",", $errors);
            $invoice->validationStatus = empty($errors) ? 1 : 0;

            if ($inv_kind == 'INVOICE') {
                $invoice->dueDate = isset($cols['dueDate']) ? $cols['dueDate'] : null;
                $invoice->purchase_order = $cols['po'];
            }

            $invoice->save();
        }

        $success = $invoice->save();
        $msg = $success ? "{$inv_kind} Number {$invoice->refNumber} successfully saved to the local DB" : 'Sorry, we could not save the invoice details';

        // Handle response or logging (consider returning appropriate data)
        return $success ? $msg : redirect()->back()->with('error', $msg); // Example using redirect and flash message
    }



    /**
     * @throws Exception
     */
    public static function getFiscalInvoiceAttributes($id, $kind = 'INVOICE'): array
    {
        $invoice = self::getInvoiceDetails($id, $kind);

        $efrisInvoice = (new EfrisInvoiceService())->createEfrisInvoice($invoice, $kind);

        return [
            'data' => is_array($efrisInvoice) ? ($efrisInvoice['data']) : (false),
            'errors' => is_array($efrisInvoice) ? ($efrisInvoice['errors']) : (false),
            'is_errors' => count($efrisInvoice['errors']) > 0,
        ];
    }

  /**
   * @throws Exception
   */
  public static function getInvoiceValidationErrors($id, $inv_kind = 'INVOICE'): array
    {
        $invoice = QuickBooksInvoice::getInvoiceDetails($id, $inv_kind);
        $efrisInvoice = (new EfrisInvoiceService())->createEfrisInvoice($invoice, $inv_kind);
        return $efrisInvoice['errors'];
    }

    /**
     * Add invoice lines to the invoice object
     *
     */
    public function addInvoiceDiscountLines($inv): array
    {
        $total = array_sum(array_column($inv['itemsBought'], 'total'));
        $discount = $inv['discountTotal'];
        //1. Discount on all items in the invoice
        if ($total > 0) {
            if ($discount > 0) {
                for ($i = 0; $i < count($inv['itemsBought']); $i++) {
                    $inv['itemsBought'][$i]['discountTotal'] = round(($inv['itemsBought'][$i]['total'] * ($discount / $total)), 2);
                    $inv['itemsBought'][$i]['discountFlag'] = 0;
                }
            } else {
                for ($i = 0; $i < count($inv['itemsBought']); $i++) {
                    $inv['itemsBought'][$i]['discountFlag'] = 2;
                }
            }
        }
        //2. Dicount on specifc line items
        for ($i = 0; $i < count($inv['itemsBought']); $i++) {
            if ($inv['itemsBought'][$i]['taxRule'] == 'DISCOUNT') {
                $inv['itemsBought'][$i + 1]['discountFlag'] = 0;
                $inv['itemsBought'][$i + 1]['discountTotal'] = $inv['itemsBought'][$i]['total'];
                //Remove Discount Line
                unset($inv['itemsBought'][$i]);
            }
        }

        //Reindex product Items
        $inv['itemsBought'] = array_values($inv['itemsBought']);

        return $inv;
    }

    public static function batchInsert($records, $qbItems)
    {
        $data = $qbItems->toArray();
      foreach ($records as $record) {
        // Check for key existence directly
        if (isset($record['referenceNo']) && array_key_exists($record['referenceNo'], $data)) {
          $id = Arr::get($data, $record['referenceNo']);
          self::customInsert($record, $id['Id']);
        }
      }
        return true;
    }

    public static function customInsert($fromefris, $qbId): bool|null
    {
        //        //Check if we have record already
      if ($existingInvoice = static::find($qbId)) {
        $existingInvoice->fill([
          'fiscalNumber' => $fromefris['invoiceNo'],
          'refNumber' => $fromefris['referenceNo'],
          'customerName' => $fromefris['buyerLegalName'],
          'totalAmount' => $fromefris['grossAmount'],
          'tax_amount' => $fromefris['taxAmount'],
          'buyerTin' => $fromefris['buyerTin'] ?? null,
          'qb_created_at' => $fromefris['issuedDate'],
          'buyerType' => 1,
          'industryCode' => 101,
          'invoice_kind' => $fromefris['invoiceType'] === 1 ? 'RECEIPT' : 'INVOICE',
          'validationStatus' => 1,
          'validationError'=>null,
          'fiscalStatus' => 1,
        ]);

//        dd($existingInvoice);

        try {
          $existingInvoice->save();
        } catch (QueryException $e) {
          Log::info('There was a problem saving records to the database', ['error' => $e->getMessage()]);
        }
      }
        return true;
    }

    private function getItemDiscountAmount()
    {
        return $this->discountAmount;
    }

}
