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


use Illuminate\Support\Facades\Route;
use Modules\Invoices\Http\Controllers\ValidationsController;
use Modules\Receipts\Http\Controllers\ReceiptsController;

//Route::group(['prefix' => 'quickbooks/receipts', 'middleware' => ['auth', 'web', 'token', 'verified', 'qbo.token']], function () {
//    Route::get('/', [ReceiptsController::class, 'index'])->name('qbo.receipts.index');
//    Route::get('/passed-validations', [ReceiptsController::class, 'passedValidations'])->name('qbo.receipts.passed');
//    Route::get('/failed-validations', [ReceiptsController::class, 'failed'])->name('qbo.receipts.failed');
//    Route::get('/validation-errors', [ReceiptsController::class, 'errors'])->name('qbo.receipts.errors');
//    Route::get('/fiscalised', [ReceiptsController::class, 'fiscalised'])->name('qbo.receipts.ura');
//    Route::post('update-invoice-industry-code', [ReceiptsController::class, 'updateInvoiceIndustry'])->name('receipts.update.industrycode');
//    Route::post('update-invoice-buyer-type', [ReceiptsController::class, 'updateBuyerType'])->name('receipts.update.buyerType');
//
//  Route::get('receipts-range/{validate}', [ReceiptsController::class, 'receiptsDateRange'])->name('qbo.receipts.range');
//});
//Route::get('validate/all/receipts', [ValidationsController::class, 'validateReceipts'])->name('receipts.validate');
