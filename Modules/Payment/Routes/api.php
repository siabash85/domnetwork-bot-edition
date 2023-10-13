<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\Client\PaymentController;
use Modules\Payment\Http\Controllers\Panel\PaymentController as PanelPaymentController;

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

Route::prefix('/client')->group(function () {
    Route::get("payment/generate/{order}/{id}", [PaymentController::class, 'generate'])->name('payment.generate');
    Route::post("payment/callback", [PaymentController::class, 'callback'])->name('payment.callback');
});

Route::prefix('/panel')->group(function () {

    Route::apiResource("payments", PanelPaymentController::class);
});
