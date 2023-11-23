<?php

namespace Modules\Payment\Http\Controllers\Panel;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Entities\User;
use Modules\Order\Entities\Order;
use Illuminate\Support\Facades\Http;
use Modules\Order\Entities\PreOrder;
use Modules\Payment\Entities\Payment;
use Modules\Server\Entities\Subscription;
use App\Telegram\Keyboard\KeyboardHandler;
use Telegram\Bot\Laravel\Facades\Telegram;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\Server\Transformers\Panel\PaymentResource;

class PaymentController extends ApiController
{

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $payments = Payment::query()->get();
        $payments = PaymentResource::collection($payments);
        return $this->successResponse($payments, "");
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $payment = Payment::query()->find($id);
        $payment = new PaymentResource($payment);
        return $this->successResponse($payment, "");
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::query()->find($id);
        $user = $payment->user;
        if ($payment->status == "pending_confirmation" && $request->status == "success") {
            $user->increment("wallet", $payment->amount);
        }
        $payment->update([
            'status' => $request->status,
        ]);
        return $this->successResponse($payment, "ویرایش  با موفقیت انجام شد");
    }
}
