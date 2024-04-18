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


use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::group(['prefix' => 'quickbooks/goods', 'middleware' => ['auth', 'web', 'token', 'verified', 'qbo.token',
    InitializeTenancyByDomainOrSubdomain::class,
    PreventAccessFromCentralDomains::class,]], function () {
    Route::get('/', 'GoodsController@index')->name('goods.all');
    Route::get('/not-registered', 'GoodsController@index')->name('goods.noregistered');
    Route::get('sync-items', 'GoodsController@syncItems')->name('goods.syncItems');
    Route::get('register-opening-stock/{id}', 'GoodsController@registerOpeningStockView')->name('quickbooks.register-stock');
    Route::post('register-opening-stock/{id}', 'GoodsController@registerOpeningStock')->name('quickbooks.register-stock.store');
    Route::get('/product-details/{id}', 'GoodsController@actionItemProductDetails')->name('goods.product-details');
    Route::post('register-product/{id}', 'GoodsController@registerProductn')->name('quickbooks.register-product-efris');
    Route::match(['get','post'],'/register-product/{id}/{redo?}', 'GoodsController@registerProduct')->name('quickbooks.register-product');
});
