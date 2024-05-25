<?php

use Modules\Purchases\Http\Controllers\PurchasesController;

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

//Route::prefix('quickbooks/purchases')->group(function() {
//    Route::group(['prefix' => 'quickbooks/purchases', 'middleware' => ['auth','xss', 'web', 'token', 'verified', 'qbo.token']], function () {
//    Route::get('/', 'PurchasesController@index')->name('purchases.index');
//    Route::post('update-invoice-buyer-type', [PurchasesController::class, 'updatePurchaseStockInType'])->name('purchase.stockUpdate');
//    Route::get('fiscalise-increase-stock/{id}', [PurchasesController::class, 'increasePurchaseStock'])->name('quickbooks.fiscalise-increase-stock');
//});
