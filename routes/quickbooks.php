<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\QBOController;


//Route::group(['middleware' => ['web']], function () {
    Route::get('connect', [QBOController::class, 'index']);
    Route::get('callback', [QBOController::class, 'callback'])->name('callback');
    Route::get('company', [QBOController::class, 'get_company_info']);
    Route::get('refresh-token', [QBOController::class, 'refresh_token']);
//});
