<?php

use Modules\Invoices\Http\Controllers\ValidationsController;

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
Route::group(['prefix' => 'quickbooks/creditmemos', 'middleware' => ['auth', 'web', 'token', 'verified', 'qbo.token']], function () {
// Route::prefix('quickbooks/creditmemos')->group(function() {
    Route::get('/', 'CreditMemosController@index')->name('qbo.creditnotes.index');
    Route::get('fiscalise-credit-note/{id}', 'CreditMemosController@fiscaliseCreditnote');
    Route::get('fiscalise-credit-note', 'CreditMemosController@actionFiscaliseCreditnote')->name('quickbooks.fiscalise-creditnote');
    Route::get('credit-notes/{validate?}', 'CreditMemosController@actionCreditMemos');
    Route::post('link-credit-note', 'CreditMemosController@actionLinkCreditNote')->name('quickbooks.link-credit-note');
    Route::get('credit-notes', 'CreditMemosController@actionCreditMemos')->name('sync.creditnotes');
    Route::get('fiscalise-credit-note/{id}', 'CreditMemosController@fiscaliseCreditnote')->name('creditnote.fiscalise');
    Route::post('send-fiscalise-credit-note', 'CreditMemosController@sendFiscaliseCreditnote')->name('send.creditnote.fiscalise');

});
