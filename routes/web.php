<?php

use Illuminate\Support\Str;
use Modules\User\Entities\User;
use Modules\Order\Entities\Order;
use Modules\Server\Entities\Server;
use Illuminate\Support\Facades\Http;
use Modules\Order\Entities\PreOrder;
use Modules\Server\Entities\Package;
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

Route::get("test", function () {
    try {
        $res = Http::asForm()->post("https://perfectmoney.com/acct/ev_activate.asp", [
            "Payee_Account" => "admin",
            "ev_number" => "1qaz@WSX",
            "ev_code" => "1qaz@WSX",
        ]);
        $html = $res->body();
        $dom = new SimpleXMLElement($html);
        $errorValue = $dom->xpath('//input[@name="ERROR"]/@value');
        dd($errorValue);
    } catch (\Throwable $th) {
        dd($th);
    }
});
// Route::fallback(function () {


//     return view('welcome');
// })->name('welcome');
