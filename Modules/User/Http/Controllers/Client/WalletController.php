<?php

namespace Modules\User\Http\Controllers\Client;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\User\Entities\User;
use Modules\Order\Entities\Order;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Modules\Payment\Entities\Payment;
use Modules\Server\Entities\Subscription;
use App\Telegram\Keyboard\KeyboardHandler;
use Telegram\Bot\Laravel\Facades\Telegram;
use Modules\Payment\Entities\PaymentMethod;
use Illuminate\Contracts\Support\Renderable;
use Modules\User\Entities\WalletTransaction;
use Modules\Common\Http\Controllers\Api\ApiController;

class WalletController extends ApiController
{
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
            $wallet_trans = WalletTransaction::query()->where('user_id', $payment->user_id)->first();
            $user = User::query()->where('id', $payment->user_id)->first();

            if ($code == "1") {
                $payment->update(['status' => 'success']);
                $user->increment("wallet", $wallet_trans->amount);
                $message = "📣 *افزایش موجودی کیف پول شما با موفقیت انجام شد*\n\n" .
                    "💎 *موجودی فعلی:* `$user->wallet` " . "تومان\n";
                Telegram::sendMessage([
                    'text' => $message,
                    "chat_id" => $payment->user->uid,
                    'parse_mode' => 'MarkdownV2',
                    'reply_markup' => KeyboardHandler::home(),
                ]);
                return redirect()->route('payment.success', $payment->id);
            } else {
                $payment->update(['status' => 'rejected']);
                $message = "📣 *افزایش کیف پول با موفقیت انجام نشد*\n\n" .
                    "💎 *موجودی فعلی:* `$user->wallet` " . "تومان\n";
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
