<?php

namespace Modules\CreditMemos\Http\Controllers;

use App\Models\CreditMemo;
use App\Models\EfrisInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Services\ApiRequestHelper;
use Illuminate\Support\Facades\Session;
use App\Services\QuickBooksServiceHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\Renderable;

class CreditMemosController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($validate = 'no')
    {
        ini_set('memory_limit', '4096M'); //Allow up to 2GB for this action
        $credit_notes = $this->urlQueryBuilderAll('CreditMemo');
        //    dd($credit_notes);

        $invoiceStatus = CreditMemo::all()->keyBy('id');

        $credits = (new Collection($credit_notes['QueryResponse']['CreditMemo']))->paginate(100);


        // Should we run validations?
        if ($validate == 'yes') {
            $all_credits = json_decode((json_encode($credit_notes)));

            if (count($all_credits) > 0) { //if we have some records, this has been tested and working fine
                // Save Validation Errors to the DB
                foreach ($all_credits as $inv) {
                    EfrisInvoice::saveInvoiceSummary($inv->Id, $inv);
                }

                session()->flash('success', 'successfully validated credit notes data from quickbooks');
            } else {
                session()->flash('warning', 'There are no Credit Memos in your QuickBooks account at the moment');
            }
            // Go to the View
            //            return Inertia::render('Quickbooks/CreditMemos/CreditMemos', ['credits' => $collection, 'invoiceStatus' => $invoiceStatus]);
        } else {
            //    dd($credits);

            // Go to the View
            return view('creditmemos::index', compact('credits', 'invoiceStatus'));
        }
        // return view('creditmemos::index',compact('credits','invoiceStatus'));
    }

    /**
     * Link CreditNote to an Invoice
     */
    public function actionLinkCreditNote(Request $request)
    {
        request()->validate([
            'creditnoteId' => 'required',
            'invoiceFdn' => 'required',
            'creditnoteRef' => 'required',
            'unlink' => 'nullable',
        ]);
        $creditMemo = CreditMemo::find($request->creditnoteId);

        // check if we need to unlink the invoice from the credit note
        if ($request->unlink == 'yes'){
          $creditMemo->invoice_fdn = null;
          $creditMemo->update();
          return redirect()->back()->with('success', $request->invoiceFdn . 'Successfully unlinked from a  credit note');
        }

// link credit note to invoice
        if ($creditMemo) {
            $creditMemo->invoice_fdn = $request->invoiceFdn;
            $creditMemo->update();
        } else {
            try {
                $note = new CreditMemo;
                $note->id = $request->creditnoteId;
                $note->invoice_fdn = $request->invoiceFdn;
                $note->fiscalStatus = 0;
                $note->qb_refNumber = $request->creditnoteRef;
                $note->record_type = $request->crednoteType;

                QuickBooksServiceHelper::logToFile($note);
                $note->save();
            } catch (\Throwable $throwable) {
                return $throwable->getMessage();
            }
        }
        //            CreditMemo::linkInvoice($request->creditnoteId, $request->invoiceFdn, $request->creditnoteRef, $request->crednoteType);

        if (request()->crednoteType == 'CN') {
            return redirect()->back()->with('success', $request->invoiceFdn . ' '. ' successfully linked to a credit note');
        } else {
            return redirect()->back()->with('success', $request->invoiceFdn . ' successfully linked to a receipt refund');
        }
        //        } catch (\Exception $e) {
        //            return \response()->json(['status' => 'FAIL', 'payload' => $e->getMessage()]);
        //        }
    }

    /**
     * Credit Notes
     *
     * @param  int  $validate
     */
  public function actionCreditMemos()
  {
    ini_set('memory_limit', '4096M'); //Allow up to 2GB for this action
    $credit_notes = $this->urlQueryBuilderAll('CreditMemo');
    $items = $credit_notes['QueryResponse']['CreditMemo'];
    $all_credits = json_decode((json_encode($items)));

    if (count($all_credits) > 0) { //if we have some records, this has been tested and working fine
      // Save Validation Errors to the DB
      foreach ($all_credits as $item) {
        $creditMemo = CreditMemo::find($item->Id);
        if (!$creditMemo){
          $note = new CreditMemo;
          $note->id = $item->Id;
          $note->fiscalStatus = 0;
          $note->qb_refNumber = $item->DocNumber;
          $note->record_type = 'CN';
          $note->save();
        }
      }

      return redirect()->back()->with('success', 'successfully validated credit notes data from quickbooks');
    } else {
      return redirect()->back()->with('warning', 'No credit notes found');
    }
  }


     public function fiscaliseCreditnote($id)
     {
         $creditmemo = CreditMemo::findOrFail($id);

         $reasons = [
             101 => 'Return of products due to expiry or damage, etc',
             102 => 'Cancellation of the purchase',
             103 => 'Invoice amount wrongly stated due to miscalculation of price, tax, or discounts, etc',
             104 => 'Partial or complete waive off of the product sale after the invoice is generated and sent to customer',
             105 => 'Others (Please specify)',
         ];

         // checking if original invoice has been attached
         if (isset($creditmemo->originalInvoice()->data->basicInformation)) {
             // dd('test');
             return view('creditmemos::FiscaliseCreditNote', compact('creditmemo','reasons'));
         } else {
             return redirect()->back()->with('warning', 'Try relinking Creditnote to original FDN');
         }
     }

    public function sendFiscaliseCreditnote()
    {
      $inv = request()->all();

      $id = $inv['CreditMemo']['id'];

      try {
        $efris_data = CreditMemo::buildEfrisRequestObject($inv);

        $efris = new ApiRequestHelper('efris1');
        $response = $efris->makePost('apply-for-creditnote', $efris_data);
        $feedback = json_decode($response);

        QuickBooksServiceHelper::logToFile($feedback);
        //Response
        if ($feedback->status->returnCode != '00') {
          return response()->json(['status' => 'FAIL', 'payload' => $feedback->status->returnMessage]);
        } else {
          $invoiceItem = CreditMemo::findOrFail($id);
          $invoiceItem->fiscalStatus = 1;
          $invoiceItem->efris_refNumber = $feedback->data->referenceNo;

          try {
            $invoiceItem->save();

            return response()->json(['status' => 'SUCCESS', 'payload' => 'Credit Note successfully fiscalised']);
          } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['status' => 'FAIL', 'payload' => $e->getMessage()]);
          }
        }
      } catch (\Throwable $th) {
        return response()->json(['status' => 'FAIL', 'payload' => $th->getMessage()]);
      }
    }
}
