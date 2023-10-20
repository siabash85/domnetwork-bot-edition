<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Guide\Http\Controllers\Panel\GuidePlatformClientController;
use Modules\Guide\Http\Controllers\Panel\GuidePlatformController;

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

Route::prefix('/panel')->group(function () {
    Route::apiResource("guide/platforms", GuidePlatformController::class);
    Route::apiResource("guide/platform/{platform}/clients", GuidePlatformClientController::class);
});
