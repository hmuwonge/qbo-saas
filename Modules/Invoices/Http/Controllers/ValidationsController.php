<?php

namespace Modules\Invoices\Http\Controllers;

use App\Models\QuickBooksPurchase;
use App\Services\QBOServices\QuickbooksApiClient;
use DateTime;
use App\Models\EfrisInvoice;
use App\Models\QuickBooksInvoice;
use App\Http\Controllers\Controller;
use App\Traits\DataServiceConnector;
use App\Services\QuickBooksServiceHelper;
use Exception;
use Illuminate\Http\RedirectResponse;

class ValidationsController extends Controller
{
    use DataServiceConnector;

  /**
   * @throws Exception
   */
  public function validateInvoices(): RedirectResponse
  {
        ini_set('memory_limit', '2048M'); //Allow up to 2GB for this action

        $all_invoice = QuickbooksApiClient::queryInvoiceData1000();

      // Convert each sub-array to an object
      $invoices = array_map(function($invData) {
        return json_decode(json_encode($invData), false);
      }, $all_invoice);

        if (!is_null($invoices)) {
            //Save Validation Errors to the DB
                foreach ($invoices as $inv) {
                    $customfields = $inv->CustomField;
                    $invoiceCols['refNumber'] = $inv->DocNumber;
                    $invoiceCols['qb_created_at'] = $inv->MetaData->CreateTime;
                    $invoiceCols['customerName'] = $inv->CustomerRef->name;
                    $invoiceCols['totalAmount'] = $inv->TotalAmt;
                    $invoiceCols['tin'] =get_tin($customfields);
                    $invoiceCols['po'] = @$customfields[1]->StringValue;
                    $invoiceCols['dueDate'] = $inv->DueDate;
                    $invoiceCols['balance'] = $inv->Balance;

                    QuickBooksInvoice::saveInvoiceSummary(intval($inv->Id), $invoiceCols);
                }
            return redirect()->back()->with('success', 'Invoice validation tests successfully completed');
        } else {
            return redirect()->back()->with('failed', 'We did not find any invoices from your quickbooks account');
        }
    }

  /**
   * @throws Exception
   */
  public function syncPurchaseBills(): RedirectResponse
  {
    $all_purchases = QuickbooksApiClient::queryPurchasesData1000();
    $purchases_data = json_decode(json_encode($all_purchases),false);

    foreach ($purchases_data as $ph) {
      // Do we have this record?
      if (!QuickBooksPurchase::where('id', $ph->Id)->exists()) {
        $purch = new QuickBooksPurchase;
        $purch->id = $ph->Id;
        $purch->uraSyncStatus = 0;
        $purch->save();
      }
    }

    return redirect()->back()->with('success', 'Purchase validation tests successfully completed');
  }

    public function validateInvoicesWithDatePeriod()
    {

            //    try {
        ini_set('memory_limit', '2048M'); //Allow up to 2GB for this action

        $period = request()->input('invoice_period');

        $dates = explode(' to ', $period);

        $oneMonthAgo = new DateTime('1 month ago');
        $month_ago = $oneMonthAgo->format('Y-m-d');

        $startdate = (isset($period)) ? ($dates[0]) : ($month_ago);
        $enddate = (isset($period)) ? ($dates[1]) : (date('Y-m-d'));
        $queryString = '/query?query=select * from Invoice WHERE TxnDate >= \'' . $startdate . '\' AND TxnDate <= \'' . $enddate . '\' maxresults 1000&minorversion=57';
        $decreaseResponse = (new self())->queryString($queryString);

        $invoices = json_decode(json_encode($decreaseResponse), false);

//        $tin = \auth()->user()->company->tin;
        if (!is_null($invoices)) {
            //Save Validation Errors to the DB
            try {
            foreach ($invoices->QueryResponse->Invoice as $inv) {
                QuickBooksServiceHelper::logToFile($inv);
                $customfields = $inv->CustomField;
                $invoiceCols['refNumber'] = $inv->DocNumber;
                $invoiceCols['qb_created_at'] = $inv->MetaData->CreateTime;
                $invoiceCols['customerName'] = $inv->CustomerRef->name;
                $invoiceCols['totalAmount'] = $inv->TotalAmt;
                //TIN
                //                dd(@$customfields[1]['StringValue']);
                // dd(@$customfields[0]->StringValue);
                // $invoiceCols['tin'] = match ($tin) {
                //     1007473185, => @$customfields[1]->StringValue,
                //     1000293121 => @$customfields[2]->StringValue,
                //     default => @$customfields[0]->StringValue,
                // };
                $invoiceCols['tin'] =@$customfields[0]->StringValue;

                $invoiceCols['po'] = @$customfields[1]->StringValue;
                $invoiceCols['dueDate'] = $inv->DueDate;
                $invoiceCols['balance'] = $inv->Balance;


                QuickBooksInvoice::saveInvoiceSummary(intval($inv->Id), $invoiceCols);

                // continue;
                //Skip and go to next record even if there was an error
            }
            } catch (\Throwable $th) {
                return $th->getMessage();
            }

            return response()->json([
                'status' => 'SUCCESS',
                'msg' => 'Invoice validation tests successfully completed',
            ], 200);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'msg' => 'We did not find any invoices from your quickbooks account',
            ], 200);
        }
    }

    /**
     *  For validationg credit memos
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateCreditMemos()
    {
        ini_set('memory_limit', '4096M'); //Allow up to 2GB for this action
        $credit_notes = $this->urlQueryBuilderAll('CreditMemo');
        $cust = $credit_notes['QueryResponse']['CreditMemo'];

        $all_credits = json_decode((json_encode($cust)));
        if (count($all_credits) > 0) { //if we have some records, this has been tested and working fine
            // Save Validation Errors to the DB
            foreach ($all_credits as $inv) {
                EfrisInvoice::saveInvoiceSummary(intval($inv->Id), $inv);
            }

            return response()->json(['success' => 'successfully validated credit notes data from quickbooks']);
        } else {
            return response()->json(['warning' => 'There are no Credit Memos in your QuickBooks account at the moment']);
        }
    }

    /*
     * For validating receipts data
     */
  /**
   * @throws Exception
   */
  public function validateReceipts()
    {
        //        try {
        ini_set('memory_limit', '2048M'); //Allow up to 2GB for this action

        $qbo_receipts = QuickbooksApiClient::queryReceiptsData1000(); //$this->urlQueryBuilderAll('salesreceipt');

        $invoices = json_decode(json_encode($qbo_receipts), false);
        if (!is_null($invoices)) {
            //Save Validation Errors to the DB
            //            try {
            foreach ($invoices as $receipt) {
                $customfields = $receipt->CustomField;
                $invoiceCols['refNumber'] = $receipt->DocNumber;
                $invoiceCols['qb_created_at'] = $receipt->MetaData->CreateTime;
                $invoiceCols['customerName'] = $receipt->CustomerRef->name;
                $invoiceCols['totalAmount'] = $receipt->TotalAmt;
                $invoiceCols['balance'] = $receipt->Balance;
                $invoiceCols['tin'] =get_tin($customfields);

                QuickBooksInvoice::saveInvoiceSummary(intval($receipt->Id), $invoiceCols, 'RECEIPT');
            }
            //            } catch (\Throwable $th) {
            //                return $th->getMessage();
            //            }

            return redirect()->back()->with('success','Receipts validation tests successfully completed');
//            return response()->json([
//                'status' => 'SUCCESS',
//                'msg' => 'Receipts validation tests successfully completed',
//            ], 200);
        } else {
            return redirect()->back()->with('success','We did not find any Receipts from your quickbooks account');
//            return response()->json([
//                'status' => 'FAIL',
//                'msg' => 'We did not find any invoices from your quickbooks account',
//            ], 200);
        }
    }

    public function validateReceiptsWithDatePeriod()
    {
        //        try {
        ini_set('memory_limit', '2048M'); //Allow up to 2GB for this action

        $period = request()->input('invoice_period');

        $dates = explode(' to ', $period);

        $oneMonthAgo = new DateTime('1 month ago');
        $month_ago = $oneMonthAgo->format('Y-m-d');

        $startdate = (isset($period)) ? ($dates[0]) : ($month_ago);
        $enddate = (isset($period)) ? ($dates[1]) : (date('Y-m-d'));
        $queryString = '/query?query=select * from SalesReceipt WHERE TxnDate >= \'' . $startdate . '\' AND TxnDate <= \'' . $enddate . '\' maxresults 1000&minorversion=57';
        $decreaseResponse = (new self())->queryString($queryString);

        $invoices = json_decode(json_encode($decreaseResponse), false);

        // dd($decreaseResponse);

//        $tin = \auth()->user()->company->tin;
        // $all_invoice = $this->urlQueryBuilderAll('salesreceipt');

        // $invoices = json_decode(json_encode($all_invoice), false);
        if (!is_null($invoices)) {
            //Save Validation Errors to the DB
            //            try {
            foreach ($invoices->QueryResponse->SalesReceipt as $inv) {

                $customfields = $inv->CustomField;
                $invoiceCols['refNumber'] = $inv->DocNumber;
                $invoiceCols['qb_created_at'] = $inv->MetaData->CreateTime;
                $invoiceCols['customerName'] = $inv->CustomerRef->name;
                $invoiceCols['totalAmount'] = $inv->TotalAmt;
                $invoiceCols['balance'] = $inv->Balance;
                // $invoiceCols['tin'] = match ($tin) {
                //     1007473185, => @$customfields[1]->StringValue,
                //     1000293121 => @$customfields[2]->StringValue,
                //     default => @$customfields[0]->StringValue,
                // };
                $invoiceCols['tin'] =@$customfields[0]->StringValue;
                QuickBooksInvoice::saveInvoiceSummary2(intval($inv->Id), $invoiceCols, 'RECEIPT');
            }
            //            } catch (\Throwable $th) {
            //                return $th->getMessage();
            //            }

            return response()->json([
                'status' => 'SUCCESS',
                'msg' => 'Receipts validation tests successfully completed',
            ], 200);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'msg' => 'We did not find any invoices from your quickbooks account',
            ], 200);
        }
    }
}
