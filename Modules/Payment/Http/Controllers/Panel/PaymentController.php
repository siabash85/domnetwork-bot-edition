<?php

namespace Modules\Payment\Http\Controllers\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\Payment\Entities\Payment;
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
        $payment->update([
            'status' => $request->status,
        ]);
        return $this->successResponse($payment, "ویرایش  با موفقیت انجام شد");
    }
}
