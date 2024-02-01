<?php

use Illuminate\Support\Str;
use Modules\User\Entities\User;
use Modules\Order\Entities\Order;
use Modules\Server\Entities\Server;
use Illuminate\Support\Facades\Http;
use Modules\Order\Entities\PreOrder;
use Modules\Server\Entities\Package;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Http\Controllers\WebhookController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Modules\Setting\Transformers\SettingResource;
use Spatie\MediaLibrary\Conversions\ImageGenerators\Webp;

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


Route::fallback(function () {

    // $qrCode = QrCode::format('png')->generate('Hello, World!');
    // $path = 'public/images/qrcodes/' . uniqid() . '.png';
    // Storage::put($path, $qrCode);
    // $url = Storage::url($path);
    // dd($url);

    // return response()->json(['qr_code_url' => $url]);

    return view('welcome');
})->name('welcome');
