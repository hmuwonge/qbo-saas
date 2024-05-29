<?php

namespace Modules\Receipts\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EfrisInvoiceSearch;
use App\Services\QBOServices\QboQueryService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Receipts\Http\Services\ReceiptsService;
use QuickBooksOnline\API\Exception\SdkException;

class ReceiptsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     * @throws SdkException
     */

    // Buyer Types
    protected array $buyerType = [
        ''=>'Select buyer type',
        0 => 'Business(B2B)',
        1 => 'Consumer (B2C)',
        2 => 'Foreigner',
        3 => 'Government(B2G)',
    ];

    // Industry Codes
    protected array $industryCode = [
      101 => "General Industry",
      102 => 'Export',
      104 => 'Imported Service',
      105 => 'Telecom',
      106 => 'Stamp Duty',
      107 => 'Hotel Service',
      108 => 'Other Taxes',
    ];

    public function index(Request $request){

        $data = $this->queryReceipts('all');

        // Buyer Types
        $buyerType = [
            ''=>'Select buyer type',
            0 => 'Business(B2B)',
            1 => 'Consumer (B2C)',
            2 => 'Foreigner',
            3 => 'Government(B2G)',
        ];

        // Industry Codes
        $industryCode = [
            101 => 'General Industry',
            102 => 'Export',
            104 => 'Imported Service',
            105 => 'Telecom',
            106 => 'Stamp Duty',
            107 => 'Hotel Service',
            108 => 'Other Taxes',
        ];

        $tin=43645765;

        return view('receipts::index',compact('data','buyerType','industryCode','tin'));

    }

    public function passedValidations(Request $request){

        $data = $this->queryReceipts('passed');

        // Buyer Types
        $buyerType = [
            ''=>'Select buyer type',
            0 => 'Business(B2B)',
            1 => 'Consumer (B2C)',
            2 => 'Foreigner',
            3 => 'Government(B2G)',
        ];

        // Industry Codes
        $industryCode = [
            101 => 'General Industry',
            102 => 'Export',
            104 => 'Imported Service',
            105 => 'Telecom',
            106 => 'Stamp Duty',
            107 => 'Hotel Service',
            108 => 'Other Taxes',
        ];

        $tin=43645765;

        return view('receipts::passed',compact('data','buyerType','industryCode','tin'));

    }

    /**
     * Return invoices with issues
     *
     */
    public function errors()
    {
        $data = $this->queryReceipts('failed');
        $tin =53465867;

        return view('receipts::validationErrors', compact('data','tin'));
    }

    public function failed()
    {
        $data = $this->queryReceipts('failed');
        $tin =53465867;

        return view('receipts::failed', compact('data','tin'));
    }

    public function fiscalised()
    {
        $data = $this->queryReceipts('ura');
        $tin =53465867;

        return view('receipts::fiscalised', compact('data','tin'));
    }

    public function queryReceipts($list): array
    {
        return QboQueryService::queryInvoicesOrReceipts($list,'receipt');
    }

    /**
     * @throws SdkException
     * @throws Exception
     */
    public function queryReceiptsRange($list='all')
    {

        ini_set('memory_limit', '4096M'); //Allow up to 4GB for this action
        // get custom date range
        $period = request()->input('invoice_period');

        // dates
        $dates = explode(' to ', $period);

        $oneMonthAgo = new DateTime('6 month ago');
        $month_ago = $oneMonthAgo->format('Y-m-d');

        $startdate = (isset($period)) ? ($dates[0]) : ($month_ago);
        $enddate = (isset($period)) ? ($dates[1]) : (date('Y-m-d'));

        $invoices = $this->getDataService()->Query('SELECT * FROM SalesReceipt WHERE TxnDate >= \''
          . $startdate . '\' AND TxnDate <= \''
          . $enddate . '\'', 1, 1000);

        $format_receipts = json_decode(json_encode($invoices), true);

        $filteredList = [];

        if ($list == 'passed') {
            $filteredList = EfrisInvoiceSearch::findQbReceiptsByStatus($format_receipts, 1, 0);
        } elseif ($list == 'ura') {
            $filteredList = EfrisInvoiceSearch::findQbReceiptsByStatus($format_receipts, 1, 1);
        } elseif ($list == 'all') {
            $filteredList = EfrisInvoiceSearch::findQbReceiptsByStatus($format_receipts, 2, 2);
        } else {
            $filteredList = EfrisInvoiceSearch::findQbReceiptsByStatus($format_receipts, 0, 0);
        }

        $filt = collect($filteredList)->paginate(12);

        return [
            'filteredList' => $filt,
            'startdate' => $startdate,
            'enddate' => $enddate,
            'date' => $period,
            'total' => count($filt),
            'period' => $period,
        ];
    }

  public function receiptsDateRange($validate = "no", $list = "all")
  {
    $period = request()->input('receipts_period');
    list($startdate, $enddate) = $this->getDateRange($period);
    $invoicePeriod = request()->query('receipts_period', '');

    $queryString = '/query?query=select * from SalesReceipt WHERE TxnDate >= \''
      . Carbon::parse($startdate)->format('Y-m-d') . '\' AND TxnDate <= \''
      . Carbon::parse($enddate)->format('Y-m-d') . '\' maxresults 1000&minorversion=57';
    $quickbooks_receipts = (new self())->queryString($queryString);

    $decoded_receipts = json_decode(json_encode($quickbooks_receipts), true)['QueryResponse']['SalesReceipt'];
    $receipts_to_save = json_decode(json_encode($quickbooks_receipts), false);

    if ($validate == "yes") {
      ReceiptsService::validateReceipts($receipts_to_save);
      return redirect()->route('qbo.receipts.index')->with('success', 'Receipts validation successfully completed');
    } else {
      $filteredList = $this->filterReceipts($decoded_receipts, $list);
      $filt = collect($filteredList)->paginate(12);

      $data = [
        'filteredList' => $filt,
        'startdate' => $startdate,
        'enddate' => $enddate,
        'date' => $period,
        'total' => count($filt),
        'period' => $period,
      ];

      $buyerType = $this->buyerType;
      $industryCode = $this->industryCode;
      $tin = 43645765;

      return view('receipts::receipts-range', compact('data', 'buyerType', 'industryCode', 'tin', 'invoicePeriod'));
    }
  }

  private function getDateRange($period): array
  {
    $dates = explode(' - ', $period);

    $oneMonthAgo = new DateTime('6 month ago');
    $month_ago = $oneMonthAgo->format('Y-m-d');

    $startdate = $dates[0] ?? $month_ago;
    $enddate = $dates[1] ?? date('Y-m-d');

    return [$startdate, $enddate];
  }

  private function filterReceipts($invoices, $list): array
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
