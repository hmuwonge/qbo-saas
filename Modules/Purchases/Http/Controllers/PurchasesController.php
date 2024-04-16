<?php

namespace Modules\Purchases\Http\Controllers;

use App\Services\QBOServices\QuickbooksApiClient;
use App\Services\QuickBooksServiceHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\QuickBooksPurchase;
use App\Http\Controllers\Controller;
use App\Services\EfrisPurchaseService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Log;

class PurchasesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $stockInTypes = [
            101 => 'Import',
            102 => 'Local Purchase',
            103 => 'Manufacture'
        ];

        // get company tin

        $all_purchases = QuickbooksApiClient::queryPurchasesData1000();
      $purchases =  collect($all_purchases)->paginate(10);
      $dbPurchases = QuickBooksPurchase::all()->keyBy('id');
//        $purchases_data = json_decode(json_encode($all_purchases),false);

//        foreach ($purchases_data as $ph) {
//            // Do we have this record?
//            if (!QuickBooksPurchase::where('id', $ph->Id)->exists()) {
//                $purch = new QuickBooksPurchase;
//                $purch->id = $ph->Id;
//                $purch->uraSyncStatus = 0;
//                $purch->save();
//            }
//        }

//      foreach ($all_purchases as  $purchase){
//        // Access the purchase ID as before
//        $purchaseId = $purchase['Id'];
//
//// Check if the purchase ID exists in the array
//        if (array_key_exists($purchaseId, $dbPurchases->toArray())) {
//          // If a match is found, call the function
//          dd('if match found');
//        } else {
//          // If no match is found, handle the unmatched element
//          echo "Unmatched purchase ID: $purchaseId";
//        }
//      }

        return view('purchases::index', compact('purchases', 'dbPurchases', 'stockInTypes'));

    }


    public function updatePurchaseStockInType(Request $request)
    {
        $data = $request->purchases;
        QuickBooksPurchase::whereIn('id', $data['id'])->update([
            'stockInType' => $data['stockIn'],
        ]);

        return response()->json(['status'=>true,'payload'=>'updated successfully']);
    }

    /**
     * Incerase stock with URA
     *
     * @param  int  $id
     */
    public function increasePurchaseStock($id): JsonResponse|RedirectResponse
    {
        try {
            $data = EfrisPurchaseService::actionIncreaseStock($id);
            Log::info("purchaeses", $data);

            //All Records in the DB

            if ($data['status']['returnCode'] != '00') {
              return response()->json(['status'=>false,'payload'=>"data:". " ". $data['data']]);
            } elseif ($data['status']['returnCode'] == '00') {
                $purchItem = QuickbooksPurchase::find($id);
                $purchItem->uraSyncStatus = 1;
                    $purchItem->update();

//                    return redirect()->back()->with('success', __('Increased Stock successfully'));
              return response()->json(['status'=>true,'payload'=>'Increased Stock successfully']);

            } else {
                return redirect()->back();
            }
        } catch (\Throwable $th) {
          return response()->json(['status'=>false,'payload'=>__($th->getMessage())]);
        }
    }
}
