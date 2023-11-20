<?php

namespace Modules\Server\Http\Controllers\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\Server\Entities\PackageDuration;
use Modules\Server\Http\Requests\Panel\PackageDurationRequest;

class PackageDurationController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $servers = PackageDuration::query()->get();
        return $this->successResponse($servers, "");
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(PackageDurationRequest $request)
    {
        $server =  PackageDuration::query()->create([
            'name' => $request->name,
            'value' => $request->value,
        ]);
        return $this->successResponse($server, "ایجاد  با موفقیت انجام شد");
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $server = PackageDuration::query()->find($id);
        return $this->successResponse($server, "");
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(PackageDurationRequest $request, $id)
    {
        $server = PackageDuration::query()->find($id);
        $server->update([
            'name' => $request->name,
            'value' => $request->value,
        ]);
        return $this->successResponse($server, "ویرایش  با موفقیت انجام شد");
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $server = PackageDuration::query()->find($id);
        $server->delete();
        return $this->successResponse($server, "حذف  با موفقیت انجام شد");
    }
}
