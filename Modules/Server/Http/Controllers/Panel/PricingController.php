<?php

namespace Modules\Server\Http\Controllers\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\Server\Entities\Pricing;
use Modules\Server\Http\Requests\Panel\PricingRequest;

class PricingController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $pricings = Pricing::query()->get();
        return $this->successResponse($pricings, "");
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(PricingRequest $request)
    {
        $exists_default = Pricing::query()->where('is_default', true)->first();
        if (!is_null($exists_default) && $request->is_default) {
            $exists_default->update(['is_default' => false]);
        }
        $pricing =  Pricing::query()->create([
            'name' => $request->name,
            'content' => $request->content,
            'is_default' => $request->is_default,
        ]);

        return $this->successResponse($pricing, "ایجاد  با موفقیت انجام شد");
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $pricing = Pricing::query()->find($id);
        return $this->successResponse($pricing, "");
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(PricingRequest $request, $id)
    {
        $exists_default = Pricing::query()->where('is_default', true)->first();
        if (!is_null($exists_default) && $request->is_default) {
            $exists_default->update(['is_default' => false]);
        }
        $pricing = Pricing::query()->find($id);
        $pricing->update([
            'name' => $request->name,
            'content' => $request->content,
            'is_default' => $request->is_default,
        ]);

        return $this->successResponse($pricing, "ویرایش  با موفقیت انجام شد");
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $pricing = Pricing::query()->find($id);
        $pricing->delete();
        return $this->successResponse($pricing, "حذف  با موفقیت انجام شد");
    }
}
