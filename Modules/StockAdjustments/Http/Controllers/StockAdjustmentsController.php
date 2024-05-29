<?php

namespace Modules\StockAdjustments\Http\Controllers;

use App\Facades\UtilityFacades;
use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use App\Models\VendorCredit;
use App\Services\ApiRequestHelper;
use App\Services\QuickBooksServiceHelper;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\StockAdjustments\Http\Services\StockAdjustmentsServices;

class StockAdjustmentsController extends Controller
{
    public function index()
    {
        $data = StockAdjustment::paginate(100);
        $reasons = [
            101 => 'Expired Goods',
            102 => 'Damaged Goods',
            103 => 'Personal Uses',
            104 => 'Raw Materials',
            // 105 => 'Hotel Service',
            // 106 => 'Other Taxes',
            105 => 'Others (Please specify)',
        ];

        return view('stockadjustments::index',compact('data','reasons'));
    }


    public function stockData()
    {
        try {
            $data = StockAdjustment::paginate(100);

            return response()->json(['status' => true, 'payload' => $data], 200);
        } catch (\Throwable $throwable) {
            return response()->json($throwable->getMessage());
        }
    }

    public function updateStockADType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'reason' => 'required',
            'transaction_ids' => 'required|array',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return response()->json(['status' => 'fail', 'payload' => $errors->all()],201);
        }

        try {
            StockAdjustment::whereIn('transact_id', $request->transaction_ids)->update([
                'adjust_type' => $request->type,
                'adjust_reason' => $request->reason,
            ]);

          return response()->json(['status' => 'success', 'payload' => 'Records successfully updated'],200);
        } catch (\Throwable $throwable) {
            QuickBooksServiceHelper::logToFile($throwable->getMessage());
        }

        return redirect()->back()->with('success', 'Records successfully updated');
    }

    public function actionVendorCredit($validate = 'no')
    {
        try {
            $vcredits = $this->urlQueryBuilderAll('VendorCredit');

            if (! is_null($vcredits)) {
                //Saved new records to the local DB
                if ($validate == 'yes') {
                    $jsonPurchase = json_decode($vcredits);
                    //                dd($jsonPurchase);
                    if ($jsonPurchase->QueryResponse->VendorCredit) {
                        //Number of records updated
                        $records_updated = 0;
                        foreach ($jsonPurchase->QueryResponse->VendorCredit as $vc) {
                            //Do we have this record?
                            if (! VendorCredit::where('id', $vc->Id)->exists()) {
                                $vendcredit = new VendorCredit();
                                $vendcredit->id = $vc->Id;
                                $vendcredit->fiscal_status = 0;
                                $vendcredit->created_at = time();
                                $vendcredit->transaction_date = $vc->TxnDate;
                                $vendcredit->total_amount = $vc->TotalAmt;
                                $vendcredit->balance = $vc->Balance;
                                $vendcredit->doc_number = $vc->DocNumber;
                                $vendcredit->adjust_reason = property_exists($vc, 'PrivateNote') ? ($vc->PrivateNote) : ('');
                                if ($vendcredit->save()) {
                                    $records_updated += 1;
                                }
                            }
                            //If the records exist, but havent been fiscalised, update it
                            if (VendorCredit::where(['id' => $vc->Id, 'fiscal_status' => 0])->exists()) {
                                $vendorcredit = VendorCredit::find($vc->Id);
                                $vendorcredit->total_amount = $vc->TotalAmt;
                                $vendorcredit->balance = $vc->Balance;
                                $vendorcredit->adjust_reason = property_exists($vc, 'PrivateNote') ? ($vc->PrivateNote) : ('');
                                if ($vendorcredit->save()) {
                                    $records_updated += 1;
                                }
                            }
                        }
                        session()->flash('success', "Successfully synced vendor credits. {$records_updated} saved to local database. ");
                    }
                }

                //All Records in the DB
                $dbVcredits = VendorCredit::all()->keyBy('id');
                //Render

                return response()->json(['qbRecords' => $vcredits['QueryResponse'], 'dbRecords' => $dbVcredits]);
            }

            return response()->json(['qbRecords' => [], 'dbRecords' => []]);
        } catch (\Throwable $throwable) {
            return response()->json($throwable->getMessage());
        }
    }

    /**
     * Synch Stock Reduction with URA
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function actionReduceStock($id, $src = 'stock')
    {
        //        expected
        //        $src='stock' or src == 'vcredit',id
        //        $id = $request->id;
        //        $src = $request->src;

        if ($src == 'vcredit') {
            $stock = VendorCredit::prepareEfrisRequestObject($id);
        } else {
            $stock = StockAdjustment::prepareEfrisRequestObject($id);
            //  QuickBooksHelper::logToFile($stock,'Reduce stock');
        }
        // QuickBooksHelper::logToFile($stock,'Reduce stock- Stock');
        //2. Send Request to URA
        $efris = new ApiRequestHelper('efris1');
        $response = $efris->makePost('decrease-stock', $stock);

        //3. Handle Response
        $feedback = json_decode($response);
        //changed success code 200
        if ($feedback->status->returnCode == '00') {
            //Update DB
            $ids = explode(',', $id);
            if ($src == 'vcredit') {
                //Send Feedback to user
                VendorCredit::whereIn('id', $ids)->update([
                    'fiscal_status' => 1,
                ]);

                return redirect()->back()->with('success','Supplier Credit successfully synched with URA');
            } else {
                //Send Feedback to user
                StockAdjustment::where('transact_id', $id)->update(['ura_sync_status' => 1]);

                return redirect()->back()->with('success', 'Stock reduction successfully synched with URA');
            }
        } else {
            //Send Feedback to user
            $errors = '';
            $count = 0;
            //            dd($feedback);
            if ($feedback->status->returnCode != '00') {
                return redirect()->back()->with('success',  $feedback->status->returnMessage);
            }
            //            if (!is_null($feedback->data)) {
            //                return response()->json(['status' => 'SUCCESS', 'payload' => $feedback->data]);
            //            } else {
            //                $errors = 'There was a problems.' . $feedback->status->returnMessage;
            //            }

            return response()->json(['status' => 'FAIL', 'payload' => $errors]);
        }
    }

  public function sync()
  {
    try {
      $sync = request()->get('sync', 'yes');
      // Synchchonise records to the local DB
//      $test = (new StockAdjustmentsServices())->actionSyncStockAdjustment();

        $decreaseResponse = (new self())->queryString('/reports/GeneralLedger?date_macro=This Fiscal Year&summarize_column_by=ProductsAndServices&columns=quantity,rate,tx_date,txn_type,doc_num,item_name&minorversion=57');

        $items = json_decode(json_encode($decreaseResponse));



        $col = [];
        $row = [];
      $stock_adjust = [];

      $stock_keyword =(UtilityFacades::getsettings('stock_adjust_keyword') != "") ? UtilityFacades::getsettings('stock_adjust_keyword') : "Stock Qty Adjust";

      if (!is_null($items)) {

        foreach ($items->Rows->Row as $item) {
          // first row
          $row = $item->Rows->Row;
          foreach ($row as $colData) {
            if (property_exists($colData, 'ColData')) {
              $col = $colData->ColData;
              if (is_array($col)) {
                foreach ($col as $data) {
                  if ($data->value == "Stock Qty Adjust") {
                    $stock_adjust[] = $col;
                  }
                }
              }
            }
          }
        }
//          dd($stock_adjust);
        // Save valid records to the DB
//        $stock_adjust = $list;
        $adjust_items = [];

        foreach ($stock_adjust as $stock) {
          // Create an instance of the a stock adjustment object
          $stockadj = new StockAdjustment;
          $stockadj->transact_id = $stock[2]->value;
          $stockadj->transact_date = $stock[0]->value;
          $stockadj->item_name = $stock[3]->value;
          $stockadj->item_id = $stock[3]->id;
          $stockadj->quantity = intval($stock[4]->value);
          $stockadj->unit_price = $stock[5]->value;
          $stockadj->adjust_type = 0;
          $stockadj->ura_sync_status = 0;

          // Build a list of [StockAdjustment] Objects
          // 1. Is the quantity negative?
          // 2. Is the quantity positive?
          // 3. This item is not deleted
          if (intval($stockadj->quantity) < 0 && $stockadj->unit_price > 0 && ! str_contains($stockadj->item_name, '(deleted)')) {
            $adjust_items[] = $stockadj;
          }
        }

//        dd($adjust_items);

        $collect = collect($adjust_items)->toArray();

        if (count($collect) > 0) {
          // Bulk Insert
          Log::info('Recording '.count($adjust_items).' records to the DB...');
          try {
            // Save valid records to the DB
//            $stock_adjust = $stock_adjust;
            $adjust_items = [];
            for ($i = 0; $i < count($stock_adjust); $i++) {
              // Create an instance of the a stock adjustment object
              $stockadj = new StockAdjustment;
              $stockadj->transact_id = $stock_adjust[$i][2]->value;
              $stockadj->transact_date = $stock_adjust[$i][0]->value;
              $stockadj->item_name = $stock_adjust[$i][3]->value;
              $stockadj->item_id = $stock_adjust[$i][3]->id;
              $stockadj->quantity = intval($stock_adjust[$i][4]->value);
              $stockadj->unit_price = $stock_adjust[$i][5]->value;
              $stockadj->adjust_type = 0;
              $stockadj->ura_sync_status = 0;

              // Build a list of [StockAdjustment] Objects
              // 1. Is the quantity negative?
              // 2. Is the price positive?
              // 3. This item is not deleted
              if (intval($stockadj->quantity) < 0 && $stockadj->unit_price > 0 && ! strpos($stockadj->item_name, '(deleted)')) {
                $adjust_items[] = $stockadj;
              }
            }

            // Bulk Insert or Update
            if (! empty($adjust_items)) {
              DB::transaction(function () use ($adjust_items) {
                foreach ($adjust_items as $adjustment) {
                  StockAdjustment::updateOrInsert(
                    ['transact_id' => $adjustment->transact_id],
                    [
                      'transact_date' => $adjustment->transact_date,
                      'item_name' => $adjustment->item_name,
                      'item_id' => $adjustment->item_id,
                      'quantity' => $adjustment->quantity,
                      'unit_price' => $adjustment->unit_price,
                      'adjust_type' => $adjustment->adjust_type,
                      'ura_sync_status' => $adjustment->ura_sync_status,
                    ]
                  );
                }
              });
            }

            if (empty($adjust_items)) {
              return redirect()->back()->with('failed','No stock adjustment data found in quickbooks');
            } else {
              return redirect()->back()->with('success','Records syncing run successfully');
            }

          } catch (\Throwable $throwable) {
            QuickBooksServiceHelper::logToFile($throwable->getMessage());
          }
        }
        return redirect()->back()->with('failed','No stock adjustment data found in quickbooks');
      }


      // Pick DB Records
      $searchModel = StockAdjustment::query()->search(request()->query());
      //        $dataProvider = $searchModel->search(request()->query());

      //        return view('decrease', [
      //            'searchModel' => $searchModel,
      //            'dataProvider' => $dataProvider,
      //        ]);
      return redirect()->back()->with('fail','No Quickbooks data found for stock adjustments');
    } catch (\Exception $e) {
      return redirect()->back()->with('failed',$e->getMessage());
    }
  }



}
