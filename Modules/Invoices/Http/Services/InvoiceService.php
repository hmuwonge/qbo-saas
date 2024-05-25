<?php

namespace Modules\Invoices\Http\Services;

use App\Models\EfrisInvoiceSearch;
use App\Models\QuickBooksInvoice;
use App\Traits\DataServiceConnector;
use Carbon\Carbon;
use DateTime;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceService
{
    use DataServiceConnector;
  use DataServiceConnector;

    /**
     * @throws \Exception
     */
    public static function validateInvoices($data)
    {
        //        try {
        ini_set('memory_limit', '2048M'); //Allow up to 2GB for this action

        if (!is_null($data)) {
            //Save Validation Errors to the DB
            foreach ($data->QueryResponse->Invoice as $inv) {
                $customfields = $inv->CustomField;
                $invoiceCols['refNumber'] = $inv->DocNumber;
                $invoiceCols['qb_created_at'] = $inv->MetaData->CreateTime;
                $invoiceCols['customerName'] = $inv->CustomerRef->name;
                $invoiceCols['totalAmount'] = $inv->TotalAmt;
                $invoiceCols['tin'] = get_tin($customfields);
                $invoiceCols['po'] = @$customfields[1]->StringValue;
                $invoiceCols['dueDate'] = $inv->DueDate;
                $invoiceCols['balance'] = $inv->Balance;

                QuickBooksInvoice::saveInvoiceSummary(intval($inv->Id), $invoiceCols);
            }

            return redirect()->back()->with('success', 'Invoice validation tests successfully completed');
        } else {
            return redirect()->back()->with('fail', 'We did not find any invoices from your quickbooks account');
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
    $paginator = new LengthAwarePaginator($invoiceQuery, (int)$totalRecords, 100);
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

  public function getQuickbooksInvoices($routeName, $period, $page,$list) {
    ini_set('memory_limit', '2048M');

    $dates = explode(' to ', $period);

    $oneMonthAgo = new DateTime('6 month ago');
    $month_ago = $oneMonthAgo->format('Y-m-d');

    $startdate = (isset($period)) ? ($dates[0]) : ($month_ago);
    $enddate = (isset($period)) ? ($dates[1]) : (date('Y-m-d'));

    $startPosition = intval($page - 1) * 10;
    $countQuery = '/query?query=SELECT count(*) from Invoice  WHERE TxnDate >= \'' . Carbon::parse($startdate)->format('Y-m-d')
      . '\' AND TxnDate <= \'' . Carbon::parse($enddate)->format('Y-m-d')
      . '\' ';
    $quickbooks_invoices_count = $this->queryString($countQuery);
    $totalRecords = $quickbooks_invoices_count['QueryResponse']['totalCount'];


    $queryString = '/query?query=select * from Invoice  WHERE TxnDate >= \'' . Carbon::parse($startdate)->format('Y-m-d')
      . '\' AND TxnDate <= \'' . Carbon::parse($enddate)->format('Y-m-d')
      . '\' startposition'.' '.$startPosition.' maxresults 10';

    $quickbooks_invoices = $this->queryString($queryString);
    $invoices = json_decode(json_encode($quickbooks_invoices), true)['QueryResponse']['Invoice'] ?? [];

    $paginator = new LengthAwarePaginator($invoices, (int) $totalRecords, 10);
    $paginator->setPath(route($routeName)); // url('/paginated-data')
    $invoicesQbo = $paginator->items();
    $invoices= json_decode(json_encode($invoicesQbo), true);
    $filteredList = [];
    switch ($list) {
      case 'failed':
      default:
        $filteredList[] = EfrisInvoiceSearch::findQbInvoicesByStatus($invoices, 0, 0);
        break;

      case 'passed':
        $filteredList[] = EfrisInvoiceSearch::findQbInvoicesByStatus($invoices, 1, 0);
        break;

      case 'ura':
        $filteredList[] = EfrisInvoiceSearch::findQbInvoicesByStatus($invoices, 1, 1);
        break;

      case 'all':
        $filteredList[] = EfrisInvoiceSearch::findQbInvoicesByStatus($invoices, 2, 2);
        break;
    }

    $filt = $filteredList[0];
    return [
      'filteredList' => $filt,
      'startdate' => $startdate,
      'enddate' => $enddate,
      'date' => $period,
      'period' => $period,
      'links' =>  $paginator->links(),
      'total' => $totalRecords
    ];
}

  private function filterInvoices($invoices, $list)
  {
    switch ($list) {
      case 'failed':
      default:
        return EfrisInvoiceSearch::findQbInvoicesByStatus($invoices, 0, 0);

      case 'passed':
        return EfrisInvoiceSearch::findQbInvoicesByStatus($invoices, 1, 0);

      case 'ura':
        return EfrisInvoiceSearch::findQbInvoicesByStatus($invoices, 1, 1);

      case 'all':
        return EfrisInvoiceSearch::findQbInvoicesByStatus($invoices, 2, 2);
    }
  }

}
