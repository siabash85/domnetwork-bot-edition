<?php

namespace Modules\Payment\Http\Controllers\Client;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\User\Entities\User;
use Modules\Order\Entities\Order;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Modules\Order\Entities\PreOrder;
use Modules\Payment\Entities\Payment;
use Modules\Server\Entities\Subscription;
use App\Telegram\Keyboard\KeyboardHandler;
use Telegram\Bot\Laravel\Facades\Telegram;
use Modules\Payment\Entities\PaymentMethod;
use Illuminate\Contracts\Support\Renderable;
use Modules\Common\Http\Controllers\Api\ApiController;

class PaymentController extends ApiController
{

    public function generate(Request $request, $order_id, $transid)
    {
        $order = Order::find($order_id);
        $payment_method = PaymentMethod::query()->where('is_default', true)->first();
        $order->payments()->create([
            "user_id" => $order->user_id,
            "payment_method_id" => $payment_method->id,
            "invoice_id" => $transid,
            "amount" => $order->price,
            "status" => "pending",
        ]);
        return redirect("https://panel.aqayepardakht.ir/startpay/sandbox/{$transid}");
    }

    public function callback(Request $request)
    {
        try {
            $payment = Payment::query()->where('invoice_id', $request['transid'])->first();
            $res = Http::post("https://panel.aqayepardakht.ir/api/v2/verify", [
                "transid" => $request['transid'],
                "pin" => "sandbox",
                "amount" => $payment->amount,
            ]);
            $dd = json_decode($res->body());
            $code = $dd->code;
            // dd($dd);

            if ($code == "1") {
                $payment->update(['status' => 'success']);
                $payment->paymentable->service()->update(['status' => "purchased"]);
                $pre_order = PreOrder::query()->where('user_id', $payment->user_id)->first();
                $sub_code = random_int(1000000, 10000000);
                $subscription = Subscription::query()->create([
                    'user_id' => $payment->user_id,
                    'service_id' => $payment->paymentable->service->id,
                    'status' => "active",
                    'name' => $pre_order->service_name,
                    'code' => $sub_code,
                    'slug' => $pre_order->service_name . " - " . $sub_code,
                    "expire_at" => now()->addDays($payment->paymentable->service->package_duration->name),
                    'uuid' => Str::uuid(),
                    'subId' => Str::random(16)
                ]);

                Telegram::sendMessage([
                    'text' => 'پرداخت شما با موفقیت انجام شد 🚀 | 😍 در حال ارسال کانفیگ به تلگرام شما ...',
                    "chat_id" => $payment->user->uid,
                ]);

                $res = Http::post("https://bob.patrik.pangooan.pw:12670/login", [
                    "username" => "admin",
                    "password" => "1qaz@WSX"
                ]);
                $cookieJar = $res->cookies();
                $cookiesArray = [];
                foreach ($cookieJar as $cookie) {
                    $cookiesArray[] = $cookie->getName() . '=' . $cookie->getValue();
                }
                $cookiesString = implode('; ', $cookiesArray);
                $settings = [
                    "clients" => [
                        [
                            "id" => $subscription->uuid,
                            "flow" => "",
                            "email" => $subscription->code,
                            "limitIp" => 1,
                            "totalGB" => 2,
                            "expiryTime" => $payment->paymentable->service->package_duration->name   * 24 * 60 * 60 * 1000,
                            "enable" => true,
                            "tgId" => "",
                            "subId" => $subscription->subId
                        ]
                    ]
                ];
                $response = Http::withHeaders([
                    'Cookie' => $cookiesString,
                ])->post("https://bob.patrik.pangooan.pw:12670/xui/API/inbounds/addClient", [
                    "id" => 9,
                    "settings" => json_encode($settings)
                ]);
                // dd(json_decode($response->body()));
                if ($response->success()) {
                    $location = $payment->paymentable->service->server->name;
                    $volume = $payment->paymentable->service->package->name;
                    $service_link = $payment->paymentable->service->link;
                    $code = $subscription->code;
                    $expire_date = $subscription->expire_at;
                    $message = "📣 *پرداخت سرویس شما با موفقیت انجام شد*\n\n" .
                        "💎 *کد سرویس:* `$code`\n" .
                        "🌎 *لوکیشن:* `$location`\n" .
                        "⏳ *تاریخ انقضا:* `$expire_date`\n" .
                        "♾ *حجم کل:* `$volume` \n\n" .
                        "📌 *لینک اشتراک* \n\n" .
                        "`$service_link`";
                    Telegram::sendMessage([
                        'text' => $message,
                        "chat_id" => $payment->user->uid,
                        'parse_mode' => 'MarkdownV2',
                        'reply_markup' => KeyboardHandler::home(),
                    ]);
                    $owner_users = User::query()->where('is_notifable', true)->get();
                    $notif_message = "📣 *سرویس جدیدی خریداری شد*\n\n";
                    foreach ($owner_users as $key => $owner_user) {
                        Telegram::sendMessage([
                            'text' => $notif_message,
                            "chat_id" => $owner_user->uid,
                            'parse_mode' => 'MarkdownV2',
                            'reply_markup' => KeyboardHandler::home(),
                        ]);
                    }
                    return redirect()->route('payment.success', $payment->id);
                }
            } else {
                $payment->update(['status' => 'rejected']);
                $message = "📣 *پرداخت سرویس شما با موفقیت انجام نشد*\n\n" .
                    "💎 *کد پیگیری:* `$payment->reference_code`\n";
                Telegram::sendMessage([
                    'text' => $message,
                    "chat_id" => $payment->user->uid,
                    'parse_mode' => 'MarkdownV2',
                    'reply_markup' => KeyboardHandler::home(),
                ]);
                return redirect()->route('payment.failed', $payment->id);
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function success(Request $request, $id)
    {
        $payment = Payment::find($id);
        $reference_code = $payment->reference_code;
        $data = [
            'reference_code' => $reference_code
        ];
        return view('payment::success', compact('data'));
    }
    public function failed(Request $request, $id)
    {
        $payment = Payment::find($id);
        $reference_code = $payment->reference_code;
        $data = [
            'reference_code' => $reference_code
        ];
        return view('payment::failed', compact('data'));
    }
}
