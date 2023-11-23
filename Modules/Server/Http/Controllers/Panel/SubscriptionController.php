<?php

namespace Modules\Server\Http\Controllers\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Modules\Server\Entities\Subscription;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\Server\Transformers\Panel\SubscriptionDetailResource;
use Modules\Server\Transformers\Panel\SubscriptionResource;

class SubscriptionController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $services = Subscription::query()->get();
        $services = SubscriptionResource::collection($services);
        return $this->successResponse($services, "");
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $services =  Subscription::query()->create([
            'server_id' => $request->server_id,
            'package_duration_id' => $request->package_duration_id,
            'package_id' => $request->package_id,
            'status' => $request->status,
            'price' => $request->price,
        ]);
        return $this->successResponse($services, "ایجاد  با موفقیت انجام شد");
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $subscription = Subscription::query()->find($id);
        $subscription = new SubscriptionDetailResource($subscription);
        return $this->successResponse($subscription, "");
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $services = Subscription::query()->find($id);
        $services->update([
            'status' => $request->status,
        ]);
        return $this->successResponse($services, "ویرایش  با موفقیت انجام شد");
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $services = Subscription::query()->find($id);
        $services->delete();
        return $this->successResponse($services, "حذف  با موفقیت انجام شد");
    }
}
