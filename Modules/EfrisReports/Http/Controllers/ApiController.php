<?php

namespace Modules\EfrisReports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GoodsAndServices;
use App\Models\SalesReceipt;
use App\Services\ApiRequestHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mockery\Exception;

class ApiController extends Controller
{
    public function goods_and_services()
    {
        $data = GoodsAndServices::paginate(10);
        $data['total'] = $data->total();

        return $this->successResponse($data, 200);
    }

    public function fetch_sales_receipts()
    {
        $data = SalesReceipt::paginate(10);
        $data['total'] = $data->total();

        return $this->successResponse($data, 200);
    }

    /**
     * Invoices issued and fiscalised so far
     */
    public function invoices(Request $request)
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
                'pageNo' => $page,
                'pageSize' => 99,
                'buyerLegalName' => $customer,
                //                'buyerBusinessName' => $customer,
                'startDate' => ($period) ? ($dates[0]) : ($month_ago),
                'endDate' => ($period) ? ($dates[1]) : (date('Y-m-d')),
            ];

            // dd($query);
            $records = $api->makePost('invoice-receipt-query', $query);
            $data = json_decode($records);

            dd($data);

            return view('efrisreports::invoices',compact('data','query'));
            // $format = json_decode($records);

            // return $format;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Receipts issued and fiscalised so far
     */
    public function receipts(Request $request)
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
                'invoiceType' => 2,
                'pageNo' => $page,
                'pageSize' => 3,
                'buyerLegalName' => $customer,
                //                'buyerBusinessName' => $customer,
                'startDate' => ($period) ? ($dates[0]) : ($month_ago),
                'endDate' => ($period) ? ($dates[1]) : (date('Y-m-d')),
            ];

            // dd($query);
            $records = $api->makePost('invoice-receipt-query', $query);
            $format = json_decode($records);

            return $format;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Detail of a fiscal document (Receipt, Invoice, CreditNote, etc)
     */
    public function fiscalDocument($id)
    {
        $api = new ApiRequestHelper('efris1');
        $document = $api->makeGet('invoice-details/'.$id);

        return response()->json(['doc' => json_decode($document)]);
    }

    /**
     * Credit Notes from URA
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function creditNotes()
    {
        $request = request();
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

        return response()->json(['credit' => json_decode($records)]);
    }

    /**
     * Credit Note Details
     *
     * @param  int  $id
     */
    public function creditNoteDetails($id)
    {
        $api = new ApiRequestHelper('efris1');
        $data = $api->makeGet('creditnote-details/'.$id);
        $item = json_decode($data);

        return response()->json(['data' => $item->data]);
    }

    /**
     * Goods and Services from URA
     *
     * @return type
     */
    public function goodsAndServices2()
    {
        $api = new ApiRequestHelper('efris');
        $records = $api->makePost('goods-and-services', []);

        return response()->json(['goods' => $records]);
        //        return $this->render('goods-and-services', ['goods' =>$records]);
    }

    /**
     * Goods and Services from URA paginated and search
     */
    public function goodsAndServices(Request $request)
    {
        ini_set('memory_limit', '2048M');

        $api = new ApiRequestHelper('efris');

        $goodsName = $request->input('goodsName');
        $goodsCode = $request->input('goodsCode');
        $pageSize = $request->input('pageSize');
        $pageNo = $request->input('pageNo');

        $query = [];

        if ($goodsName != null && $goodsCode != null) {
            $query = [
                'goodsName' => $goodsName,
                'goodsCode' => $goodsCode,
            ];
        } elseif ($goodsName != null && $goodsCode == null) {
            $query = [
                'goodsName' => $goodsName,
            ];
        } elseif ($goodsName == null && $goodsCode != null) {
            $query = [
                'goodsCode' => $goodsCode,
            ];
        } else {
            $query = [
                'pageSize' => $pageSize ??  99,
                'pageNo' => $pageNo ??  1,
            ];

            // dd($query);
        }

        $records = $api->makePost('goods-and-services', $query);
        $goods['goods'] = json_decode($records);
        $goods['goodsName'] = $goodsName;
        $goods['goodsCode'] = $goodsCode;

        return $goods;
    }
}
