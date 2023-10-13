<?php

namespace Modules\Payment\Http\Controllers\Client;

use Illuminate\Http\Request;
use Modules\Order\Entities\Order;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Modules\Payment\Entities\Payment;
use Telegram\Bot\Laravel\Facades\Telegram;
use Modules\Payment\Entities\PaymentMethod;
use Illuminate\Contracts\Support\Renderable;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\Server\Entities\Subscription;

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
                Subscription::query()->create([
                    'user_id' => $payment->user_id,
                    'service_id' => $payment->paymentable->service->id,
                    'status' => "active",
                    "expire_at" => now()
                ]);
                Telegram::sendMessage([
                    'text' => "پرداخت موفق",
                    "chat_id" => $payment->user->uid,
                ]);
            } else {
                $payment->update(['status' => 'rejected']);
                Telegram::sendMessage([
                    'text' => "پرداخت ناموفق",
                    "chat_id" => $payment->user->uid,
                ]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
    }
}
