<?php

use App\Http\Controllers\AutoSyncActivitiesController;

Route::group(['prefix' => 'quickbooks', 'middleware' => ['auth', 'web', 'xss','token', 'verified', 'qbo.token']], function () {
    Route::controller(AutoSyncActivitiesController::class)
        ->group(function () {
            Route::get('validate/purchases', 'validatePurchases')->name('autosync.purchases.validate');
            Route::get('fiscalise/invoices-all', 'fiscaliseInvoices')->name('autosync.invoices.fiscalise');
            Route::get('fiscalise/receipts-all', 'fiscaliseReceipts')->name('autosync.receipts.fiscalise');
            Route::get('validate/sync-invoices', 'validateInvoices')->name('autosync.invoices.validate');
            Route::get('sync/stock-adjustments', 'syncStockAdjustment')->name('autosync.stockadjustments.sync');
            Route::get('auto-sync/increase-stock', 'increaseStock')->name('autosync.increase-stock');
        });
});


