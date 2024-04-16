<?php

use Illuminate\Http\Request;
use Modules\EfrisReports\Http\Controllers\ApiController;
use Modules\EfrisReports\Http\Controllers\EfrisController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/efrisreports', function (Request $request) {
    return $request->user();
});

/**
 * Routes that handle communication to efris api
 */
Route::group(['prefix' => 'efrisreports'], function () {
    Route::post('fiscalised-invoices', [ApiController::class, 'invoices']);
    Route::post('fiscalised-receipts', [ApiController::class, 'receipts']);
    Route::get('fiscal-document-details/{id}', [ApiController::class, 'fiscalDocument']);

    Route::post('credit-notes', [ApiController::class, 'creditNotes']);
    Route::get('credit-notes/details/{id}', [ApiController::class, 'creditNoteDetails']);
    Route::post('cancel-notes', [EfrisController::class, 'sendCancelCreditNote'])->name('creditnote.cancel');

    // Route::get('fiscalise/{id}', [QBOController::class, 'actionFiscaliseInvoice'])->name('invoice.fiscalise');

    Route::post('goods-services', [ApiController::class, 'goodsAndServices']);

    Route::get('payment-methods', [EfrisController::class, 'paymentMethods']);
    Route::get('unit-of-measure', [EfrisController::class, 'actionUnitOfMeasure']);
    Route::get('vat-regimes', [EfrisController::class, 'actionVatRegimes']);
    Route::get('sectors', [EfrisController::class, 'actionSectors']);
    Route::get('currency', [EfrisController::class, 'currency']);
    Route::get('countries', [EfrisController::class, 'actionCountries']);
    Route::get('exise-duty', [EfrisController::class, 'actionExiseDuty']);
    Route::get('unspsc-list', [EfrisController::class, 'unspscList']);
    Route::get('exise-duty', [EfrisController::class, 'actionExiseDuty']);
    Route::get('unspsc-codes', [EfrisController::class, 'actionUnspsc']);
    // Route::get('search-taxpayer/{keyword}', [TaxpayerConfigController::class, 'searchTaxpayer']);
});