<?php

namespace Modules\Receipts\Http\Services;

use App\Models\EfrisInvoiceSearch;
use App\Models\QuickBooksInvoice;
use App\Traits\DataServiceConnector;
use DateTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ReceiptsService
{
    use DataServiceConnector;
  use DataServiceConnector;

    public static function validateReceipts($data): RedirectResponse
    {
        ini_set('memory_limit', '2048M'); //Allow up to 2GB for this action
        if (!is_null($data)) {
            //Save Validation Errors to the DB

          foreach ($data->QueryResponse->SalesReceipt as $inv) {

            $customfields = $inv->CustomField;
            $invoiceCols['refNumber'] = $inv->DocNumber;
            $invoiceCols['qb_created_at'] = $inv->MetaData->CreateTime;
            $invoiceCols['customerName'] = $inv->CustomerRef->name;
            $invoiceCols['totalAmount'] = $inv->TotalAmt;
            $invoiceCols['balance'] = $inv->Balance;
            $invoiceCols['tin'] = get_tin($customfields);

            QuickBooksInvoice::saveInvoiceSummary2(intval($inv->Id), $invoiceCols, 'RECEIPT');
          }

            return redirect()->back()->with('success', 'Receipts validation tests successfully completed');
        } else {
            return redirect()->back()->with('fail', 'We did not find any Receipts from your quickbooks account');
        }
    }

  public function errors()
  {
    // Set memory limit to 2GB for this action
    ini_set('memory_limit', '2048M');

    // Get invoice period and page from request
    $period = request()->input('invoice_period');
    $page = request()->input('page', 1);

    // Split period string into start and end dates
    $dates = explode(' to ', $period);

    // Calculate one month ago
    $oneMonthAgo = new DateTime('6 month ago');
    $monthAgo = $oneMonthAgo->format('Y-m-d');

    // Set start and end dates based on period or default to one month ago
    $startdate = (isset($period)) ? $dates[0] : $monthAgo;
    $enddate = (isset($period)) ? $dates[1] : date('Y-m-d');

    // Calculate start position for pagination
    $startPosition = (intval($page) - 1) * 100;

    // Get total invoice count
    $totalRecords = $this->getDataService()->Query("SELECT COUNT(*) FROM Invoice");

    // Query invoices for the specified period
    $invoiceQuery = $this->getDataService()->Query(
      'SELECT * FROM Invoice WHERE TxnDate >= \'' . $startdate . '\' AND TxnDate <= \'' . $enddate . '\'',
      $startPosition,
      100
    );

    // Create a paginator instance
    $paginator = new LengthAwarePaginator($invoiceQuery, $totalRecords, 100);
    $paginator->setPath(route('qbo.invoices.all')); // url('/paginated-data')

    // Fetch invoice data from paginator
    $invoicesQbo = $paginator->items();

    // Convert invoicesQbo to an array
    $invoices = json_decode(json_encode($invoicesQbo), true);

    // Filter invoices by status
    $filteredList = [EfrisInvoiceSearch::findQbInvoicesByStatus($invoices, 0, 0)];

    // Return response data
    return [
      'filteredList' => $filteredList[0],
      'startdate' => $startdate,
      'enddate' => $enddate,
      'date' => $period,
      'period' => $period,
      'links' => $paginator->links(),
      'total' => $totalRecords
    ];
  }

}
