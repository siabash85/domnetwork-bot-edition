<?php

namespace Modules\Order\Http\Controllers\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Payment\Entities\Payment;
use Telegram\Bot\Laravel\Facades\Telegram;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\Order\Entities\Order;
use Modules\Order\Transformers\Panel\OrderResource;

class OrderController extends ApiController
{

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $orders = Order::query()->get();
        $orders = OrderResource::collection($orders);
        return $this->successResponse($orders, "");
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $order = Order::query()->find($id);
        $order = new OrderResource($order);
        return $this->successResponse($order, "");
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::query()->find($id);
        $user = $order->user;
        $order->update([
            'status' => $request->status,
        ]);
        return $this->successResponse($order, "ویرایش  با موفقیت انجام شد");
    }
}
