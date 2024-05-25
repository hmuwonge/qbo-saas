<?php

namespace Modules\EfrisReports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use App\Models\QuickBooksInvoice;
use App\Services\ApiRequestHelper;
use App\Services\QuickBooksServiceHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery\Exception;

class EfrisController extends Controller
{
    /**
     * List of all fiscalised receipts
     *
     * @return mixed
     */
    public function receipts()
    {
        $request = request();
        $page = $request->input('page', 1);
        $customer = $request->input('customer_name');
        $period = $request->input('invoice_period');
        //Seperate the dates
        $dates = explode(' to ', $period);
        $api = new ApiRequestHelper('efris1');
        //One month ago
        $oneMonthAgo = Carbon::now()->subMonths(2);
        $month_ago = $oneMonthAgo->toDateString();

        $query = [
            'invoiceKind' => 2,
            'invoiceType' => 2,
            'pageNo' => $page,
            'pageSize' => 20,
            'startDate' => ($period) ? ($dates[0]) : ($month_ago),
            'endDate' => ($period) ? ($dates[1]) : (date('Y-m-d')),
        ];
        $records = $api->makePost('invoice-receipt-query', $query);
        $details = json_decode($records, true);

      if ($details['status']['returnCode'] == '999'){
        return redirect()->back()->with('failed', $details['status']['returnMessage']);
      }
        // return $details;
        $raw_data = $details['data']['records'];
        $page_data = $details['data']['page'];

        $data = collect($raw_data)->paginate($query['pageSize'] - $query['pageNo']);


        return view('efrisreports::receipts', compact('data'));
    }

    public function invoices(Request $request)
    {
        $pageNo = $request->input('page',1);
        $customer = $request->input('customer_name');
        $period = $request->input('invoice_period');
        //Seperate the dates
        $dates = explode(' to ', $period);
        $api = new ApiRequestHelper('efris1');
        //One month ago
        $oneMonthAgo = Carbon::now()->subMonths(2);
        $month_ago = $oneMonthAgo->toDateString();
        $query = [
            'invoiceKind' => 1,
            'invoiceType' => 1,
            'pageNo' => $pageNo,
            'pageSize' => 20,
            'buyerLegalName' => $customer,
            //                'buyerBusinessName' => $customer,
            'startDate' => ($period) ? ($dates[0]) : ($month_ago),
            'endDate' => ($period) ? ($dates[1]) : (date('Y-m-d')),
        ];

        $records = $api->makePost('invoice-receipt-query', $query);
        $responseData = json_decode($records, true);

      if ($responseData['status']['returnCode'] != '00'){
        return redirect()->back()->with('failed', $responseData['status']['returnMessage']);
      }

        $records = $responseData['data']['records'];
        $pagination = $responseData['data']['page'];
        $totalSize = $pagination['totalSize'];

        $paginator = null; // Initialize paginator as null

        if ($pageNo) { // If page parameter is supplied
            $paginator = new LengthAwarePaginator($records, $pagination['totalSize'], $pagination['pageSize']);
            $paginator->setPath(route('ura.invoices')); // url('/paginated-data')
        }

        // Return the data, including paginated records if applicable
        return view('efrisreports::invoices', [
            'records' => $paginator ? $paginator->items() : $records, // Use paginated items if paginator exists, otherwise use original records
            'links' => $paginator?->links(), // Include pagination links only if paginator exists
            'total' => $totalSize
        ]);
    }


    /**
     * Credit Notes from URA
     *
     * @return Renderable
     */
    public function creditNotes(Request $request)
    {
      $pageNo = $request->input('page',1);
        $period = $request->input('credit_period');
        $customer = $request->input('customer_name');
        $invoice_no = $request->input('oriInvoiceNo');
        $ref_no = $request->input('referenceNo');
        $page = $request->input('page', 1);

        //Seperate the dates
        $dates = explode(' to ', $period);
        $api = new ApiRequestHelper('efris1');
        $query = [
            'referenceNo' => $ref_no,
            'oriInvoiceNo' => $invoice_no,
            'invoiceNo' => '',
            'combineKeywords' => $customer,
            'approveStatus' => '',
            'queryType' => 1,
            'invoiceApplyCategoryCode' => '',
            'pageNo' => $page,
            'pageSize' => 15,
            'startDate' => ($period) ? ($dates[0]) : (''),
            'endDate' => ($period) ? ($dates[1]) : (''),
        ];
        $records = $api->makePost('query-creditnotes', $query);
//        $details = json_decode($records, true);
//
//        $raw_data = $details['data']['records'];
//        $page_data = $details['data']['page'];
//
//        $data = collect($raw_data)->paginate($query['pageSize'] - $query['pageNo']);
//      dd($records);
      $responseData = json_decode($records, true);

      if ($responseData['status']['returnCode'] !== '00') {
          return redirect()->back()->with('failed', $responseData['status']['returnMessage']);
      }
      $records = $responseData['data']['records'];
      $pagination = $responseData['data']['page'];
      $totalSize = $pagination['totalSize'];

      $paginator = null; // Initialize paginator as null

      if ($pageNo) { // If page parameter is supplied
        $paginator = new LengthAwarePaginator($records, $pagination['totalSize'], $pagination['pageSize']);
        $paginator->setPath(route('ura.creditnotes')); // url('/paginated-data')
      }

        //      return response()->json(['credit' => json_decode($records)]);
        //        return view('efrisreports::creditnotes',compact('data'));
//        return view('efrisreports::creditnotes', compact('data', 'query', 'page_data'));

      return view('efrisreports::creditnotes', [
        'records' => $paginator ? $paginator->items() : $records,
        'links' => $paginator ? $paginator->links() : null,
        'total' => $totalSize
      ]);
    }

    /**
     * Cancel Credit Notes from URA
     *
     * @return Renderable
     */
    public function cancelCreditNotes()
    {
        return view('EfrisUra/CreditNotes/CancelCreditNote');
    }

    /**
     * Credit Notes from URA
     */
    public function approveCreditNotes()
    {
        $request = request();
        $refno = $request->input('ref_number');
        $orinvoice = $request->input('remarks');
        //Seperate the dates
        $api = new ApiRequestHelper('efris1');
        $query = [
            'referenceNo' => $refno,
            'remarks' => $orinvoice,
            'taskId' => '1',
            'approveStatus' => '101',
        ];
        $records = $api->makePost('query-creditnotes', $query);
        //        to do
        return response()->json(['credit' => $records]);
    }

    /**
     * Credit Note Details
     *
     * @param  int  $id
     */
    public function creditNoteDetails($id)
    {
        $api = new ApiRequestHelper('efris1');
        $data = $api->makeGet('creditnote-details/' . $id);
        $item = json_decode($data);
        $original_invoice_number = request()->input('invoiceNo');
        $get_invoice_details = $api->makeGet('invoice-details/' . $original_invoice_number);
        $decode_invoice = json_decode($get_invoice_details);

        $reasons = [
            101 => 'Buyer refused to accept the invoice due to incorrect invoice/receipt',
            102 => 'Not delivered due to incorrect invoice/receipt',
            103 => 'Others (Please specify)',
        ];
        $invoice_data = [
            'oriInvoiceId' => $decode_invoice->data->basicInformation->invoiceId,
            'invoiceNo' => $original_invoice_number,
            'reasons' => $reasons
        ];

        // return response()->json(['data' => $item->data]);
        return view('EfrisUra/CreditNotes/CancelCreditNote', ['itemDetails' => $item, 'data' => $invoice_data]);
    }

    public function cancelCreditNote($oriInvoiceNo, $invoiceNo, $id)
    {
        $api = new ApiRequestHelper('efris1');
        $credit = new CreditNote;
        //$efris = new ApiRequestHelper('efris1');
        $invoice = $api->makeGet('invoice-details/' . $oriInvoiceNo);
        //return $this->redirect(['efris/credit-notes']);
        return response()->json([
            'credit' => $credit,
            'invoice' => json_decode($invoice),
            'id' => $id, 'invoiceNo' => $invoiceNo,
            'oriInvoiceNo' => $oriInvoiceNo
        ]);
    }

    /**
     * Approve CreditNote
     *
     * @param  int  $id
     */
    public function approveIssuedCreditNote($oriInvoiceNo, $referenceNo, $taskId, $id)
    {
        $api = new ApiRequestHelper('efris1');
        $credit = new CreditNote;
        //$efris = new ApiRequestHelper('efris1');
        $invoice = $api->makeGet('invoice-details/' . $oriInvoiceNo);
        //return $this->redirect(['efris/credit-notes']);
        return response()->json([
            'credit' => $credit,
            'invoice' => json_decode($invoice),
            'id' => $id,
            'referenceNo' => $referenceNo,
            'taskId' => $taskId,
            'oriInvoiceNo' => $oriInvoiceNo
        ]);
    }

    public function sendCancelCreditNote(Request $request)
    {

        //        $credit = new CreditNote;
        $api = new ApiRequestHelper('efris1');
        //        $request = request()->input('CreditNotes');
        $orInvoiceID = $request->oriInvoiceId;
        $invoiceNo = $request->invoiceNo;
        $reason = $request->reason;
        $reasonCode = $request->reasonCode;
        //prepare the object
        $prepare = [
            'oriInvoiceId' => $orInvoiceID,
            'invoiceNo' => $invoiceNo,
            'reason' => $reason,
            'invoiceApplyCategoryCode' => '104',
            'reasonCode' => $reasonCode,
        ];

        //send data
        $feedback = $api->makePost('cancel-creditnote', $prepare);
        $data = json_decode($feedback, false);

        QuickBooksServiceHelper::logToFile($data);
        // check the return code
        if (is_object($data)) {
            $returnCode = $data->status->returnCode;
            $returnMessage = $data->status->returnMessage;

            if ($returnCode != '00') {
                return response()->json(['status' => 'FAIL', 'payload' => $returnMessage]);
            } else {
                return response()->json(['status' => 'SUCCESS', 'payload' => $returnMessage . 'FULLY' . ' ' . 'applied credit note cancel']);
            }
        } else {
            if ($data->status->returnCode == '00') {
                return redirect()->back()->with('success', $data->data);

                //Save without validation
            } elseif ($data->status->returnCode != '00') {
                return redirect()->back()->with('error', $data->data);
            }
        }
    }

    public function sendApproveCreditNote(Request $request)
    {
        $api = new ApiRequestHelper('efris1');
        $referenceNo = $request->input('referenceNo');
        $taskId = $request->input('taskId');
        $remark = $request->input('remark');
        $approveStatus = $request->input('approveStatus');

        $prepare = [
            'referenceNo' => $referenceNo,
            'taskId' => $taskId,
            'remark' => $remark,
            'approveStatus' => $approveStatus,
        ];

        $feedback = $api->makePost('approve-creditnote', $prepare);

        $query = [
            'referenceNo' => '',
            'oriInvoiceNo' => '',
            'invoiceNo' => '',
            'combineKeywords' => '',
            'approveStatus' => '',
            'queryType' => '2',
            'invoiceApplyCategoryCode' => '',
            'pageNo' => [
                'page' => 1,
            ],
            'pageSize' => 15,
            'startDate' => '',
            'endDate' => '',
        ];
        $record = $api->makePost('query-creditnotes', $query);

        session()->flash('success', 'You have successfully approved a credit note');

        return view('approve-issued-credit-note', ['credit' => $record, 'feedback' => $feedback]);
    }

    /**
     * Goods and Services from URA
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function goodsAndServices2(Request $request)
    {
        ini_set('memory_limit', '2048M');
        $api = new ApiRequestHelper('efris');
        $goodsName = $request->goodsName;
        $goodsCode = $request->goodsCode;
        $pageNo = $request->input('page',1);
        $query = array_filter([
            'goodsName' => $goodsName,
            'goodsCode' => $goodsCode,
            'pageSize' => $pageSize ?? 99,
            'pageNo' => $pageNo,
        ], function ($value) {
            return $value !== null;
        });

        $records = $api->makePost('goods-and-services', $query);
        $responseData = json_decode($records, true);

      if ($responseData['status']['returnCode'] !== '00'){
        return redirect()->back()->with('failed', $responseData['status']['returnMessage']);
      }

        $records = $responseData['data']['records'];
        $pagination = $responseData['data']['page'];
        $totalSize = $pagination['totalSize'];

        $paginator = null; // Initialize paginator as null

        if ($pageNo) { // If page parameter is supplied
            $paginator = new LengthAwarePaginator($records, $pagination['totalSize'], $pagination['pageSize']);
            $paginator->setPath(route('efris.goods')); // url('/paginated-data')
        }

        // Return the data, including paginated records if applicable
        return view('efrisreports::goods', [
            'records' => $paginator ? $paginator->items() : $records, // Use paginated items if paginator exists, otherwise use original records
            'links' => $paginator ? $paginator->links() : null, // Include pagination links only if paginator exists
            'total' => $totalSize
        ]);
    }

    public function downloadInvoice($id, $type = 'invoice')
    {
        ini_set('memory_limit', '2048M');

        $api = new ApiRequestHelper('efris1');
        $qbapi = new ApiRequestHelper('qb');
        $document = $api->makeGet('invoice-details/' . $id);
        $qrcode = $api->makeGet('qrcode/' . $id);
        $_tin = Yii::$app->member->company->tin;
        $efris_invoice = json_decode($document);

        switch ($_tin) {
            case 1015650841:
                $qbInvoice = QuickbooksInvoice::find(['refNumber' => $efris_invoice->data->sellerDetails->referenceNo]);
                $content = view('pdf-fiscal-kingdom-document', [
                    'doc' => json_decode($document),
                    'qrcode' => json_decode($qrcode),
                    'type' => $type,
                    'qbInvoice' => $qbInvoice,
                ])->render();
                break;
            case 1010014782:
                $content = view('pdf-fiscal-bluecrane-document', [
                    'doc' => json_decode($document),
                    'qrcode' => json_decode($qrcode),
                    'type' => $type,
                ])->render();
                break;
            case 1000253727:
                $content = view('spotclean', [
                    'doc' => json_decode($document),
                    'qrcode' => json_decode($qrcode),
                    'type' => $type,
                ])->render();
                break;
            case 1008121763:
                $content = view('kenOil', [
                    'doc' => json_decode($document),
                    'qrcode' => json_decode($qrcode),
                    'type' => $type,
                ])->render();
                break;
            case 1001302007:
            default:
                $content = view('pdf-fiscal-document', [
                    'doc' => json_decode($document),
                    'qrcode' => json_decode($qrcode),
                    'type' => $type,
                ])->render();
                break;
        }

        $paperSize = ($_tin == 1000253727 or $_tin == 1008121763) ? [80, 80] : PDF::FORMAT_A4;
        $margins = ($_tin == 1000253727 or $_tin == 1008121763) ? 4 : null;

        $pdf = Pdf::loadView('pdf-template', ['content' => $content])
            ->setPaper($paperSize, 'portrait')
            ->setOptions([
                //                // set to use core fonts only
                //                'mode' => Pdf::MODE_CORE,
                //                // A4 paper format
                //                'format' => $paperSize,
                //                // portrait orientation
                //                'orientation' => Pdf::ORIENT_PORTRAIT,
                //                // stream to browser inline
                //                'destination' => Pdf::DEST_BROWSER,
                'margin-top' => $margins,
                'margin-bottom' => $margins,
                'margin-left' => $margins,
                // set mPDF properties on the fly
                //                'options' => ['title' => 'Fiscal Document']
            ]);

        switch ($_tin) {
            case 1001302007: // MWH
                $pdf->setMargins(0, 0, 17);
                $pdf->setWatermarkImage('https://html.kakasa.app/invoice-header/1001302007.png', 1, [210, 297], [0, 0]);
                break;
            case 1015650841: // Kingdom Trading
                $pdf->setMargins(0, 0, 18);
                $pdf->setWatermarkImage('https://html.kakasa.app/invoice-header/1015650841.png', 1, [210, 297], [0, 0]);
                break;
            case 1000253727: // spot clean
                $pdf->setMargins(0, 0, 5);
                break;
            case 1008121763: // ken oils
                $pdf->setMargins(0, 0, 5);
                break;
            case 1000207274:
                $pdf->setMargins(0, 0, 10);
                $pdf->setWatermarkImage('https://html.kakasa.app/invoice-header/blue-four.jpeg', 1, [210, 297], [0, 0]);
                break;
            case 1010014782:
            case 1000030306:
            default:
                $pdf->setMargins(0, 0, 10);
                break;
        }

        $pdf->setAutoPageBreak(true);
        $pdf->setShowWatermarkImage(true);
        $pdf->setWatermarkImageBehind(true);
        $pdf->writeHTML($content);

        return $pdf->output();
    }

    /**
     * Payment Methods
     *
     * @return JsonResponse
     */
    public function paymentMethods()
    {
        $api = new ApiRequestHelper('efris1');
        $records = $api->makeGet('masterdata/payment-methods');

        return response()->json(['records' => $records]);
    }

    /**
     * Units of measure
     *
     * @return JsonResponse
     */
    public function actionUnitOfMeasure()
    {
        $api = new ApiRequestHelper('efris1');
        $records = $api->makeGet('master-data');
        $format_data = json_decode($records, true);

        return response()->json($format_data);
    }

    public function actionVatRegimes()
    {
        $api = new ApiRequestHelper('efris1');
        $records = $api->makeGet('vat-regimes');

        return response()->json(['records' => $records]);
    }

    public function actionSectors()
    {
        $api = new ApiRequestHelper('efris1');
        $records = $api->makeGet('sectors');

        return response()->json(['records' => $records]);
    }

    public function currency()
    {
        $api = new ApiRequestHelper('efris1');
        $records = $api->makeGet('currencies');

        return response()->json(['records' => $records]);
    }

    public function actionCountries()
    {
        $api = new ApiRequestHelper('efris1');
        $records = $api->makeGet('country-codes');

        return response()->json(['records' => $records]);
    }

    public function actionExiseDuty()
    {
        //  $this->layout = "loggedin";
        $api = new ApiRequestHelper('efris1');
        $records = $api->makeGet('excise-duty');

        return response()->json(['records' => json_decode($records)]);
    }

    public function actionUnspsc()
    {
        ini_set('memory_limit', '2048M'); //Allow up to 2GB for this action
        $api = new ApiRequestHelper('efris1');
        $records = $api->makeGet('unspsc-codes');

        return response()->json(['records' => json_decode($records)]);
    }

    public function unspscList($q = null)
    {
        return response()->json([
            'data' => $this->getUnspsc($q),
        ]);
    }

    private function getUnspsc($q = null)
    {
        $api = new ApiRequestHelper('efris');
        $records = $api->makeGet('masterdata/unspsc/' . $q);
        $unspsc = json_decode($records, true);
        //Rename Array keys
        $results = array_map(function ($tag) {
            return ['id' => $tag['commodity_code'], 'text' => $tag['commodity_name']];
        }, $unspsc['data']);

        return $results;
    }

    // render pdf to browser
    public function actionViewInvoicePdf($id)
    {
        // fetch invoice for print
        $api = new ApiRequestHelper('efris1');
        $document = $api->makeGet('invoice-details/' . $id);
        $reg_details = $api->makeGet('registration-details');

        //qrcode
        $qrcode = $api->makeGet('qrcode/' . $id);
        // dd(json_decode($qrcode,true));
        $pdf = Pdf::loadView('preview-invoice', [
            'doc' => json_decode($document, false),
            'qrcode' => json_decode($qrcode, true), 'regDetails' => json_decode($reg_details, false)
        ]);

        return $pdf->stream('filename.pdf', ['Attachment' => false]);
    }

    public function actionViewCreditnotePdf($id)
    {
        // fetch invoice for print
        $api = new ApiRequestHelper('efris1');
        $document = $api->makeGet('invoice-details/' . $id);
        $reg_details = $api->makeGet('registration-details');

        //qrcode
        $qrcode = $api->makeGet('qrcode/' . $id);
        $pdf = Pdf::loadView('preview-creditnote', ['doc' => json_decode($document, false), 'qrcode' => $qrcode, 'regDetails' => json_decode($reg_details, false)]);

        return $pdf->stream('filename.pdf', ['Attachment' => false]);
    }

    /**
     * Download a PDF version of the Invoice Summary
     */
    public function actionDownloadReportPdf(Request $request)
    {
        ini_set('memory_limit', '2048M'); //Allow up to 2GB for this action

        try {
            $page = $request->input('pageNo', 1);
            $customer = $request->input('customer_name');
            $period = $request->input('invoice_period');
            //Seperate the dates
            $dates = explode(' to ', $period);
            $api = new ApiRequestHelper('efris1');
            //One month ago
            $oneMonthAgo = Carbon::now()->subMonths(2);
            $month_ago = $oneMonthAgo->toDateString();
            $query = [
                'invoiceKind' => 1,
                'invoiceType' => 1,
                // 'pageNo' => $page,
                // 'pageSize' => 3,
                'buyerLegalName' => $customer,
                'startDate' => ($period) ? ($dates[0]) : ($month_ago),
                'endDate' => ($period) ? ($dates[1]) : (date('Y-m-d')),
            ];

            $records = $api->makePost('invoice-receipt-query', $query);
            $format = json_decode($records);

            // dd($format->data);
            $records = $format->data->records;
            if ($period || $customer) { // If we have a customer or the periods
                // set the view and render it
                $pdfcontent = Pdf::loadView('download-report', ['doc' => $records]);

                $pdfcontent->setOption('isHtml5ParserEnabled', true);
                $pdfcontent->setOption('isRemoteEnabled', true);
                //        Pdf::setOption('A4', 'portrait');
                $pdfcontent->setOption(['dpi' => 150, 'defaultFont' => 'Helvetica']);
                $pdfcontent->setOption('isPhpEnabled', true);
                $pdfcontent->setPaper('A4', 'portrait');

                $canvas = $pdfcontent->getDomPDF()->getCanvas();

                $height = $canvas->get_height();
                $width = $canvas->get_width();
                $canvas->set_opacity(.2, 'Multiply');

                $canvas->set_opacity(.2);

                // dd( $records);

                // output the generated PDF to the browser
                return $pdfcontent->stream('download-report.pdf');
            } else {

                return view('download-report-pdf', ['records' => json_decode($records)]);
            }
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function downloadReport()
    {
        return view('DownloadReport');
    }
}
