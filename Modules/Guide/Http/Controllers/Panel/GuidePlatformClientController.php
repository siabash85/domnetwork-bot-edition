<?php

namespace Modules\Guide\Http\Controllers\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Modules\Guide\Entities\GuidePlatform;
use Modules\Guide\Entities\GuidePlatformClient;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\Guide\Http\Requests\Panel\GudeiPlatformRequest;

class GuidePlatformClientController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($id)
    {
        $platforms = GuidePlatformClient::query()->where('guide_platform_id', $id)->get();
        return $this->successResponse($platforms, "");
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(GudeiPlatformRequest $request, $id)
    {
        $file = $request->file('video');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('videos', $filename, 'public');
        $platform =  GuidePlatformClient::query()->create([
            'name' => $request->name,
            'status' => "active",
            'guide_platform_id' => $id,
            'description' => $request->description,
            'link' => $request->link,
            'video' => asset('/storage/videos/' . $filename),
        ]);
        return $this->successResponse($platform, "ایجاد  با موفقیت انجام شد");
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($platform, $id)
    {
        $platform = GuidePlatformClient::query()->find($id);
        return $this->successResponse($platform, "");
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(GudeiPlatformRequest $request, $platform, $id)
    {

        $client = GuidePlatformClient::query()->find($id);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'link' => $request->link,
        ];
        if ($request->hasFile('video')) {
            $prevVideoPath = str_replace(asset('storage'), 'public', $client->video);
            Storage::delete($prevVideoPath);
            $file = $request->file('video');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('videos', $filename, 'public');
            $data['video'] = asset('/storage/videos/' . $filename);
        }
        $client->update($data);
        return $this->successResponse($client, "ویرایش  با موفقیت انجام شد");
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($platform, $id)
    {
        $platform = GuidePlatformClient::query()->find($id);
        $platform->delete();
        return $this->successResponse($platform, "حذف  با موفقیت انجام شد");
    }
}
