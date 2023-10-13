<?php

namespace Modules\Server\Http\Controllers\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\Server\Entities\Service;
use Modules\Server\Http\Requests\Panel\ServiceRequest;
use Modules\Server\Transformers\Panel\ServiceResource;

class ServiceController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $services = Service::query()->get();
        $services = ServiceResource::collection($services);
        return $this->successResponse($services, "");
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(ServiceRequest $request)
    {
        $services =  Service::query()->create([
            'server_id' => $request->server_id,
            'package_duration_id' => $request->package_duration_id,
            'package_id' => $request->package_id,
            'status' => $request->status,
            'price' => $request->price,
            'link' => $request->link,
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
        $service = Service::query()->find($id);
        $service = new ServiceResource($service);
        return $this->successResponse($service, "");
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(ServiceRequest $request, $id)
    {
        $services = Service::query()->find($id);
        $services->update([
            'server_id' => $request->server_id,
            'package_duration_id' => $request->package_duration_id,
            'package_id' => $request->package_id,
            'status' => $request->status,
            'price' => $request->price,
            'link' => $request->link,
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
        $services = Service::query()->find($id);
        $services->delete();
        return $this->successResponse($services, "حذف  با موفقیت انجام شد");
    }
}
