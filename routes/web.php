<?php

use Modules\User\Entities\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // $res = Http::get("https://api.telegram.org/bot6627556212:AAHLM2Z_iUJTVmKyyXmsXSyzpRiFpc9umSs/getMe");
    // dd($res);
    // Http::asForm()->post("https://api.telegram.org/bot6627556212:AAHLM2Z_iUJTVmKyyXmsXSyzpRiFpc9umSs/sendMessage", [
    //     "chat_id"  => "1669306764",
    //     "text"  => "test message ee"
    // ]);

    return view('welcome');
});
// Route::post('/webhook/callback', [WebhookController::class, 'callback']);
