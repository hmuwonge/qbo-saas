<?php

namespace App\Services;

use App\Models\EfrisInvoice;
use App\Models\EfrisItem;
use App\Models\QuickBooksInvoice;
use App\Models\QuickBooksInvoicesDatatable;
use App\Services\QBOServices\QuickbooksApiClient;
use App\Traits\DataServiceConnector;
use Exception;

class EfrisInvoiceService
{
    use DataServiceConnector;

    public array $productsSold = [];

    public int $discountAmount = 0;
    private string|int $tin = '';

    protected array $errorMsg = [];

    protected array $errors = [];
  public array $invoiceLineDiscount = [];

    /**
     * Set product lines, discount lines, subTotals, etc
     */
    public function initInvoiceLines($lines): void
    {
      QuickBooksServiceHelper::logToFile($lines);
        foreach ($lines as $line) {
            // add product lines
            if ($line->DetailType == 'SalesItemLineDetail') {
                $this->productsSold[] = $line;
            }

            // if we have a discount
            if ($line->DetailType == 'DiscountLineDetail') {
//                $this->discountAmount += $line->Amount;
              if(optional($line->DiscountLineDetail)->PercentBased){
                $this->discountAmount += $line->Amount * 1.18;
              }
              else{
                $this->discountAmount += $line->Amount;
              }
            }
        }

    }

    /**
     * @throws Exception
     */
    public function createEfrisInvoice($qbInvData, $inv_kind)
    {
//      dd($qbInvData->Invoice);
      if ($inv_kind === 'INVOICE' && !empty($qbInvData->Invoice)) {
        // Remove the first element if $inv_kind is 'INVOICE'
        $qbInv = $qbInvData->Invoice;
      }

      if ($inv_kind === 'RECEIPT' && !empty($qbInvData->SalesReceipt)) {
        // Remove the first element if $inv_kind is 'INVOICE'
        $qbInv = $qbInvData->SalesReceipt;
      }

        // does this invoice exist?
        if ($qbInv) {
            // DB invcice
            $db_invoice = EfrisInvoice::where('refNumber', $qbInv->DocNumber)->first();

            // Init lines
            $this->initInvoiceLines($qbInv->Line);

            // get buyertin
            $customFields = $qbInv->CustomField;

            //BuyerType
            $buyerTyp = isset($db_invoice->buyerType) ? ($db_invoice->buyerType) : 0;

            //Efris Formarted Invoice Request
            $efrisInvoice = [
                'sellerDetails' => [
                    'placeOfBusiness' => 'Quickbooks test',
                    'referenceNo' => $qbInv->DocNumber,
                ],
                'basicInformation' => [
                    'invoiceNo' => $qbInv->DocNumber,
                    'operator' => auth()->user()->name,
                    'currency' => $qbInv->CurrencyRef->value,
                    'invoiceType' => 1,
                    'invoiceKind' => 1,
                    'invoiceIndustryCode' => 101,
                    //General Industry
                ],
                'buyerDetails' => $this->getCustomerDetails(
                    $qbInv->CustomerRef->value,
                    $buyerTyp,
                    get_tin($customFields)
                ),
                'itemsBought' => $this->prepareInvoiceLines($qbInv->CurrencyRef->value,$db_invoice),
                'remarks' => optional($qbInv->CustomerMemo)->value,
            ];

            return [
                'data' => $efrisInvoice,
                'errors' => $this->getInvoiceValidationMessages($efrisInvoice),
            ];
        } else {
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function createEfrisInvoiceQbo($qbInvData): bool|array
    {
        $qbInv = $qbInvData;
        // does this invoice exist?
        if ($qbInv) {
            // DB invcice
            $db_invoice = EfrisInvoice::where('refNumber', $qbInv->DocNumber)->first();

            // Init lines
            $this->initInvoiceLines($qbInv->Line);
            // get buyertin
            $custpmFields = $qbInv->CustomField;
            //BuyerType
            $buyerType = isset($db_invoice->buyerType) ? ($db_invoice->buyerType) : 0;
            //Efris Formarted Invoice Request
            $efrisInvoice = [
                'sellerDetails' => [
                    'placeOfBusiness' => 'Quickbooks test',
                    'referenceNo' => $qbInv->DocNumber,
                ],
                'basicInformation' => [
                    'invoiceNo' => $qbInv->DocNumber,
                    'operator' => auth()->user()->name,
                    'currency' => $qbInv->CurrencyRef->value,
                    'invoiceType' => 1,
                    'invoiceKind' => 1,
                    'invoiceIndustryCode' => 101,
                    //General Industry
                ],
                'buyerDetails' => $this->getCustomerDetails(
                    $qbInv->CustomerRef->value,
                    $buyerType,
                    get_tin($custpmFields ),
                ),
                'itemsBought' => $this->prepareInvoiceLines($qbInv->CurrencyRef->value,$db_invoice),
                'remarks' => optional($qbInv->CustomerMemo)->value,
            ];

            return [
                'data' =>  $efrisInvoice,//$this->addInvoiceDiscountLines($efrisInvoice),
                'errors' => $this->getInvoiceValidationMessages($efrisInvoice),
            ];
        } else {
            return false;
        }
    }

    public function getCustomerDetails($id, $buyer = 2, $tin = null): array|string
    {
        $cust = null;
        try {
          $cust = QuickbooksApiClient::getSingleCustomerData($id);
//          dd($cust);
            //Customer details for EFRIS
            return [
                'buyerTin' => $tin,
                'buyerNinBrn' => '',
                'buyerPassportNum' => '',
                'buyerLegalName' => $cust['FullyQualifiedName'],
                'buyerBusinessName' => $cust['FullyQualifiedName'],
                'buyerAddress' => $this->getCustomerAddress(@$cust['BillAddr']),
                'buyerEmail' => @$cust['PrimaryEmailAddr']['Address'],
                'buyerMobilePhone' => @$cust['Mobile']['FreeFormNumber'],
                'buyerLinePhone' => @$cust['PrimaryPhone']['FreeFormNumber'],
                'buyerPlaceOfBusi' => '',
                'buyerType' => $buyer,
                'buyerCitizenship' => '',
                'buyerSector' => '',
                'buyerReferenceNo' => $cust['Id'],
            ];
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * Customer Address
     */
    public function getCustomerAddress($address = null)
    {
        $decode_add = json_decode(json_encode($address));
        $customer_address = '';
        if (isset($address)) {
            $customer_address .= property_exists($decode_add, 'Line1') ? ($decode_add->Line1 . ', ') : ('');
            $customer_address .= property_exists($decode_add, 'PostalCode') ? ($decode_add->PostalCode . ', ') : ('');
            $customer_address .= property_exists($decode_add, 'CountrySubDivisionCode') ? (optional($decode_add->CountrySubDivisionCode) . ', ') : ('');
            $customer_address .= property_exists($decode_add, 'City') ? ($decode_add->City . ', ') : ('');
            $customer_address .= property_exists($decode_add, 'Country') ? (optional($decode_add->Country)) : ('');
        }

        return $customer_address;
    }

    public function prepareInvoiceLines($currency,$invoice_data=null): array
    {
        $itemsold = [];
        foreach ($this->productsSold as $item) {
            $is_registered_item_id = '';
            if (property_exists($item->SalesItemLineDetail, 'ItemRef')) {
                $is_registered_item_id = $item->SalesItemLineDetail->ItemRef->value;
            }

            //Find details of this product and we're going to check if it's registered
            $efrisProduct = EfrisItem::where('id', $is_registered_item_id)->first();
            $total = optional($item->SalesItemLineDetail)->TaxInclusiveAmt;
            if ($efrisProduct) {
                //check for deemedflag

              $deemedFlag = 2;

              if (config('quickbooks.taxpayer_config.is_deemed_registered') === '101') {
                $deemedFlag = ($item->SalesItemLineDetail->TaxCodeRef->value ?? '') ==config('quickbooks.taxpayer_config.quickbooks_deemed_taxcoderef') ? 1 : 2;
              }

//                $total = isset($item->SalesItemLineDetail->TaxInclusiveAmt) ? $item->SalesItemLineDetail->TaxInclusiveAmt : 0;

              if (floatval($total) > 0) {
                $quantity = optional($item->SalesItemLineDetail)->Qty;

                $unitPrice = ($quantity == 0) ? (0) : (round(($total / $quantity), 7)); //UnitPrice
                    //$unitPrice = @$item->SalesItemLineDetail->UnitPrice;
                    if ($quantity == 0) {
                        //Record validation error
                        $this->errorMsg[] = 'Invoice has some items whose quantity are not specified';
                    }

                    if ($unitPrice == 0) {
                        $this->errorMsg[] = 'Price cannot be zero for item ' . $efrisProduct->itemCode;
                    }

                    if (is_object($efrisProduct) && ($item->DetailType == 'SalesItemLineDetail')) { //Is this product mapped to URA?

                        // hard code unit of measure for adept
                        $unit_of_measure = $efrisProduct->unitOfMeasure;
                        $item_tax_value = isset($item->SalesItemLineDetail) ? $item->SalesItemLineDetail->TaxCodeRef->value : null;

//                        dd($item_tax_value);
                        //check for exempt tins
                        // dd($item_tax_value);
                        // $standardTaxCodRef = config('quickbooks.taxpayerConfig.standard_taxcodref');

                          // 01:A: Standard (18%)
                          // 02:B: Zero (0%)
                          // 03:C: Exempt (-)
                          // 04:D: Deemed (18%)
                          // 05:E: Excise Duty
                          // 06:Over the Top Service (OTT)
                          // 07:Stamp Duty
                          // 08:Local Hotel Service Tax
                          // 09:UCC Levy
                          // 10:Others
                          // 11:F: VAT Not Applicable

                          // For example, the taxRate is
                          // 18% Fill in: 0.18
                          // For example, the taxRate is
                          // zero Fill in: 0
                          // For example, the taxRate is
                          // deemed Fill in: ‘-’ or ' '
                          // Integer digits cannot exceed
                          // 1, decimal digits cannot
                          // exceed 4;



                        if ($item_tax_value == env('EXEMPT')) {
                            $taxRule = 'EXEMPT';
                        }elseif($item_tax_value === env('DEEMED')) {
                          $taxRule = 'DEEMED';
                        } elseif (isset($invoice_data) &&($invoice_data->buyerType == "1") && ($invoice_data->industryCode == '101')) {
                        $taxRule = 'ZERORATED';
                      }else{
                          $taxRule = 'STANDARD';
                        }

                        $_item = [
                            'itemCode' => $efrisProduct->itemCode,
                            'quantity' => $quantity,
                            'unitPrice' => $unitPrice,
                            'total' => $total,
                            'discountFlag' => 2,
                            'discountTotal' => '',
                            'exciseFlag' => $efrisProduct->haveExciseTax == '102' ? 2 : 1,
                            'exciseUnit' => '',
                            'exciseTax' => '',
                            'deemedFlag' => $deemedFlag,
                            'exciseCurrency' => $currency,
                            'taxRateName' => $taxRule,
                            'taxRule' => $taxRule,
                            'qbLine' => $item->LineNum,
                            'unitOfMeasure' => $unit_of_measure,
                        ];

                        //Is Item Exisable??
                        if ($efrisProduct->haveExciseTax == 101) {
                            $_item['exciseRule'] = 1;
                            $_item['exciseRate'] = $efrisProduct->exciseRate;
                            $_item['exciseTax'] = $this->getItemExciseTaxByPercentage($unitPrice, $quantity, $efrisProduct->exciseRate);
                            $_item['exciseCurrency'] = $currency;
                        }
                    } else {
                        $_item = [
                            'itemCode' => 'NO_CODE',
                            //NOT MAPPED WITH URA
                            'quantity' => $quantity,
                            'unitPrice' => $unitPrice,
                            'total' => $total,
                            'discountFlag' => '',
                            'discountTotal' => '',
                            'exciseFlag' => '',
                            'exciseUnit' => '',
                            'exciseTax' => '',
                            'exciseCurrency' => $currency,
                        ];
                        //Add Error message
                        $this->errorMsg[] = $item->SalesItemLineDetail->ItemRef->name . ' is not registered with URA. Please register this item to generate a Fiscal Invoice';
                    }

                    //Add this item to the list
                    $itemsold[] = $_item;
                } else {
                    //return some error message about taxinclusive
                    $this->errorMsg[] = 'Please set amount to tax inclusive for ' . $item->SalesItemLineDetail->ItemRef->name;
                    $itemsold[] = [
                        'qbLine' => $item->LineNum,
                        'total' => $total,
                        'taxRule' => 'URA', //'DISCOUNT'
                    ];
                }
            } else { //Add Error message
//                if (is_null($total)) {
//                    if (property_exists($item->SalesItemLineDetail, 'ItemRef')) {
//                        $this->errorMsg[] = 'Please set amount to tax inclusive for ' . $item->SalesItemLineDetail->ItemRef->name;
//                    }
//
//                } else {
//                    if (property_exists($item->SalesItemLineDetail, 'ItemRef')) {
//                        $this->errorMsg[] = $item->SalesItemLineDetail->ItemRef->name . ' is not registered with URA. Please register this item to generate a Fiscal Invoice';
//                    }
//
//                }
//                if (property_exists($item->SalesItemLineDetail, 'ItemRef')) {
                    $this->errorMsg[] = $item->SalesItemLineDetail->ItemRef->name . ' is not registered with URA. Please register this item to generate a Fiscal Invoice';
//                }
            }
        }

        return $itemsold;
    }

    /**
     * Check if this invoice can be fiscalised by the EFRIS platform
     * and return Validation messages
     */
    public function getInvoiceValidationMessages($data): array
    {
        // dd($data);
        if (!isset($data['buyerDetails']['buyerType'])) {
            $this->errorMsg[] = "The customer 'BuyerType' is not specified";
        }

        if (is_null($data['buyerDetails']['buyerLegalName'])) {
            $this->errorMsg[] = 'The customer name is not supplied';
        }

        //If we are dealing with B2G or B2C but the TIN is not specified...
        if ($data['buyerDetails']['buyerType'] == 0 && strlen($data['buyerDetails']['buyerTin']) != 10) {
            $this->errorMsg[] = 'The customer TIN is not valid. TIN should be 10 character digits';
        }

        if ($data['buyerDetails']['buyerType'] == 0 && empty($data['buyerDetails']['buyerTin'])) {
            $this->errorMsg[] = 'The customer TIN is not specified';
        }

        if ($data['buyerDetails']['buyerType'] == 0 && is_null($data['buyerDetails']['buyerLegalName']) ) {
            $this->errorMsg[] = 'The buyerLegalName is not specified';
        }

        return $this->errorMsg;
    }

    /**
     * Calculate the excise Tax by Percentage
     *
     * @param  float  $exciseRate The excise Tax rate, e.g. 0.12
     */
    public function getItemExciseTaxByPercentage(float $unitPrice, float $qty, float $exciseRate): float
    {
        $vat = $unitPrice - ($unitPrice / 1.18);
        $netRateWithVat = ($unitPrice / $vat) / (1 + $exciseRate) + $vat;

        return $unitPrice - $netRateWithVat;
    }

    public static function findQbReceiptsByStatus($invoices, $status = 1, $fiscalised = 1)
    {
        $data = json_decode($invoices, true);
        $list = $data['QueryResponse']['SalesReceipt'];

        if ($list) {
            if ($status == 2 && $fiscalised == 2) {
                $invoiceStatus = QuickBooksInvoice::where('invoice_kind', 'RECEIPT')->get()->keyBy('id');
                $indexed = collect($list)->keyBy('Id');

                return self::prepareInvoiceList($invoiceStatus, (array)$indexed);
            }

            $invoiceStatus = QuickBooksInvoice::where([
                'invoice_kind' => 'RECEIPT',
                'validationStatus' => $status,
                'fiscalStatus' => $fiscalised,
            ])->get()->keyBy('id');

            $list_ids = $invoiceStatus->pluck('id')->toArray();
            $indexed = collect($list)->keyBy('Id');
            $filteredList = $indexed->filter(function ($item, $key) use ($list_ids) {
                return in_array($key, $list_ids);
            });

            return self::prepareInvoiceList($invoiceStatus, $filteredList);
        }

        return [];
    }

    /**
     * Details of an Invoice from an ID
     *
     * @param  int  $id
     * @return false|string
     */
    public function getInvoiceDetails($id, $inv_kind = 'INVOICE')
    {
        if ($inv_kind == 'INVOICE') {
            $item = QuickbooksApiClient::getSingleInvoice($id); //$this->urlQueryBuilderById('invoice', $id);

        } else {
            $item = $this->urlQueryBuilderById('salesreceipt', $id); // tested and passed
        }

        if ($item) {
            if (array_key_exists('Fault', $item->original)) {
                return $item->original['Fault']['Error'][0];
            } elseif (array_key_exists('Invoice', $item->original)) {
                $item = $item->original;
            }
        }

        //Invoice details
        return json_decode(json_encode($item));
    }

    /**
     * Prepare a list of invoices for display using DataTables
     */
    protected static function prepareInvoiceList($dbInvoices, array $qbInvoices): QuickBooksInvoice|array
    {
        $invoices = [];
//        foreach ($qbInvoices as $inv) {
//            $_id = $inv['Id'];
//            $dbRec = @$dbInvoices[$_id];
//            $invoices[] = new QuickBooksInvoicesDatatable($dbRec, $inv);
//        }
      foreach ($qbInvoices as $qbInvoice) {
        // Extract invoice ID with clear naming
        $qbInvoiceId = $qbInvoice['Id'];

        // Check for existing record with proper error handling (assuming exception)
        $dbRecord = null;
        if (isset($dbInvoices[$qbInvoiceId])) {
          $dbRecord = $dbInvoices[$qbInvoiceId];
        } else {
          // Handle missing record (e.g., log or throw exception)
          // You can replace this with your desired behavior
          // echo "Invoice with ID $qbInvoiceId not found in database";
        }

        // Create new object with descriptive variable names
        $invoices[] = new QuickBooksInvoicesDatatable($dbRecord, $qbInvoice);
      }

        return $invoices;
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
          $inv['itemsBought'][$i]['discountTotal'] =
            round(($inv['itemsBought'][$i]['total'] * ($discount / $total)), 2);
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
}
