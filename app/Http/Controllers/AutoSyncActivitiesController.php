<?php

namespace App\Http\Controllers;

use App\Models\AutoSyncActivity;
use App\Services\AutoSyncActivityServices;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Inertia\Inertia;

class AutoSyncActivitiesController extends Controller
{
    public function allErrors()
    {
        $errors = AutoSyncActivity::where('msg_category', 'ERROR')->with('autoSync')->orderBy('id', 'DESC')->paginate(12);

        return view::render('AutoSyncErrors', ['data' => $errors]);
    }

    /**
     * Validate Bills and purchases
     *
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Response
     */
    public function validatePurchases()
    {
        return (new AutoSyncActivityServices())::validatePurchases();
    }

    /**
     * Fiscalise invoices which are ready...
     */
    public function fiscaliseInvoices()
    {
        return (new AutoSyncActivityServices())::fiscaliseInvoices();
    }

    /**
     * Fiscalise Receipts which are ready...
     */
    public function fiscaliseReceipts()
    {
        return (new AutoSyncActivityServices())::fiscaliseReceipts();
    }

    /**
     * Validate Invoices
     */
    public function validateInvoices()
    {
        return (new AutoSyncActivityServices())::validateInvoices();
    }

    /**
     * Synch Stock Adjustment Records
     */
    public function syncStockAdjustment()
    {
        return (new AutoSyncActivityServices())::syncStockAdjustment();
    }

    /**
     * Increase stock Items in Bills from the QuickBooks Account
     *
     * @return int
     */
    public function increaseStock()
    {
        return (new AutoSyncActivityServices())::increaseStock();
    }
}
