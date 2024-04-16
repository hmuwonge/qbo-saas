<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Modules\EfrisReports\Http\Controllers\EfrisController;
use Modules\Invoices\Http\Controllers\InvoicesController;

Route::prefix('efrisreports')->group(function() {
    Route::get('/', 'EfrisController@invoices')->name('efris.invoices');
    Route::get('/goods-services', 'EfrisController@goodsAndServices2')->name('efris.goods');
    Route::post('/goods-services', 'EfrisController@goodsAndServices2')->name('efris.goods.get');
});

// All FRIS URA DATA FROM THE EFRIS MIDDLEWARE API
Route::group(['prefix' => 'efris-ura', 'middleware' => ['auth', 'web', 'verified']], function () {
    Route::get('fiscalised-invoices', [EfrisController::class, 'invoices'])->name('ura.invoices');
    Route::get('fiscalised-receipts', [EfrisController::class, 'receipts'])->name('ura.receipts');
    Route::get('cancel-creditnote/{id}', [EfrisController::class, 'creditNoteDetails'])->name('creditnote.cancel.view');
    Route::get('fiscal-invoice/details/{id}', [EfrisController::class, 'invoicesDetails'])->name('fiscal-invoice.preview');
    Route::get('fiscal-invoice-download/{id}', [EfrisController::class, 'actionViewInvoicePdf'])->name('invoice.download.rt');
    Route::get('fiscal-creditnote-download/{id}', [EfrisController::class, 'actionViewCreditnotePdf'])->name('creditnote.download');
   Route::get('fiscalise/{id}/{kind}', [InvoicesController::class, 'actionFiscaliseInvoice'])->name('invoice.fiscalise');

    Route::get('issued-credit-notes', [EfrisController::class, 'creditNotes'])->name('ura.creditnotes');
    Route::get('goods-services', [EfrisController::class, 'goodsAndServices2'])->name('ura.goods');
});
