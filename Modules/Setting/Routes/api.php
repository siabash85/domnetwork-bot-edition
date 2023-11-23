<?php

use Illuminate\Support\Facades\Route;

Route::prefix('panel')->group(function () {
    Route::prefix('setting')->group(
        function () {
            Route::get("/variables", [\Modules\Setting\Http\Controllers\v1\Management\SettingController::class, 'index']);
            Route::post("/variables", [\Modules\Setting\Http\Controllers\v1\Management\SettingController::class, 'update']);
        }
    );
});

Route::prefix('application')->group(function () {
    Route::prefix('setting')->group(
        function () {
            Route::get("/variables", [\Modules\Setting\Http\Controllers\v1\Management\SettingController::class, 'index']);
        }
    );
});
