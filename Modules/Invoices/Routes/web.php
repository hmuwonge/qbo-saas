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
use Modules\Invoices\Http\Controllers\InvoicesController;
use Modules\Invoices\Http\Controllers\ValidationsController;

//Route::group(['prefix' => 'quickbooks/invoices', 'middleware' => ['auth', 'web', 'xss','token', 'verified', 'qbo.token']], function () {
//    Route::get('/', [InvoicesController::class, 'index'])->name('qbo.invoices.all');
//    Route::get('invoices-range/{validate}', [InvoicesController::class, 'invoicesRange'])->name('qbo.invoices.range');
//    Route::get('/passed-validations', [InvoicesController::class, 'passedValidations'])->name('qbo.invoices.passed');
//    Route::get('/failed-validations', [InvoicesController::class, 'failed'])->name('qbo.invoices.failed');
//    Route::get('/validation-errors', [InvoicesController::class, 'errors'])->name('qbo.invoices.errors');
//    Route::get('/fiscalised', [InvoicesController::class, 'fiscalised'])->name('qbo.invoices.ura');
////    Route::post('update-invoice-industry-code', [InvoicesController::class, 'updateInvoiceIndustry'])->name('update.industrycode');
//
//    Route::get('invoice-preview/{id}', [InvoicesController::class,'actionInvoicePreview'])->name('invoice.preview');
//    Route::get('invoice-sample/{id}/{invoice}', [InvoicesController::class,'actionInvoicePreview'])->name('invoices.sample');
//    Route::get('invoices-sync', [InvoicesController::class,'syncInvoices'])->name('invoices.sync');
//});
//
//Route::group(['prefix' => 'quickbooks/invoices', 'middleware' => ['auth', 'web', 'token', 'verified']], function () {
//    Route::post('update-invoice-industry-type', [InvoicesController::class, 'updateInvoiceIndustry'])->name('update.industrycode');
//    Route::post('update-invoice-buyer-type', [InvoicesController::class, 'updateBuyerType'])->name('invoices.update.buyerType');
//});
//
//
//Route::group(['middleware' => ['auth', 'web', 'token', 'verified', 'qbo.token']], function () {
//Route::get('validate/invoices', [ValidationsController::class, 'validateInvoices'])->name('validate.invoices');
//Route::get('validate/purchase', [ValidationsController::class, 'syncPurchaseBills'])->name('validate.bill');
//Route::post('sync-invoices-range', [ValidationsController::class, 'validateInvoicesWithDatePeriod']);
//Route::post('sync-receipts-range', [ValidationsController::class, 'validateReceiptsWithDatePeriod']);
//Route::get('validate/creditnotes', [ValidationsController::class, 'validateCreditMemos'])->name('validateCreditMemos');
//
//});
