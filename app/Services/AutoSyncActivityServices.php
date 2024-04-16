<?php

namespace App\Services;

use App\Models\AutoSync;
use App\Models\AutoSyncActivity;
use App\Models\EfrisInvoice;
use App\Models\QuickBooksInvoice;
use App\Models\QuickBooksPurchase;
use App\Models\StockAdjustment;
use App\Traits\DataServiceConnector;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoSyncActivityServices
{
    use DataServiceConnector;

    public static function createSyncActivity($sync_id, $category, $message, $server_response = null)
    {
        $activity = new AutoSyncActivity;
        $activity->sync_id = $sync_id;
        $activity->msg_category = $category;
        $activity->message = $message;
        $activity->server_response = $server_response;
        $activity->save();

        if ($activity) {
            return response()->json($activity);
        }

        return response()->json('something went wrong');
    }

    /**
     * Validate Bills and purchases
     *
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Response
     */
    public static function validatePurchases()
    {
        // 1. Update all bills which have not yet been synced to EFRIS
        QuickbooksPurchase::where('uraSyncStatus', 0)
            ->update(['stockInType' => 102, 'updated_at' => now()]);

        // 2. Pick records from the DB
        $dbPurchases = QuickbooksPurchase::where('uraSyncStatus', 0)->get();

        // Create an Auto-Sync instance
        $auto = AutoSync::createAutoSync('Validate Bills and Purchases');

        // Go through each record
        $num = 1;
        foreach ($dbPurchases as $purchase) {
            Log::info("\n{$num}. Validating stock No. ".$purchase->id);
            $stock = QuickBooksPurchase::prepareEfrisStockIncrease($purchase->id);
            if (is_array($stock) && is_array($stock['stockInItem'])) {
                $purchItem = QuickBooksPurchase::find($purchase->id);
                $purchItem->validationStatus = 1; // Passed
                $purchItem->update();

                $msg = 'Bill No.'.$purchase->id.' passed validation criteria';
                Log::info("\n\033[32m$msg \033[0m\n");
                AutoSyncActivityServices::createSyncActivity($auto, 'SUCCESS', $msg, '');
            } else {
                if (! is_array($stock)) {
                    $msg = 'Bill/Purchase not found on your Quickbooks account';
                } elseif (! is_array($stock['stockInItem'])) {
                    $msg = 'Stock Items purchased are not registered with URA';
                }

                $purchItem = QuickBooksPurchase::find($purchase->id);
                $purchItem->validationStatus = 0; // Failed
                $purchItem->validationError = $msg;
                $purchItem->update();

                $reason = 'Bill No.'.$purchase->id.' failed validation criteria';

                Log::info("\n\033[31m$reason \033[0m");
                Log::info("\n\033[31m$msg \033[0m");
                AutoSyncActivityServices::createSyncActivity($auto, 'ERROR', $reason, $msg);
            }
            $num++;
        }

        return response()->json(['status' => 'SUCCESS', 'payload' => 'Records successfully validated']);
    }

    /**
     * Fiscalise invoices which are ready...
     */
    public static function fiscaliseInvoices()
    {
        // 1. Pick invoices which can be fiscalised
        // Pick first 1000 and work with that
        $fiscalised = EfrisInvoice::where(['fiscalStatus' => 0, 'validationStatus' => 1, 'invoice_kind' => 'INVOICE'])
            ->limit(1000)
            ->get();

        // Create an Auto-Sync instance
        $auto = AutoSync::createAutoSync('Invoice Fiscalisation');
        Log::info("\nStarting Invoice Fiscalisation #".$auto);
        $num = 1;
        foreach ($fiscalised as $fs) {
            Log::info("\n{$num}. Fiscalizing Invoice REF: ".$fs->refNumber);
            $efris = new ApiRequestHelper('efris1');
            $efris_invoice = QuickBooksInvoice::getFiscalInvoiceAtrributes($fs->id);

            // If this is an array (formatted to create a fiscal invoice)
            if (is_array($efris_invoice)) {
                // Check if all products are registered
                $products = $efris_invoice['itemsBought'];
                // Item codes
                $codes = array_column($products, 'itemCode');

                // Post it if check if one of the items has code='NO_CODE'
                if (! in_array('NO_CODE', $codes)) {
                    $response = $efris->makePost('generate-fiscal-invoice', ['data' => $efris_invoice]);
                    $feedback = json_decode($response);
                    QuickBooksServiceHelper::logToFile($feedback);
                    if (! ($feedback->status) || (@$feedback->status->returnCode != '00')) {
                        $msg = 'Invoice '.$fs->refNumber.' could not be fiscalised';
                        Log::info("\n\033[31m$msg \033[0m");
                        Log::info("\n\033[31m$response \033[0m");
                        AutoSyncActivityServices::createSyncActivity($auto, 'ERROR', $msg, json_decode($response));
                    } else {
                        $invoiceItem = EfrisInvoice::find($fs->id);
                        $invoiceItem->fiscalNumber = $feedback->data->basicInformation->invoiceNo;
                        $invoiceItem->fiscalStatus = 1;
                        $invoiceItem->validationStatus = 1;
                        $invoiceItem->update();

                        $msg = 'Invoice '.$fs->refNumber.' successfully fiscalised';
                        // Success Msg
                        Log::info("\n\033[32m$msg \033[0m\n");
                        AutoSyncActivityServices::createSyncActivity($auto, 'SUCCESS', $msg, '');
                    }
                }
                // </try to fiscalise>
                sleep(2); // Delay Execution by 2 seconds
            } else { // Nothing found
                Log::info($efris_invoice);
                $msg = 'Invoice No '.$fs->refNumber." not found in the client's QB account. We are going ahead to remove it";
                Log::info("\n\033[31m$msg \033[0m");
                EfrisInvoice::find($fs->id)->delete();
                Log::info("\n\033[32mIf this invoice is indeed in the QB account, it can be recovered by synching the records again\033[0m");
            }
            $num++;
        }

        return response()->json(['status' => 'SUCCESS', 'payload' => 'Fiscal checks completed']);
    }

    /**
     * Fiscalise Receipts which are ready...
     */
    public static function fiscaliseReceipts()
    {
        // 1. Pick invoices which can be fiscalised
        // Work on 1000 records at a time
        $fiscalised = QuickBooksInvoice::where(['fiscalStatus' => 0, 'validationStatus' => 1, 'invoice_kind' => 'RECEIPT'])
            ->limit(1000)
            ->get();

        // Create an Auto-Sync instance
        $auto = AutoSync::createAutoSync('Receipt Fiscalisation');
        Log::info("\nStarting Receipt Fiscalisation #".$auto);
        foreach ($fiscalised as $fs) {
            Log::info("\nFiscalising Receipt. ".$fs->refNumber);
            $efris = new ApiRequestHelper('efris1');
            $efris_invoice = QuickBooksInvoice::getFiscalInvoiceAtrributes($fs->id, 'RECEIPT');

            // If this is an array (formatted to create a fiscal invoice)
            if (is_array($efris_invoice)) {
                // Check if all products are registered
                $products = $efris_invoice['itemsBought'];
                // Item codes
                $codes = array_column($products, 'itemCode');

                // Post it if check if one of the items has code='NO_CODE'
                if (! in_array('NO_CODE', $codes)) {
                    $response = $efris->makePost('generate-fiscal-invoice', ['data' => $efris_invoice]);

                    $feedback = json_decode($response);
                    if (! ($feedback->status) || (@$feedback->status->returnCode != '00')) {
                        $msg = 'Receipt '.$fs->refNumber.' could not be fiscalised';
                        Log::info("\n\033[31m$msg \033[0m");
                        Log::info("\n\033[31m$response \033[0m");
                        AutoSyncActivityServices::createSyncActivity($auto, 'ERROR', $msg, $response);
                    } else {
                        $invoiceItem = QuickBooksInvoice::where(['id' => $fs->id, 'invoice_kind' => 'RECEIPT'])->first();
                        $invoiceItem->fiscalStatus = 1;
                        $invoiceItem->fiscalNumber = $feedback->data->basicInformation->invoiceNo;
                        $invoiceItem->validationStatus = 1;
                        $invoiceItem->update();

                        $msg = 'Receipt '.$fs->refNumber.' successfully fiscalised';
                        Log::info("\n\033[32m$msg \033[0m\n");
                        AutoSyncActivityServices::createSyncActivity($auto, 'SUCCESS', $msg, '');
                    }
                }
                // Delay Execution by 2 seconds
                sleep(2);
            }
        }

        return response('All is well');
    }

    /**
     * Validate Invoices
     */
    public static function validateInvoices()
    {
        ini_set('memory_limit', '2048M'); // Allow up to 2GB for this action

        // 1. All invoices which have not yet been validated
        $fiscalised = QuickBooksInvoice::where(['validationStatus' => 0, 'invoice_kind' => 'INVOICE'])
            ->get();

        if ($fiscalised->count() > 0) {
            // Invoices from QB Online
            $invoices = (new self())->urlQueryBuilderAll('Invoice');

            $all_invoices = json_decode(json_encode($invoices), false);

            // Save Validation Errors to the DB
            foreach ($all_invoices->QueryResponse->Invoice as $inv) {
                $customfields = $inv->CustomField;
                $invoiceCols['refNumber'] = $inv->DocNumber;
                $invoiceCols['qb_created_at'] = $inv->MetaData->CreateTime;
                $invoiceCols['customerName'] = $inv->CustomerRef->name;
                $invoiceCols['totalAmount'] = $inv->TotalAmt;
                $invoiceCols['po'] = 1;
                $invoiceCols['tin'] = @$customfields[0]->StringValue;
                $invoiceCols['dueDate'] = $inv->DueDate;
                $invoiceCols['TxnDate'] = $inv->TxnDate;
                $invoiceCols['balance'] = $inv->Balance;

                QuickBooksInvoice::saveInvoiceSummary($inv->Id, $invoiceCols);

                continue; // Skip and continue if there is an error
            }

            // Validate Invoices Registered to the DB
            $auto = AutoSync::createAutoSync('Invoice Validation');
            Log::info("\nStarting Invoice Validate #".$auto);
            foreach ($fiscalised as $fs) {
                Log::info("\nTrying to validate Invoice #".$fs->refNumber);

                $efris_invoice = QuickBooksInvoice::getFiscalInvoiceAtrributes($fs->id);
                // Invoice Instance
                $invoiceItem = QuickBooksInvoice::where(['id' => $fs->id])->first();

                // if this an array? (formatted to create a fiscal invoice?
                if (is_array($efris_invoice)) {
                    // check if all products are registered
                    $products = $efris_invoice['itemsBought'];
                    // item codes
                    $codes = array_column($products, 'itemCode');

                    // Some items have not been registered...
                    if (in_array('NO_CODE', $codes)) {
                        $invoiceItem->validationStatus = 0; // Failed
                        $msg = "Invoice No.{$fs->refNumber} has items which are not registered with EFRIS";
                        $reason = 'Invoice No.'.$fs->refNumber.' failed validation criteria';
                        $invoiceItem->validationError = $msg;
                        AutoSyncActivityServices::createSyncActivity($auto, 'ERROR', $reason, $msg);
                    } else { // All checks out?
                        $invoiceItem->validationStatus = 1; // Passed
                        $msg = "Invoice No.{$fs->refNumber} passed validation criteria";
                        AutoSyncActivityServices::createSyncActivity($auto, 'SUCCESS', $msg, '');
                    }
                } else {
                    $invoiceItem->validationStatus = 0; // Failed
                    $msg = "Invoice No.{$fs->refNumber} not found in your Quickbooks account";
                    $reason = 'Invoice No.'.$fs->refNumber.' failed validation criteria';
                    $invoiceItem->validationError = $msg;
                    AutoSyncActivityServices::createSyncActivity($auto, 'ERROR', $reason, $msg);
                }
                // Update Record
                $invoiceItem->update();
            }
        }

        return response()->json('All is well');
    }

    /**
     * Synch Stock Adjustment Records
     */
    public static function syncStockAdjustment()
    {
        $decreaseResponse = (new self())->queryString('reports/GeneralLedger?date_macro=This Fiscal Year&summarize_column_by=ProductsAndServices&columns=quantity,rate,tx_date,txn_type,doc_num,item_name&minorversion=57');

        $items = json_decode(json_encode($decreaseResponse));

        $col = [];
        $row = [];
        $list = [];
        //        dd($items);

        if (! is_null($items)) {

            foreach ($items->Rows->Row as $item) {
                // first row
                $row = $item->Rows->Row;
                foreach ($row as $colData) {
                    if (property_exists($colData, 'ColData')) {
                        $col = $colData->ColData;
                        if (is_array($col)) {
                            foreach ($col as $data) {
                                if ($data->value == 'Stock Qty Adjust') {
                                    QuickBooksServiceHelper::logToFile($col);
                                    $list[] = $col;
                                }
                            }
                        }
                    }
                }
            }

            // Save valid records to the DB
            $stock_adjust = $list;
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

            // Show this in tabular format
            // Example: Logging the data instead of using Yii's Table widget
            Log::info('Recording '.count($adjust_items).' records to the DB...');
            Log::info(json_encode($adjust_items));

            $collect = collect($adjust_items)->toArray();

            if (count($collect) > 0) {
                // Bulk Insert
                Log::info('Recording '.count($adjust_items).' records to the DB...');
                try {
                    foreach (array_chunk($collect, 1000) as $chunk) {
                        StockAdjustment::Insert($chunk);

                    }

                    return response()->json(['status' => 'SUCCESS', 'payload' => 'Records successfully updated']);
                } catch (\Throwable $throwable) {
                    QuickBooksServiceHelper::logToFile($throwable->getMessage());
                }
            }
        }

        return response()->json(['status' => 'FAIL', 'payload' => 'No Stock Adjustments data found']);

    }

    /**
     * Increase stock Items in Bills from the QuickBooks Account
     *
     * @return int
     */
    public static function increaseStock()
    {
        // 1. Update all bills which have not yet been synced to EFRIS
        QuickbooksPurchase::where('uraSyncStatus', 0)
            ->update(['stockInType' => 102]);

        // 2. Pick records from the DB
        $dbPurchases = QuickbooksPurchase::where('uraSyncStatus', 0)
            ->where('validationStatus', 1)
            ->get();

        $efris = new ApiRequestHelper('efris1');

        // Create an Auto-Sync instance
        $auto = AutoSync::createAutoSync('Stock Increase');

        $num = 1;
        foreach ($dbPurchases as $purchase) {
            Log::info("{$num}. Increasing stock for Bill No. ".$purchase->id);
            $stock = QuickbooksPurchase::prepareEfrisStockIncrease($purchase->id);

            if (is_array($stock) && is_array($stock['stockInItem'])) {
                $response = $efris->makePost('increase-stock', $stock);
                $data = json_decode($response);

                if ($data->status->returnCode == '00') { // if all went well
                    $purchItem = QuickbooksPurchase::where('id', $purchase->id)->first();
                    $purchItem->updated_at = now();
                    $purchItem->uraSyncStatus = 1;
                    $purchItem->save(); // Update

                    $msg = 'Bill No. '.$purchase->id.' successfully recorded in EFRIS';
                    Log::info($msg);
                    AutoSyncActivityServices::createSyncActivity($auto, 'SUCCESS', $msg, '');
                } else {
                    $msg = 'Bill No.'.$purchase->id.' could not be recorded in EFRIS';
                    Log::error($msg);
                    Log::error($response);
                    AutoSyncActivityServices::createSyncActivity($auto, 'ERROR', $msg, $response);
                }
            } else {
                $msg = 'Bill No.'.$purchase->id.' could not be recorded in EFRIS';
                Log::error($msg);
                Log::error(json_encode($stock));
                AutoSyncActivityServices::createSyncActivity($auto, 'ERROR', $msg, json_encode($stock));
            }
            $num++;
        }

        return response('All is well');
    }
}
