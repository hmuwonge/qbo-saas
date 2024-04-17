<?php

namespace Modules\Invoices\Http\Controllers;

use App\Facades\Utility;
use DateTime;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\QuickBooksInvoice;
use Illuminate\Http\JsonResponse;
use App\Models\EfrisInvoiceSearch;
use App\Services\ApiRequestHelper;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Services\QuickBooksServiceHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Invoices\Http\Services\InvoiceService;
use Modules\Receipts\Http\Services\ReceiptsService;

class InvoicesController extends Controller
{
  // Buyer Types
  protected $buyerType = [
    1 => 'Consumer',
    0 => 'Government/Business',
    2 => 'Foreigner',
  ];

  // Industry Codes
  protected $industryCode = [
    101 => 'General Industry',
    102 => 'Export',
    104 => 'Imported Service',
    105 => 'Telecom',
    106 => 'Stamp Duty',
    107 => 'Hotel Service',
    108 => 'Other Taxes',
  ];

  /**
   * Display a listing of the resource.
   * @return Renderable
   */
  public function index()
  {

    $data = $this->queryInvoices('all');
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

    // dd('invoices list');
    $period = request()->input('invoice_period');
    $page = request()->input('page', 1);
    $tin = 53465867;

    //    $data = (new InvoiceService())->getQuickbooksInvoices('qbo.invoices.all',$period,$page,'all');

    return view('invoices::index', compact('data', 'buyerType', 'industryCode', 'tin'));
  }

  public function passedValidations(Request $request)
  {
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
    $filteredList = [];
    $totalRecords = $this->getDataService()->Query("SELECT * FROM Invoice");
    $invoices = json_decode(json_encode($totalRecords), true);
    $filteredList[] = EfrisInvoiceSearch::findQbInvoicesByStatus($invoices, 1, 0);
    $data = collect($filteredList[0])->paginate(10);
    $tin = 43645765;

    return view('invoices::passed', compact('data', 'buyerType', 'industryCode', 'tin'));
  }

  /**
   * Return invoices with issues
   *
   */
  public function errors(InvoiceService $invoiceService)
  {
    $period = request()->input('invoice_period');
    $page = request()->input('page', 1);
    $tin = 53465867;
    $data = (new InvoiceService())->getQuickbooksInvoices('qbo.invoices.errors', $period, $page, 'failed');

    return view('invoices::validationErrors', compact('data', 'tin'));
  }

  public function failed()
  {
    $period = request()->input('invoice_period');
    $page = request()->input('page', 1);
    $tin = 53465867;

    $data = (new InvoiceService())->getQuickbooksInvoices('qbo.invoices.failed', $period, $page, 'failed');

    return view('invoices::failed', compact('data', 'tin'));
  }

  public function fiscalised()
  {
    ini_set('memory_limit', '2048M'); //Allow up to 2GB for this action

    $period = request()->input('invoice_period');
    $page = request()->input('page', 1);

    $data = (new InvoiceService())->getQuickbooksInvoices('qbo.invoices.ura', $period, $page, 'ura');

    $tin = 53465867;

    return view('invoices::fiscalised', compact('data', 'tin'));
  }

  public function queryInvoices($list): array
  {
    ini_set('memory_limit', '2048M'); //Allow up to 2GB for this action

    $period = request()->input('invoice_period');
    $page = request()->input('page', 1);

    $dates = explode(' to ', $period);

    $oneMonthAgo = new DateTime('1 year ago');
    $month_ago = $oneMonthAgo->format('Y-m-d');

    $startdate = (isset($period)) ? ($dates[0]) : ($month_ago);
    $enddate = (isset($period)) ? ($dates[1]) : (date('Y-m-d'));

    $startPosition = intval($page - 1) * 10;
    $countQuery =  "SELECT count(*) FROM Invoice";

    $quickbooks_invoices_count = $this->postQuery($countQuery);

    $totalRecords = $quickbooks_invoices_count['QueryResponse']['totalCount'];

    $query = 'select * from Invoice  WHERE TxnDate >= \'' . Carbon::parse($startdate)->format('Y-m-d')
      . '\' AND TxnDate <= \'' . Carbon::parse($enddate)->format('Y-m-d')
      . '\' startposition' . ' ' . $startPosition . ' maxresults 10';

//    $queryString = '/query?query='.$query ;
    $quickbooks_invoices =$this->postQuery($query);  //(new self())->queryString($query);

    //check if we have a search query
    if (request()->has('q')){
      $new_query = request()->input('q');
      $query = "SELECT * FROM Invoice WHERE DocNumber LIKE '%" . $new_query . "%'";

//      $queryString = '/query?query='.$query ;
      $quickbooks_invoices = $this->postQuery($query);// (new self())->queryString($query);

      $totalRecords = $quickbooks_invoices['QueryResponse']['totalCount'];
    }

//    dd($quickbooks_invoices['QueryResponse']);

    $invoices = $quickbooks_invoices['QueryResponse']['Invoice'] ?? [];

    $paginator = new LengthAwarePaginator($invoices, (int)$totalRecords,  10);
    $paginator->setPath(route('qbo.invoices.all'));
    $invoicesQbo = $paginator->items();
    $invoices_decode = json_decode(json_encode($invoicesQbo), true);

    $filteredList = [];

    $filteredList[] = EfrisInvoiceSearch::findQbInvoicesByStatus($invoices_decode, 2, 2);

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

  public function invoicesRange($validate = "no", $list = "all")
  {
    $period = request()->input('invoice_period');
    list($startdate, $enddate) = $this->getDateRange($period);
    $invoicePeriod = request()->query('invoice_period', '');

    $queryString = 'SELECT * FROM Invoice WHERE TxnDate >= \'' . Carbon::parse($startdate)->format('Y-m-d')
      . '\' AND TxnDate <= \'' . Carbon::parse($enddate)->format('Y-m-d') . '\' maxresults 1000';
    $quickbooks_invoices =$this->postQuery($queryString); // (new self())->queryString($queryString);


    $invoices = $quickbooks_invoices['QueryResponse']['Invoice'] ?? [];

    $invoices_to_save = json_decode(json_encode($quickbooks_invoices), false);

    if ($validate == "yes") {
      InvoiceService::validateInvoices($invoices_to_save);
      return redirect()->route('qbo.invoices.all')->with('success', 'Invoice validation tests successfully completed');
    } else {
      $filteredList = $this->filterInvoices($invoices, $list);
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

      return view('invoices::invoice-range', compact('data', 'buyerType', 'industryCode', 'tin', 'invoicePeriod'));
    }
  }

  private function getDateRange($period)
  {
    $dates = explode(' - ', $period);

    $oneMonthAgo = new DateTime('6 month ago');
    $month_ago = $oneMonthAgo->format('Y-m-d');

    $startdate = $dates[0] ?? $month_ago;
    $enddate = $dates[1] ?? date('Y-m-d');

    return [$startdate, $enddate];
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

  /**
   * Show the form for creating a new resource.
   * @return Renderable
   */
  public function create()
  {
    return view('invoices::create');
  }



  public function updateInvoiceIndustry(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'industryCode' => 'required|integer',
    ]);

    if ($validator->fails()) {
      $errors = $validator->errors();

      return response()->json(['status' => 'FAIL', 'payload' => $errors->all()]);
    }
    try {
      QuickBooksInvoice::whereIn('id', $request->invoiceIds)->update([
        'industryCode' => $request->industryCode,
      ]);

      return response()->json(['status' => true, 'payload' => 'Industry Code successfully updated']);
    } catch (Exception $exception) {
      return $exception->getMessage();
    }
  }

  /**
   * function to update invoice buyer type
   */
  public function updateBuyerType(Request $request): JsonResponse|string
  {
    $validator = Validator::make($request->all(), [
      'buyerType' => 'required|integer',
      'invoiceIds' => 'required|Array',
    ]);

    if ($validator->fails()) {
      $errors = $validator->errors();
      return response()->json(['status' => 'FAIL', 'payload' => $errors->all()]);
    }
    try {
      QuickBooksInvoice::whereIn('id', $request->invoiceIds)->update([
        'buyerType' => $request->buyerType,
      ]);

      return response()->json(['status' => true, 'payload' => 'Buyer Type successfully updated']);
    } catch (Exception $exception) {
      return $exception->getMessage();
    }
  }

  /**
   *  For previewing quickbooks efris invoices
   * @throws Exception
   */
  public function actionInvoicePreview($id, $kind = 'INVOICE'): Response|RedirectResponse
  {
    $efris = new ApiRequestHelper('efris1');
    $efris_invoice = QuickBooksInvoice::getFiscalInvoiceAtrributesPreview($id, $kind);

    if ($efris_invoice['is_errors']) {
      $error = "";
      $itemcount = 0;
      foreach ($efris_invoice['errors'] as $error2) {
        $itemcount = $itemcount + 1;
        $error .= $itemcount . ". " . $error2;
      }

      return redirect()->route("qbo.invoices.all")->with('failed', $error);
    } else {
      //get invoice preview details
      $res = $efris->makePost('generate-fiscal-invoice-preview', ['data' => $efris_invoice['data']]);

      $response = json_decode($res);
      if (isset($response->data->sellerDetails)) {

        $pdfcontent =
          Utility::getsettings('invoice_print_type') === '58mm'
            ? Pdf::loadView('invoices::pos_invoice_print_preview', ['doc' => $response]) :
            Pdf::loadView('invoice_preview', ['doc' => $response]);

        $paper_size = Utility::getsettings('invoice_print_type');

        // set the view and render it
//        $pdfcontent = Pdf::loadView('invoices::pos_invoice_print_preview', ['doc' => $response]);

        $pdfcontent->setOption('isHtml5ParserEnabled', true);
        $pdfcontent->setOption('isRemoteEnabled', true);
        $pdfcontent->setOption(['dpi' => 100, 'defaultFont' => 'sans-serif']);
        $pdfcontent->setOption('isPhpEnabled', true);
        $pdfcontent->setPaper($paper_size, 'patriot');
//        $pdfcontent->setPaper(array(0,0,204,650));

        $canvas = $pdfcontent->getDomPDF()->getCanvas();
        $canvas->set_opacity(.2, 'Multiply');

        $canvas->set_opacity(.2);

        // output the generated PDF to the browser
        return $pdfcontent->stream('invoice_preview.pdf');
      }

//      dd($response->status->returnMessage);
      $message='';
      if (isset($response->status)){
        $message=$response->status->returnMessage;

      }
      return redirect()->route("qbo.invoices.all")->with('failed', $message);
    }
  }


  /**
   * Sync LocalDatabase with EFRIS platform
   */
  public function syncInvoices(): RedirectResponse
  {
    ini_set('memory_limit', '2048M');

    $items  = $this->queryInvoiceData();
    $qb_items = json_decode(json_encode($items), true);
    $indexed = collect($qb_items)->keyBy('DocNumber');

    //URA Items
    $efris = new ApiRequestHelper('efris1');
    $efris_response = $efris->makePost('invoice-receipt-query', []);
    $ef_items = json_decode($efris_response, true);

    // check tcs connection failure
    if ($ef_items['status']['returnCode'] == '999'){
      return redirect()->back()->with('failed', $ef_items['status']['returnMessage']);
    }
    $efris_indexed = collect($ef_items['data']['records']);

    // perform a batch insert of matched data
    QuickBooksInvoice::batchInsert($efris_indexed, $indexed);

    return redirect()->back()->with('success', 'Successfully synced Invoices with EFRIS platform');
  }

  /**
   * @return RedirectResponse
   * @throws Exception
   */
  public function actionFiscaliseInvoice($id, string $kind = 'INVOICE')
  {

    $efris = new ApiRequestHelper('efris1');
    $efris_invoice = QuickBooksInvoice::getFiscalInvoiceAttributes($id, $kind); // passed check mark
    if ($efris_invoice['is_errors']) {
      $invoiceItem = QuickBooksInvoice::find($id);
      $invoiceItem->validationError = implode(',', $efris_invoice['errors']);
      $invoiceItem->fiscalStatus = 0;
      $invoiceItem->validationStatus = 0;
      $invoiceItem->update();

      return redirect()->back()->with('failed', 'Invoice could not be fiscalised, has validation errors');
    } else {
      //Post it
      $response = $efris->makePost('generate-fiscal-invoice', ['data' => $efris_invoice['data']]);
      $feedback = json_decode($response);

      if ($feedback->status->returnCode != '00') {
        return redirect()->back()->with('failed', $feedback->status->returnMessage);
      } else {
        $invoiceItem = QuickBooksInvoice::find($id);
        $invoiceItem->fiscalStatus = 1;
        $invoiceItem->fiscalNumber = $feedback->data->basicInformation->invoiceNo;
        $invoiceItem->tax_amount = optional($feedback->data->summary)->taxAmount ?? null;
        $invoiceItem->validationStatus = 1;

        try {
          $invoiceItem->update(['timestamps' => false]);
          $what = ($kind == 'INVOICE') ? 'Invoice' : 'Receipt';

          return redirect()->back()->with('success', $what . ' successfully fiscalised');
        } catch (Exception $e) {
          return redirect()->back()->with('danger', $e->getMessage());
        }
      }
    }
  }

  public function queryInvoiceData()
  {
    $queryString = '/query?query=select * from Invoice maxresults 1000&minorversion=57';
    $quickbooks_invoices = (new self())->queryString($queryString);

    $invoices = json_decode(json_encode($quickbooks_invoices), true)['QueryResponse']['Invoice'] ?? [];
    return json_decode(json_encode($invoices), false);
  }
}
