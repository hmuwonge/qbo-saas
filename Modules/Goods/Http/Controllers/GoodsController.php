<?php

namespace Modules\Goods\Http\Controllers;

use App\Models\StockAdjustment;
use Mockery\Exception;
use App\Models\EfrisItem;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Services\ApiRequestHelper;
use Illuminate\Support\Collection;
use App\Services\EfrisItemsService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Services\QuickBooksServiceHelper;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Pagination\LengthAwarePaginator;
use QuickBooksOnline\API\Exception\IdsException;
use QuickBooksOnline\API\Exception\SdkException;

class GoodsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        //         try {
        // Sync products
        $efris = new ApiRequestHelper('efris1');

        $prevCount = $request->input('prevCount', 0);
        $page = $request->input('page', 1);
        $code = $request->input('name');
        $cname = $request->input('code');

        $total = null;
        $limit = 10;
        $startPosition = intval($page - 1) * $limit;

        // Quickbooks Items
      $countQuery =  "SELECT count(*) FROM Item";
      $quickbooks_invoices_count = $this->postQuery($countQuery);
      $totalRecords=$quickbooks_invoices_count['QueryResponse']['totalCount'];

        $queryString = "SELECT * FROM Item startposition {$startPosition} maxresults {$limit}";
        $quickbooks_items = $this->postQuery($queryString);


        if (request()->has('q')){
            $new_query = request()->input('q');
            $query = "SELECT * FROM Item WHERE FullyQualifiedName LIKE '%" . $new_query . "%'";

            $quickbooks_items = (new self())->postQuery($query);

            $totalRecords = $quickbooks_items['QueryResponse']['maxResults']??[];
        }
         // JSON decode
         $itemsQuery = json_decode(json_encode($quickbooks_items['QueryResponse']['Item']),true);

        $paginator = new LengthAwarePaginator($itemsQuery, (int)$totalRecords,  $limit);
        $paginator->setPath(route('goods.all'));
        $qb_items = $paginator->items();

        // URA Items
        $efris_api = new ApiRequestHelper('efris1');
        $efris_response = $efris_api->makePost('goods-and-services', []);
        $ef_items = json_decode($efris_response, true);

      if ($ef_items['status']['returnCode'] == '999'){
        return redirect()->back()->with('failed', $ef_items['status']['returnMessage']);
      }
        if ($ef_items['status']['returnCode'] !== '00'){
          return view('noitems');
        }


        // List of Synced Items
        $efrisItems = EfrisItem::all()->keyBy('id');

        if ($ef_items !== null) {
            $efris_indexed = Arr::keyBy($ef_items['data']['records'], 'goodsCode');
            $from_data_table = QuickBooksServiceHelper::prepareItemList($efrisItems, $efris_indexed, $qb_items);
            $collection = collect($from_data_table);

            $data_table = $collection->toArray();

            $data['items'] = $data_table;
            $data['qbitems'] = $qb_items;
            $data['efrisItems'] = $efris_indexed;
            $data['page'] = $page;
            $data['totalFetched'] = $total;
            $data['links'] =$paginator->links();

            if (!is_null($qb_items)) {
                $collection = new Collection($data['items']);
                $data = $collection;
                $links = $paginator->links();
                return view('goods::index', compact('data', 'links'));

            } else {
                //                // URA Items
                return redirect()->route('dashboard.integrator');
            }
        } else {
            return view('noitems');
        }
    }

    /** method for registering product's opening stock
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
            return redirect()->back()->with('failed', $feedback->data);
        } else {
            //Prepare details to update DB
            $efrisdb = EfrisItem::where(['id' => intval($id)])->first();
            $efrisdb->stockin_date = $request->stockInDate;
            $efrisdb->stockin_quantity = $request->stockinQuantity;
            $efrisdb->stockin_measureUnit = $request->stockinMeasureUnit;
            $efrisdb->stockin_purchase_cost = $request->stockinPrice;
            $efrisdb->stockin_supplier_tin = $request->stockinSupplierTin;
            $efrisdb->stockin_supplier = $request->stockInsupplier;
            $efrisdb->stockStatus = $request->stockStatus;
            $efrisdb->stockin_price = $request->stockinPrice;
            $efrisdb->itemCode = $request->itemCode;

            if ($efrisdb->update()) {
                return redirect()->back()->with('success', 'Opening stock successfully registered');
            } else {
                return redirect()->back()->with('failed', 'Sorry  there was a problem updating your local db');
            }
        }
    }

    /**
     * @throws SdkException
     * @throws IdsException
     */
    public function registerOpeningStockView($id)
    {
        $item_details = $this->itemDetails($id);
        $units = (new ApiRequestHelper('efris1'))->makeGet('master-data');
        $item = json_decode($item_details);
        $measureunit = json_decode($units);
        $itemId = $id;

        return view(
            'goods::RegisterOpeningStock',
            compact('item', 'measureunit', 'itemId')
        );
    }

    public function actionItemProductDetails($id)
    {
        $get_qbo_data = $this->urlQueryBuilderById('item', $id);
        $product = EfrisItem::findorFail($id);
        $item = json_decode(json_encode($get_qbo_data))->original;

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

        $unitOfMeasure = $Unit;
        $data = $item->Item;

        return view('goods::ProductDetails', compact('data', 'product',  'unitOfMeasure', 'Curr'));
    }

    /**
     * Sync LocalDatabase with EFRIS platform
     * https://laravel.com/docs/9.x/helpers
     */
    public function syncItems()
    {
        return (new EfrisItemsService)->syncItems();
    }

    public function  registerProductView($id)
    {
        return view('goods::RegisterProduct');
    }

  /**
   * @throws SdkException
   * @throws IdsException
   */
  public function registerProduct($id, $redo = 'no')
  {
    $item = $this->itemDetails($id);
      QuickBooksServiceHelper::logToFile('posted',json_encode($item));
      $efris = EfrisItem::find($id);

//      if ($efris == null) {
//        // If the record doesn't exist, create a new one
//        $efris = new EfrisItem;
//        $efris->id = $id;
////                $efris->id = $id;
//      }
//      $efris->currency = 101;
//      $efris->save();

      $efrisApi = new ApiRequestHelper('efris');
      $masterData = $efrisApi->makeGet('master-data');

      $qbItem = json_decode($item);
//            $efrisItem = $efrisApi->makePost('goods-and-services', ['goodsCode' => @$qbItem->Item->Sku]);

      return view('goods::RegisterProduct', [
        'item' => $qbItem,
        'efris' => $efris,
//                'efrisItem' => json_decode($efrisItem)->data->records,
        'masterdata' => json_decode($masterData),
        'redo' => $redo
      ]);
  }

    public function registerEfrisProduct($id, $redo = 'no')
    {
        $item = $this->itemDetails($id);
            $efris = new ApiRequestHelper('efris1');
            $posted = request()->all();
            $qb = json_decode($item, true);
            $form = $posted;
//            dd($posted);

            $isEfrisRegistered = request()->isRegisteredInEfris;

//            this is for re-registering an alreday registered product
            if ($isEfrisRegistered == "yes") {
                $efrisdb = EfrisItem::find($id);

                if ($efrisdb){
                    try {
                        $efrisdb->unitOfMeasure = request()->unitOfMeasure;
                        $efrisdb->stockStatus = request()->stockinQuantity;
                        $efrisdb->itemCode = request()->itemCode;
                        $efrisdb->commodityCategoryId = request()->commodityCategoryId;
                        $efrisdb->havePieceUnit = request()->havePieceUnit;
                        $efrisdb->haveExciseTax = request()->haveExciseTax;
                        $efrisdb->pieceUnitPrice = request()->pieceUnitPrice;
                        $efrisdb->stockin_price = request()->stockinPrice;
                        $efrisdb->pieceScaledValue = request()->pieceScaledValue;
                        $efrisdb->pieceMeasureUnit = request()->pieceMeasureUnit;
                        $efrisdb->packageScaledValue = request()->packageScaledValue;
                        $efrisdb->item_tax_rule = request()->itemTaxRule??'URA';
                        $efrisdb->haveExciseDuty = request()->haveExciseDuty;
                        $efrisdb->haveOtherUnit = request()->haveOtherUnit;
                        $efrisdb->hasOpeningStock = request()->hasOpeningStock;
                        $efrisdb->stockin_supplier_tin = request()->stockinSupplierTin;
                        $efrisdb->stockin_supplier = request()->stockInsupplier;
                        $efrisdb->opening_stock_remarks = request()->stockInRemarks;
                        $efrisdb->stockin_date = request()->stockInDate;
                        $efrisdb->stockin_price = request()->stockinPrice;
                        $efrisdb->stockin_quantity = request()->stockinQuantity;
                        $efrisdb->currency = request()->currency;
                        $efrisdb->otherUnit = request()->otherUnit;
                        $efrisdb->otherPrice = request()->otherPrice;
                        $efrisdb->exciseDutyCode = request()->exciseDutyCode;
                        $efrisdb->packageScaled = request()->packageScaled;
                        $efrisdb->otherScaled = request()->otherScaled;

                        if ($efrisdb->update()) {
                            return response()->json(['status'=>'SUCCESS', 'msg'=>'Item successfully synced with the local database']);
                        }
                    }catch (\Throwable $throwable){
                        return response()->json(['status'=>'failed',$throwable->getMessage()]);
                    }

//                    else {
//                        return response()->json(['status'=>'failed', 'msg'=>'Sorry, there was a problem updating your local database']);
//                    }
                }

                return response()->json(['status'=>'SUCCESS', 'msg'=>'Sorry, there was a problem updating your local database']);
            }

//            dd('here');


            $efrisItem = EfrisItem::createEfrisProduct($form, $qb["Item"]);

            $response = $efris->makePost('register-product', ['products' => [$efrisItem]]);
            $data = json_decode($response);

            if ($data->status->returnCode != "00") {
                $returnCode = $data->status->returnCode;
                $returnMessage = $data->data;
                return response()->json(['status'=>'FAIL','msg'=> $returnMessage,$returnCode]);

            } else {

                if ($data->status->returnCode == "00") {
                    if ($form['hasOpeningStock'] == 101) {
                        $efris->makePost('sync-products', []);
                        $stock = EfrisItem::prepareOpeningStockObject($form);
                        $stockInResponse = $efris->makePost('increase-stock', $stock);
                        return response()->json(['status'=>'SUCCESS', 'msg'=>$stockInResponse]);
                    }

                    QuickBooksServiceHelper::logToFile('posted',json_encode($posted));

                    $efrisdb = new EfrisItem;
                    $efrisdb->id = request()->Id;
                    $efrisdb->unitOfMeasure = request()->unitOfMeasure;
                    $efrisdb->stockStatus = request()->stockinQuantity;
                    $efrisdb->itemCode = request()->itemCode;
                    $efrisdb->commodityCategoryId = request()->commodityCategoryId;
                    $efrisdb->havePieceUnit = request()->havePieceUnit;
                    $efrisdb->haveExciseTax = request()->haveExciseTax;
                    $efrisdb->pieceUnitPrice = request()->pieceUnitPrice;
                    $efrisdb->stockin_price = request()->stockinPrice;
                    $efrisdb->pieceScaledValue = request()->pieceScaledValue;
                    $efrisdb->pieceMeasureUnit = request()->pieceMeasureUnit;
                    $efrisdb->packageScaledValue = request()->packageScaledValue;
                    $efrisdb->item_tax_rule = request()->itemTaxRule??'URA';
                    $efrisdb->haveExciseDuty = request()->haveExciseDuty;
                    $efrisdb->haveOtherUnit = request()->haveOtherUnit;
                    $efrisdb->hasOpeningStock = request()->hasOpeningStock;
                    $efrisdb->stockin_supplier_tin = request()->stockinSupplierTin;
                    $efrisdb->stockin_supplier = request()->stockInsupplier;
                    $efrisdb->opening_stock_remarks = request()->stockInRemarks;
                    $efrisdb->stockin_date = request()->stockInDate;
                    $efrisdb->stockin_price = request()->stockinPrice;
                    $efrisdb->stockin_quantity = request()->stockinQuantity;
                    $efrisdb->currency = request()->currency;
                    $efrisdb->otherUnit = request()->otherUnit;
                    $efrisdb->otherPrice = request()->otherPrice;
//            $efrisdb->isRegisteredInEfris = request()->isRegisteredInEfris;
                    $efrisdb->exciseDutyCode = request()->exciseDutyCode;
                    $efrisdb->packageScaled = request()->packageScaled;
                    $efrisdb->otherScaled = request()->otherScaled;
                    $efrisdb->registration_status = 1;
                    if ($efrisdb->save()) {
                        $get_stock_adjustment_item=StockAdjustment::where('item_id',$efrisdb->id)->first();
                        if ($get_stock_adjustment_item){
                            $get_stock_adjustment_item->ura_sync_status = 1;
                            $get_stock_adjustment_item->update();
                        }
                        return response()->json(['status'=>'SUCCESS','msg'=> $data->data]);//'Product Successfully Registered with URA'
                    } else {
                        return redirect()->route('quickbooks.register-product', ['id' => $id])->with('danger', 'Sorry, there was a problem updating your local database');
                    }
                } elseif ($data->status->returnCode != "00") {
                    return response()->json(['status'=>'FAIL','msg'=> $data->data]);
                }
            }

    }

    /**
     * Invoice details (for a given ID)
     *
     * @return string|false
     *
     */
    public function itemDetails($id): bool|string
    {
        $data = $this->urlQueryBuilderById('item', $id);

        return json_encode($data->original);
    }

    public function  syncProducts()
    {
        try {
            $efrisApi = new ApiRequestHelper('efris');
            $response = $efrisApi->makePost('sync-products', (array)null);

            $data_response = json_decode($response);

            if ($data_response->status == 200){
                return redirect()->back()->with('success', 'Products synced successfully!');
            }
        }catch (\Throwable $throwable){
            return redirect()->back()->with('failed', $throwable->getMessage());
        }



    }
}
