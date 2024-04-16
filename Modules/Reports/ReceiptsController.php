<?php

namespace Modules\Reports;

use App\Http\Controllers\Controller;
use App\Models\EfrisInvoiceSearch;
use DateTime;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use QuickBooksOnline\API\Exception\SdkException;

class ReceiptsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     * @throws SdkException
     */

    public function index(Request $request){

        $data = $this->queryReceiptsRange('all');

        // Buyer Types
        $buyerType = [
            1 => 'Consumer',
            0 => 'Government/Business',
            2 => 'Foreigner',
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

        $data = $this->queryReceiptsRange($request,'passed');

        // Buyer Types
        $buyerType = [
            1 => 'Consumer',
            0 => 'Government/Business',
            2 => 'Foreigner',
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
        $data = $this->queryReceiptsRange('failed');
        $tin =53465867;
        // dd($data);
        // $paginatedData = $collection->paginate(100);

        return view('receipts::validationErrors', compact('data','tin'));
    }

    public function failed()
    {
        $data = $this->queryReceiptsRange('failed');
        $tin =53465867;
        // dd($data);
        // $paginatedData = $collection->paginate(100);

        return view('receipts::failed', compact('data','tin'));
    }

    public function fiscalised()
    {
        $data = $this->queryReceiptsRange('ura');
        $tin =53465867;

        return view('invoices::fiscalised', compact('data','tin'));
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

        $invoices = $this->getDataService()->Query('SELECT * FROM SalesReceipt WHERE TxnDate >= \'' . $startdate . '\' AND TxnDate <= \'' . $enddate . '\'', 1, 1000);

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
}
