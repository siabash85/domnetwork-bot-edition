<?php

namespace Modules\Guide\Http\Controllers\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\Guide\Entities\GuidePlatform;
use Modules\Guide\Http\Requests\Panel\GudeiPlatformRequest;

class GuidePlatformController extends ApiController
{

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $platforms = GuidePlatform::query()->get();
        return $this->successResponse($platforms, "");
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(GudeiPlatformRequest $request)
    {
        $platform =  GuidePlatform::query()->create([
            'name' => $request->name,
            'status' => "active",
        ]);
        return $this->successResponse($platform, "ایجاد  با موفقیت انجام شد");
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $platform = GuidePlatform::query()->find($id);
        return $this->successResponse($platform, "");
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(GudeiPlatformRequest $request, $id)
    {
        $platform = GuidePlatform::query()->find($id);
        $platform->update([
            'name' => $request->name,
            'status' => "active",
        ]);
        return $this->successResponse($platform, "ویرایش  با موفقیت انجام شد");
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $platform = GuidePlatform::query()->find($id);
        $platform->delete();
        return $this->successResponse($platform, "حذف  با موفقیت انجام شد");
    }
}
