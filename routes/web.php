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
        $res = Http::post("https://digital.pangooan.pw:12670/login", [
            "username" => "admin",
            "password" => "1qaz@WSX"
        ]);


        $cookieJar = $res->cookies();
        $cookiesArray = [];
        foreach ($cookieJar as $cookie) {
            $cookiesArray[] = $cookie->getName() . '=' . $cookie->getValue();
        }
        $cookiesString = implode('; ', $cookiesArray);
        // $sub_code = random_int(1000000, 10000000);
        // $settings = [
        //     "clients" => [
        //         [
        //             "id" => Str::uuid(),
        //             "flow" => "",
        //             "email" => Str::random(8),
        //             "limitIp" => 0,
        //             "totalGB" => 2147483648,
        //             "expiryTime" => -30   * 24 * 60 * 60 * 1000,
        //             "enable" => true,
        //             "tgId" => "",
        //             "subId" => Str::random(16)
        //         ]
        //     ]
        // ];

        // $response = Http::withHeaders([
        //     'Cookie' => $cookiesString,
        // ])->post("https://digital.pangooan.pw:12670/panel/inbound/addClient", [
        //     "id" => 2,
        //     "settings" => json_encode($settings)
        // ]);
        // dd($response->body());
        // $server_config = json_decode($response->body());
        // dd($server_config);
        $server_address = "https://digital.pangooan.pw:12670";
        $inbound = Http::withHeaders(['Cookie' => $cookiesString])->get("$server_address/xui/API/inbounds/get/1");
        $inbound_res = json_decode($inbound->body());
        $inbound_obj = $inbound_res->obj;
        dd($inbound_obj);
    } catch (\Throwable $th) {
        dd($th->getMessage());
    }
});
Route::fallback(function () {
    return view('welcome');
})->name('welcome');
