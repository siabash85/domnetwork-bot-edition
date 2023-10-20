<?php

namespace Modules\Payment\Http\Controllers\Client;

use App\Telegram\Keyboard\KeyboardHandler;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\Order\Entities\Order;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Modules\Payment\Entities\Payment;
use Modules\Server\Entities\Subscription;
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
                $subscription = Subscription::query()->create([
                    'user_id' => $payment->user_id,
                    'service_id' => $payment->paymentable->service->id,
                    'status' => "active",
                    'name' => random_int(1000, 1000000),
                    "expire_at" => now()->addDays($payment->paymentable->service->package_duration->name)
                ]);

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
                return redirect()->route('payment.success', $payment->id);
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
