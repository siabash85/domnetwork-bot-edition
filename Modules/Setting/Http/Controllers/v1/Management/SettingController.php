<?php

namespace Modules\Setting\Http\Controllers\v1\Management;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Setting\Entities\Setting;
use Modules\Setting\Transformers\Management\SettingResource;
use Modules\Common\Http\Controllers\Api\ApiController;


class SettingController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $settings = settingRepo()->all();
        return SettingResource::collection($settings);
    }

    public function isJson($string)
    {
        if (is_string($string)) {
            json_decode($string, true);
            return json_last_error() === JSON_ERROR_NONE;
        }
        return false;
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $options = config('setting.options');
        $settings = [];
        foreach ($options as $option) {
            if ($request->filled($option)) {
                $value = $this->isJson($request->input($option)) ? json_encode(json_decode($request->input($option), true)) : json_encode($request->input($option));
                if ($request->file($option)) {
                    $setting = settingRepo()->find($option);
                    if ($setting) {
                        $setting->clearMediaCollectionExcept();
                    } else {
                        $setting = Setting::query()->create(['name' => $option]);
                    }
                    $setting->addMedia($request->{$option})->toMediaCollection();

                    $value = json_encode($setting->getFirstMediaUrl());
                }
                array_push($settings, ['name' => $option, 'value' => $value]);
            } else {
                $option_exists =   Setting::query()->where('name', $option)->first();
                if (!is_null($option_exists)) {
                    $option_exists->delete();
                }
            }
        }
        Setting::set($settings);
        return $this->successResponse($settings, trans('response.responses.200'));
    }
}
