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
use Modules\StockAdjustments\Http\Controllers\StockAdjustmentsController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::group(['prefix' => 'quickbooks/stockadjustments',
    'middleware' => ['auth', 'web', 'token', 'verified', 'qbo.token',
        InitializeTenancyByDomainOrSubdomain::class,
        PreventAccessFromCentralDomains::class,
        ]], function () {
    Route::get('/', 'StockAdjustmentsController@index')->name('qbo.stockadjustments');
    Route::get('/sync', 'StockAdjustmentsController@sync')->name('qbo.stockadjustments.sync');
    Route::get('reduce-stock/{id}/{stock}', 'StockAdjustmentsController@actionReduceStock')->name('stockAdjust.reduce-stock');
    Route::post('update-stockin-type', [StockAdjustmentsController::class, 'updateStockADType'])->name('update.stockInType');
});
