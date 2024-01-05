<?php

namespace Modules\Server\Http\Controllers\Panel;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Entities\User;
use Illuminate\Support\Facades\Http;
use Modules\Server\Entities\Service;
use Modules\Server\Entities\Subscription;
use App\Telegram\Keyboard\KeyboardHandler;
use Telegram\Bot\Laravel\Facades\Telegram;
use Modules\Server\Services\GenerateConfigService;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\Server\Transformers\Panel\SubscriptionResource;
use Modules\Server\Transformers\Panel\SubscriptionDetailResource;

class SubscriptionController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->is_partner) {
            $services = Subscription::query()->whereHas("user", function ($q) use ($user) {
                $q->where("partner_id", $user->id);
            })->get();
        } else {
            $services = Subscription::query()->get();
        }

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
        $service = Service::query()
            ->where('server_id', $request->server_id)
            ->where('package_duration_id', $request->package_duration_id)
            ->where('package_id', $request->package_id)
            ->where('status', "active")
            ->first();

        $rand_code = Str::random(8);
        $subscription =  Subscription::query()->create([
            'service_id' => $service->id,
            'user_id' => $request->user_id,
            'package_id' => $request->package_id,
            'status' => "active",
            'name' => $request->name,
            'code' => $rand_code,
            'slug' => $request->name . " - " . $rand_code,
            "expire_at" => now()->addDays($service->package_duration->name),
            'uuid' => Str::uuid(),
            'subId' => Str::random(16)
        ]);

        $server_address = $service->server->address;
        $res = Http::post("$server_address/login", [
            "username" => $service->server->username,
            "password" => $service->server->password
        ]);
        $cookieJar = $res->cookies();
        $cookiesArray = [];
        foreach ($cookieJar as $cookie) {
            $cookiesArray[] = $cookie->getName() . '=' . $cookie->getValue();
        }
        $cookiesString = implode('; ', $cookiesArray);
        $package_duration_time = $service->package_duration->value > 0 ? -$service->package_duration->value * 24 * 60 * 60 * 1000 : 0;
        $settings = [
            "clients" => [
                [
                    "id" => $subscription->uuid,
                    "flow" => "",
                    "email" => $subscription->code,
                    "limitIp" => 0,
                    "totalGB" => $service->package->value > 0 ? $service->package->value * pow(1024, 3) : 0,
                    "expire_at" => createDatetimeFromFormat($request->expire_date, 'Y/m/d'),
                    "enable" => true,
                    "tgId" => "",
                    "subId" => $subscription->subId
                ]
            ]
        ];
        $server_inbound_id = $service->server->inbound;
        $response = Http::withHeaders([
            'Cookie' => $cookiesString,
        ])->post("$server_address/panel/inbound/addClient", [
            "id" => intval($server_inbound_id),
            "settings" => json_encode($settings)
        ]);
        try {

            $inbound = Http::withHeaders(['Cookie' => $cookiesString])->get("$server_address/xui/API/inbounds/get/$server_inbound_id");
            $inbound_res = json_decode($inbound->body());
            $inbound_obj = $inbound_res->obj;
            $network = json_decode($inbound_obj->streamSettings)->network;
            $inbound_port = $inbound_obj->port;
            $inbound_remark = $inbound_obj->remark;
            if ($response->successful()) {
                $location = $service->server->name;
                $volume = $service->package->name;
                $code = $subscription->code;
                $expire_date = $subscription->expire_at;
                $parts = parse_url($server_address);
                $clean_server_url = $parts['host'];
                $sub_link = GenerateConfigService::generateSubscription($subscription->id);
                $service_link = "vless://$subscription->uuid@$clean_server_url:$inbound_port?type=$network&path=%2F&security=none#$inbound_remark-$subscription->code";
                // $message = "📣 * سرویس شما با موفقیت ایجاد شد*\n\n" .
                //     "💎 *کد سرویس:* `$code`\n" .
                //     "🌎 *لوکیشن:* `$location`\n" .
                //     "⏳ *تاریخ انقضا:* `$expire_date`\n" .
                //     "♾ *حجم کل:* `$volume` \n\n" .
                //     "📌 *لینک v2ray* \n\n" .
                //     "`$service_link` \n\n" .
                //     "📌 *لینک اشتراک* \n\n" .
                //     "`$sub_link` \n\n";

                $reponse_data = [
                    'link' => $service_link,
                    'sub' => $sub_link,
                ];
                return $this->successResponse($reponse_data, "ایجاد  با موفقیت انجام شد");
            }
        } catch (\Throwable $th) {
            $reponse_data = [];
            return $this->successResponse($subscription, "خطا در ایجاد اشتراک");
        }
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
