<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Mockery\Exception;
use App\Models\EfrisItem;
use App\Traits\Responser;
use App\Models\CreditMemo;
use Illuminate\Support\Arr;
use App\Models\EfrisInvoice;
use App\Models\VendorCredit;
use Illuminate\Http\Request;
use App\Models\StockDecrease;
use App\Traits\QboRequestHelper;
use App\Models\QuickBooksInvoice;
use Illuminate\Http\JsonResponse;
use App\Services\ApiRequestHelper;
use App\Services\EfrisItemsService;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Services\EfrisInvoiceService;
use Illuminate\Support\Facades\Session;
use App\Services\QuickBooksServiceHelper;
use Illuminate\Support\Facades\Validator;
use App\Services\QBOServices\OAuthClientService;
use QuickBooksOnline\API\Exception\IdsException;
use QuickBooksOnline\API\Exception\SdkException;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\ServiceException;

class QBOController extends Controller
{
    use Responser, QboRequestHelper;

    public EfrisInvoiceService $efrisInvoiceService;

    private string $customerName = '';

    public function __construct(EfrisInvoiceService $efrisInvoiceService)
    {
        $this->efrisInvoiceService = $efrisInvoiceService;
    }

    /**
     * @throws SdkException
     */
    public function index(OAuthClientService $service): string
    {
        return $service->connect();
    }

    /**
     * @throws ServiceException
     * @throws SdkException
     */
    public function callback(Request $request, OAuthClientService $service)
    {
        return $service->callback($request);
    }

    public function refresh_token(): JsonResponse
    {
        return (new OAuthClientService())->refresh_token();
    }

    /**
     * returns all invoices from quickbooks
     *
     * @throws SdkException
     */
    public function allInvoices()
    {
        try {
            $qbo_invoices = QuickBooksInvoice::paginate(100);

            return Inertia::render('Quickbooks/Invoices/InvoicesLayer', ['invoices' => $qbo_invoices]);
            // return redirect()->route('dashboard.integrator');
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Details of a CreditMemo
     *
     * @throws SdkException
     */
    public function creditMemoDetails(Request $request): JsonResponse
    {
        $accessToken = Session::get('sessionAccessToken');

        $accessTokenJson = $this->getAccessToken($accessToken);

        $dataService = DataService::Configure(
            $accessTokenJson
        );

        $id = $request->id;
        $dataService->updateOAuth2Token($accessToken);

        $all_receipts = $dataService->FindById('creditmemo', $id);

        $format = json_encode($all_receipts);

        return response()->json($format);
    }

    /**
     * Details of a receipt refund
     *
     * @throws SdkException
     * @throws IdsException
     */
    public function receiptRefundDetails(Request $request): JsonResponse
    {
        $this->makeApiCall();

        $accessToken = Session::get('sessionAccessToken');

        $accessTokenJson = $this->getAccessToken($accessToken);

        $dataService = DataService::Configure(
            $accessTokenJson
        );

        $id = $request->id;
        $dataService->updateOAuth2Token($accessToken);

        $all_receipts = $dataService->FindById('refundreceipt', $id);

        $format = json_encode($all_receipts);

        return response()->json($format);
    }

    /**
     * Invoice details (for a given ID)
     *
     * @return string|false
     *
     * @throws SdkException
     * @throws IdsException
     */
    public function invoiceDetails($id)
    {
        $all_details = $this->urlQueryBuilderById('invoice', $id);

        return json_encode($all_details);
    }



    /**
     * All VendorCredit
     *
     * @throws SdkException
     * @throws \Exception
     */
    public function allVendorCredit(Request $request): JsonResponse
    {
        $this->makeApiCall();

        $accessToken = Session::get('sessionAccessToken');

        $accessTokenJson = $this->getAccessToken($accessToken);

        $dataService = DataService::Configure(
            $accessTokenJson
        );

        $dataService->updateOAuth2Token($accessToken);

        $all_receipts = $dataService->Query('SELECT * FROM vendorcredit');

        $format = json_encode($all_receipts);

        return response()->json($format);
    }

    /**
     * Details of a VendorCredit record
     *
     * @throws SdkException
     * @throws IdsException
     */
    public function vendorCreditDetails(Request $request): JsonResponse
    {
        $this->makeApiCall();

        $accessToken = Session::get('sessionAccessToken');

        $accessTokenJson = $this->getAccessToken($accessToken);

        $dataService = DataService::Configure(
            $accessTokenJson
        );

        $dataService->updateOAuth2Token($accessToken);
        $id = $request->id;

        $all_receipts = $dataService->FindById('vendorcredit', $id);
        $format = json_encode($all_receipts);

        return response()->json($format);
    }

    /**
     * Invoices from a given branch
     *
     * @throws IdsException
     * @throws SdkException
     */
    public function branchtInvoices(Request $request): JsonResponse
    {
        $accessToken = Session::get('sessionAccessToken');

        $accessTokenJson = $this->getAccessToken($accessToken);

        $dataService = DataService::Configure(
            $accessTokenJson
        );

        $dataService->updateOAuth2Token($accessToken);
        $branch = $request->branch;

        $query = "select * from Invoice where DocNumber LIKE '%" . $branch . "%'";

        $all_receipts = $dataService->Query($query);

        $format = json_encode($all_receipts);

        return response()->json($format);
    }

    public function updateStockDecrease(Request $request)
    {
        StockDecrease::whereIn('id', $request->ids)->update([
            'adjust_type' => $request->adjustType,
            'adjust_reason' => $request->adjustReason,
            'transact_id' => $request->transactId,
        ]);

        return response()->json('Records successfully updated');
    }



    public function call_builder()
    {
        return $this->QueryBuilder('Invoice', null, '1', '1');
    }

    /**
     * Receipt Refunds
     *
     * @param  type  $validate
     */
    public function actionReceiptRefunds()
    {
        $credit_notes = $this->urlQueryBuilderAll('RefundReceipt');
        $cust = [];
        if ($credit_notes) {
            if (array_key_exists('fault', $credit_notes)) {
                $error_body = $credit_notes['fault']['error'][0];
                if ($error_body['code'] == '3200') {
                    return Inertia::render('Integrator', ['url' => $this->qbo_url()]);
                }
            } elseif (array_key_exists('RefundReceipt', $credit_notes['QueryResponse'])) {
                $cust = $credit_notes['QueryResponse']['RefundReceipt'];
            } else {
                $cust = [];
            }
        }

        $invoiceStatus = CreditMemo::all()->keyBy('id');

        // Go to the View
        return Inertia::render('Quickbooks/ReceiptRefunds/ReceiptRefunds', ['refunds' => $cust, 'invoiceStatus' => $invoiceStatus]);
    }



    public function invoiceIssues()
    {
        return Inertia::render('InvoiceIssues');
    }

    public function invoicesPendingList()
    {
        $invoices = $this->urlQueryBuilderAll('invoice');
        $invoiceStatus = [];

        // Get the invoice validation messages from the DB
        if (EfrisInvoice::exists()) {
            $invoiceStatus = EfrisInvoice::all()->keyBy('id');
        }

        // All Fiscalised Invoices
        $fiscalised = EfrisInvoice::where('fiscalStatus', 1)->get();

        $data = json_decode(json_encode($invoices), true);

        // Remove items already fiscalised
        foreach ($data['QueryResponse']['Invoice'] as $key => $value) {
            if (Arr::first($fiscalised, function ($item) use ($value) {
                return $item['id'] == $value['Id'];
            })) {
                unset($data['QueryResponse']['Invoice'][$key]);
            }
        }

        // Go to the View
        return \response()->json(['status' => 'true', 'data' => $data['QueryResponse']['Invoice'], 'invoiceStatus' => $invoiceStatus], 200);
        //        return view('invoices-pending', [
        //            'data' => $data['QueryResponse']['Invoice'],
        //            'invoiceStatus' => $invoiceStatus,
        //        ]);
    }

    public function invoicePendingList()
    {
        return Inertia::render('InvoicePending');
    }

    public function actionItemProductDetails($id)
    {
        $data = $this->urlQueryBuilderById('item', $id);
        $item = json_decode(json_encode($data))->original;

        // pick all units of measure
        $efris = new ApiRequestHelper('efris');
        $unitOfMeasure = $efris->makeGet('master-data');
        $measureUnits = json_decode($unitOfMeasure);

        // pick all currency
        $currencies = $efris->makeGet('currencies');
        $all_currencies = json_decode($currencies);

        $Unit = '';
        $Curr = '';

        if (isset($item->Item->AdditionalDetails)) {
            $measureUnitValue = '';
            $currencyValue = '';

            $measureUnit = '';
            $currency = '';

            // check if measure unit exits in array
            if ($item->Item->AdditionalDetails->unitOfMeasure == $measureUnitValue) {
                $Unit = $measureUnit;
            }
            // check if currency exits in array
            if ($item->Item->AdditionalDetails->currency == $currencyValue) {
                $Curr = $currency;
            }
        }

        return Inertia::render('ProductDetails', [
            'data' => $item->Item,
            'unitOfMeasure' => $Unit,
            'currency' => $Curr,
        ]);
    }

    /**
     * Details of an Invoice from an ID
     *
     * @param  int  $id
     * @return array
     *
     * @throws SdkException|IdsException
     */
    public function getInvoiceDetails($id, $inv_kind = 'RECEIPT')
    {
        if ($inv_kind == 'INVOICE') {
            $item = $this->invoiceDetails($id);
        } else {
            $item = $this->receiptDetails($id);
        }
        //Invoice details
        return json_decode($item)->original;
    }

    /** All Invoices that have not been fiscalised
     * @return Response
     *
     * @throws SdkException
     */
    public function actionInvoicesPending(Request $request)
    {
        $invoices = $this->urlQueryBuilderAll('invoice');

        if ($invoices) {
            if (array_key_exists('fault', $invoices)) {

                $error_body = $invoices['fault']['error'][0];

                if ($error_body['code'] == '3200') {
                    return Inertia::render('Integrator', ['url' => $this->qbo_url()]);
                }
            } elseif (array_key_exists('Invoice', $invoices['QueryResponse'])) {
                $invoicesData = $invoices;
            }
        }

        $invoiceStatus = [];

        //Get the invoice validation emssages from the DB
        if (EfrisInvoice::exists()) {
            $invoiceStatus = EfrisInvoice::all()->keyBy('id');
        }

        //All Fiscalised Invoices
        $fiscalised = EfrisInvoice::where('fiscalStatus', 1)->get();

        $data = json_decode(json_encode($invoicesData), true);

        //Remove items already fiscalised
        foreach ($invoices['QueryResponse']['Invoice'] as $key => $invoice) {
            if (in_array($invoice['Id'], $fiscalised->pluck('id')->toArray())) {
                unset($invoices['QueryResponse']['Invoice'][$key]);
            }
        }

        $data = [
            'data' => $data['QueryResponse']['Invoice'],
            'invoiceStatus' => $invoiceStatus,
        ];

        //Go to the View
        return response()->json([
            'status' => true,
            'payload' => $data,
        ], 200);
    }

    public function actionModifyRegisteredProduct($id)
    {
        $api = new ApiRequestHelper('qb');
        $efris_api = new ApiRequestHelper('efris1');
        $efris_ap = new ApiRequestHelper('efris');
        $item = $this->urlQueryBuilderById('item', $id);
        $efris = new EfrisItem(['id' => $id]);

        if (request()->isMethod('post')) {
            $posted = request()->post();
            $form = $posted['EfrisItem'];
            //Sync products
            $efris_api->makePost('sync-products', []);
            $product = EfrisItem::prepareModifyStockObject($form);
            $stockIn_response = $efris_api->makePost('register-product', $product);

            $efrisdb = EfrisItem::find($id);
            $efrisdb->fill($posted);

            if ($efrisdb->save()) {
                return response()->json(['success' => 'The item has been successfully updated']);

                //                return redirect()->route('quickbooks.items');
            } else {
                return response()->json(['danger' => 'Sorry there was a problem updating your local db']);

                // return redirect()->route('quickbooks.items');
            }
        } else {
            //$efris->stockStatus = 1;
            $efrisdb = EfrisItem::find(['id' => $id])->first();
            $measure_unit = $efris_api->makeGet('master-data');

            //Item details from QB
            return view('modify-registered-product', [
                'item' => json_decode($item),
                'efris' => $efrisdb,
                'measureunit' => json_decode($measure_unit),
            ]);
        }
    }

    public function registerProduct($id, $redo = 'no')
    {
        try {
            //Item details from QB
            $item = $this->itemDetails($id);

            $item_dec = json_decode($item, true);
                $efris = new ApiRequestHelper('efris1');

                $posted = request()->post();

                $qb = json_decode($item, true);

                $form = [];
                $form['unitOfMeasure'] = $posted['measureUnit'];
                $form['currency'] = $posted['currency'];
                $form['commodityCategoryId'] = $posted['commodityCategoryId'];
                $form['havePieceUnit'] = $posted['havePieceUnit'];
                $form['pieceUnitPrice'] = $posted['pieceUnitPrice'];
                $form['pieceScaledValue'] = $posted['pieceScaledValue'];
                $form['pieceMeasureUnit'] = $posted['pieceMeasureUnit'];
                $form['packageScaledValue'] = $posted['packageScaledValue'];
                $form['haveExciseTax'] = $posted['haveExciseTax'];
                $form['haveOtherUnit'] = $posted['haveOtherUnit'];
                $form['hasOpeningStock'] = $posted['hasOpeningStock'];
                $form['otherUnit'] = $posted['otherUnit'];
                $form['otherPrice'] = $posted['otherPrice'];
                $form['otherScaled'] = $posted['otherScaled'];
                $form['packageScaled'] = $posted['packageScaled'];
                $form['exciseDutyCode'] = $posted['exciseDutyCode'];
                //                $isEfrisRegistered = 'no';
                $isEfrisRegistered = $posted['isRegisteredInEfris'];

                if ($isEfrisRegistered == 'yes') {
                    //Prepare details to save to the DB
                    $efrisdb = new EfrisItem;
                    $efrisdb->id = request()->Id;
                    $efrisdb->commodityCategoryId = request()->commodityCategoryId;
                    $efrisdb->currency = request()->currency;
                    $efrisdb->exciseDutyCode = request()->exciseDutyCode;
                    $efrisdb->hasOpeningStock = request()->hasOpeningStock;
                    // $efrisdb->haveExciseDuty =request()->haveExciseDuty;
                    $efrisdb->haveExciseTax = request()->haveExciseTax;
                    $efrisdb->haveOtherUnit = request()->haveOtherUnit;
                    $efrisdb->havePieceUnit = request()->havePieceUnit;
                    $efrisdb->itemCode = request()->itemCode;
                    $efrisdb->otherPrice = request()->otherPrice;
                    $efrisdb->otherScaled = request()->otherScaled;
                    $efrisdb->otherUnit = request()->otherUnit;
                    $efrisdb->packageScaleValue = request()->packageScaledValue;
                    $efrisdb->packageScaled = request()->packageScaled;
                    $efrisdb->pieceMeasureUnit = request()->pieceMeasureUnit;
                    $efrisdb->pieceScaledValue = request()->pieceScaledValue;
                    $efrisdb->pieceUnitPrice = request()->pieceUnitPrice;
                    $efrisdb->pieceScaledValue = request()->piece_scaled_value;
                    $efrisdb->stockStatus = request()->stockStatus;
                    $efrisdb->registration_status = 1;
                    if ($efrisdb->save()) {
                        return response()->json(['status' => 'SUCCESS', 'payload' => 'Item successfully synched with local database']);
                    } else {
                        return response()->json(['status' => 'FAIL', 'payload' => 'Sorry there was a problem updating your local']);
                    }
                } else {
                    $efrisItem = EfrisItem::createEfrisProduct($form, $qb['Item']);

                    //prepare request payload
                    $response = $efris->makePost('register-product', ['products' => [$efrisItem]]);
                    $data = json_decode($response, true);
                    QuickBooksServiceHelper::logToFile($data);
                    // check the return code
                    if (is_array($data)) {
                        $returnCode = $data['status']['returnCode'];
                        $returnMessage = $data['data'];
                        if ($returnCode != '00') {
                            return response()->json(['status' => 'FAIL', 'msg' => $returnMessage], 202);
                        }

                        QuickBooksServiceHelper::logToFile($data);
                        //Prepare details to save to the DB
                        $efrisdb_check = EfrisItem::where('id', request()->Id)->first();
                        try {
                            if (!$efrisdb_check) {
                                $efrisdb = new EfrisItem;
                                $efrisdb->id = request()->Id;
                                $efrisdb->commodityCategoryId = request()->commodityCategoryId;
                                $efrisdb->currency = request()->currency;
                                $efrisdb->exciseDutyCode = request()->exciseDutyCode;
                                $efrisdb->hasOpeningStock = request()->hasOpeningStock;
                                // $efrisdb->haveExciseDuty =request()->haveExciseDuty;
                                $efrisdb->haveExciseTax = request()->haveExciseTax;
                                $efrisdb->haveOtherUnit = request()->haveOtherUnit;
                                $efrisdb->havePieceUnit = request()->havePieceUnit;
                                // $efrisdb->isRegisteredInEfris =request()->isRegisteredInEfris;
                                $efrisdb->itemCode = request()->itemCode;
                                $efrisdb->otherPrice = request()->otherPrice;
                                $efrisdb->otherScaled = request()->otherScaled;
                                $efrisdb->otherUnit = request()->otherUnit;
                                $efrisdb->packageScaleValue = request()->packageScaledValue;
                                $efrisdb->packageScaled = request()->packageScaled;
                                $efrisdb->pieceMeasureUnit = request()->pieceMeasureUnit;
                                $efrisdb->pieceUnitPrice = request()->pieceUnitPrice;
                                $efrisdb->pieceScaledValue = request()->pieceScaledValue;
                                $efrisdb->unitOfMeasure = request()->unitOfMeasure;
                                $efrisdb->stockStatus = request()->stockStatus;
                                $efrisdb->registration_status = 1;
                                $efrisdb->save();

                                return response()->json(['status' => 'SUCCESS', 'msg' => $returnMessage], 200);
                            }
                        } catch (Exception $e) {
                            QuickBooksServiceHelper::logToFile($e->getMessage());
                        }

                        QuickBooksServiceHelper::logToFile($returnMessage);
                    } else {
                        if ($data->status->returnCode == '00') {
                            if ($form['hasOpeningStock'] == 101) { //Do we have opening stock?
                                //Sync products
                                $efris->makePost('sync-products', []);
                                $stock = EfrisItem::prepareOpeningStockObject($form);
                                $stockIn_response = $efris->makePost('increase-stock', $stock);

                                return response()->json(['status' => 'success', 'msg' => $stockIn_response]);
                            }

                            //Prepare details to save to the DB
                            $efrisdb = new EfrisItem;
                            $efrisdb->id = request()->Id;
                            $efrisdb->commodityCategoryId = request()->commodityCategoryId;
                            $efrisdb->currency = request()->currency;
                            $efrisdb->exciseDutyCode = request()->exciseDutyCode;
                            $efrisdb->hasOpeningStock = request()->hasOpeningStock;
                            $efrisdb->haveExciseDuty = request()->haveExciseDuty;
                            // $efrisdb->haveExciseTax =request()->haveExciseTax;
                            $efrisdb->haveOtherUnit = request()->haveOtherUnit;
                            $efrisdb->havePieceUnit = request()->havePieceUnit;
                            $efrisdb->isRegisteredInEfris = request()->isRegisteredInEfris;
                            $efrisdb->itemCode = request()->itemCode;
                            $efrisdb->otherPrice = request()->otherPrice;
                            $efrisdb->otherScaled = request()->otherScaled;
                            $efrisdb->otherUnit = request()->otherUnit;
                            $efrisdb->packageScaleValue = request()->packageScaledValue;
                            $efrisdb->packageScaled = request()->packageScaled;
                            $efrisdb->pieceMeasureUnit = request()->pieceMeasureUnit;
                            $efrisdb->pieceScaledValue = request()->pieceScaledValue;
                            $efrisdb->pieceUnitPrice = request()->pieceUnitPrice;
                            $efrisdb->piecescaledValue = request()->piece_scaled_value;
                            $efrisdb->stockStatus = request()->stockStatus;
                            $efrisdb->registration_status = 1;
                            $efrisdb->save();
                            if ($efrisdb->save()) {
                                return response()->json(['status' => 'SUCCESS', 'msg' => 'Product Successfully Registered with URA'], 200);
                            } else {
                                return \response()->json(['status' => 'FAIL', 'msg' => 'Sorry  there was a problem updating your local db'], 200);
                            }

                            //Save without validation
                        } elseif ($data->status->returnCode != '00') {
                            return \response()->json(['status' => 'FAIL', 'msg' => $data->data], 200);
                        }
                    }
                }

        } catch (\Throwable $th) {
            return response()->json(['status' => 'FAIL', 'payload' => $th->getMessage()]);
        }
    }

    /**
     * @throws SdkException
     * @throws IdsException
     */
    public function openingStock($id)
    {
        $item = $this->itemDetails($id);
        $units = (new ApiRequestHelper('efris1'))->makeGet('master-data');

        return Inertia::render(
            'Components/RegisterOpeningStock',
            [
                'item' => json_decode($item),
                'measureunit' => json_decode($units),
                'itemId' => $id,
            ]
        );
    }

    public function registerProductView($id)
    {
        $item = $this->itemDetails($id);
        $item_dec = json_decode($item, true);

        $master_data = File::get(public_path('data/master-data.json'));
        $efris_master_data = json_decode($master_data, true);
        if ($item_dec) {
            if (array_key_exists('fault', $item_dec)) {
                $error_body = $item_dec['fault']['error'][0];
                if ($error_body['code'] == '3200') {
                    return Inertia::render('Integrator', ['url' => $this->qbo_url()]);
                }
            } else {
                return Inertia::render('Components/RegisterProduct', [
                    'item' => json_decode($item),
                    'masterData' => $efris_master_data
                ]);
            }
        }
    }

    /**
     * @throws SdkException
     * @throws IdsException
     */
    public function registerOpeningStock(Request $request, $id)
    {
        $efris_api = new ApiRequestHelper('efris1');

        $item = $this->itemDetails($id);
        $posted = request()->post();

        //Sync products
        $efris_api->makePost('sync-products', []);
        $stock = EfrisItem::prepareOpeningStockObject($posted);
        $stockIn_response = $efris_api->makePost('increase-stock', $stock);
        $feedback = json_decode($stockIn_response);

        if ($feedback->status->returnCode != '00') {
            return response()->json([
                'status' => 'FAIL', 'payload' => $feedback->data,
            ], 200);
        } else {
            //Prepare details to update DB
            $efrisdb = EfrisItem::where(['id' => intval($id)])->first();
            $efrisdb->stockin_date = $request->stockin_date;
            $efrisdb->stockin_quantity = $request->stockin_quantity;
            $efrisdb->stockin_measureUnit = $request->stockin_measureUnit;
            $efrisdb->stockin_purchase_cost = $request->stockin_purchase_cost;
            $efrisdb->stockin_supplier_tin = $request->stockin_supplier_tin;
            $efrisdb->stockin_supplier = $request->stockin_supplier;
            $efrisdb->stockStatus = $request->stockStatus;
            $efrisdb->stockin_price = $request->stockin_price;
            $efrisdb->itemCode = $request->itemCode;

            if ($efrisdb->update()) {
                return response()->json(['status' => 'SUCCESS', 'payload' => 'Opening stock successfully registered'], 202);
            } else {
                return response()->json(['status' => 'FAIL', 'payload' => 'Sorry  there was a problem updating your local db'], 200);
            }
        }
    }

  


}
