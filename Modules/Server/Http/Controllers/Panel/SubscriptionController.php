<?php

namespace Modules\Server\Http\Controllers\Panel;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Entities\User;
use Modules\Order\Entities\Order;
use Illuminate\Support\Facades\Http;
use Modules\Server\Entities\Service;
use Modules\Server\Entities\Subscription;
use App\Telegram\Keyboard\KeyboardHandler;
use Telegram\Bot\Laravel\Facades\Telegram;
use Modules\Server\Entities\PackageDuration;
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
        $user = User::find($request->user_id);

        $service = Service::query()
            ->where('server_id', $request->server_id)
            ->where('package_duration_id', $request->package_duration_id)
            ->where('package_id', $request->package_id)
            ->where('status', "active")
            ->first();

        if ($service->price > $user->wallet) {
            return $this->successResponse($user->wallet, "❌ موجودی شما برای خرید این سرویس کافی نمیباشد ", 204);
        } else {
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
                        // "expire_at" => createDatetimeFromFormat($request->expire_date, 'Y/m/d'),
                        "expire_at" => now()->addDays($service->package_duration->name),
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
            $order = Order::query()->create([
                "user_id" => $user->id,
                "service_id" => $service->id,
                "status" => "success",
                "payable_price" => $service->price,
                "price" => $service->price,
                "type" => "purchase"
            ]);
            $user->decrement("wallet", $service->price);
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
    }

    public function extension(Request $request)
    {
        $sub = Subscription::query()->where('id', $request->subscription_id)->first();
        $sub_package_duration = PackageDuration::query()->where('id', $request->package_duration_id)->first();
        $user = $sub->user;
        $extension_price = $sub_package_duration->price *  intval($request->volume);
        if ($extension_price > $user->wallet) {
            return $this->successResponse($extension_price, "❌ موجودی شما برای خرید این سرویس کافی نمیباشد ", 204);
        } else {
            try {
                $server_address = $sub->service->server->address;
                $user->decrement("wallet", $extension_price);
                $res = Http::post("$server_address/login", [
                    "username" => $sub->service->server->username,
                    "password" => $sub->service->server->password
                ]);;
                $cookieJar = $res->cookies();
                $cookiesArray = [];
                foreach ($cookieJar as $cookie) {
                    $cookiesArray[] = $cookie->getName() . '=' . $cookie->getValue();
                }
                $cookiesString = implode('; ', $cookiesArray);
                $inbound_obj = GenerateConfigService::getClientTraffics($sub->id);
                $volume_consumed = $inbound_obj->up + $inbound_obj->down;
                $total = $inbound_obj->total;
                $remaining_volume = $total - $volume_consumed;
                $extension_service_total = $request->volume * pow(1024, 3);
                $today = Carbon::now();
                $givenDate = Carbon::parse($sub->expire_at);
                $diffInDays = $today->diffInDays($givenDate);
                if ($givenDate->isPast()) {
                    $diffInDays = 0;
                    $new_volume = $extension_service_total;
                } else {
                    $new_volume = $remaining_volume + $extension_service_total;
                }
                $added_deadline = $sub_package_duration->value;
                $package_duration_time = ($diffInDays + $added_deadline) * 24 * 60 * 60 * 1000;
                $settings = [
                    "clients" => [
                        [
                            "id" => $sub->uuid,
                            "flow" => "",
                            "email" => $sub->code,
                            "limitIp" => 0,
                            "totalGB" => $new_volume,
                            "expiryTime" => -$package_duration_time,
                            "enable" => true,
                            "tgId" => "",
                            "subId" => $sub->subId
                        ]
                    ]
                ];
                $server_inbound_id = $sub->service->server->inbound;
                $response = Http::withHeaders([
                    'Cookie' => $cookiesString,
                ])->post("$server_address/panel/inbound/updateClient/$sub->uuid", [
                    "id" => intval($server_inbound_id),
                    "settings" => json_encode($settings)
                ]);
                $code = $sub->code;
                if ($givenDate->isPast()) {
                    Subscription::query()->where('id', $sub->id)->update(
                        ['expire_at' => now()->addDays($added_deadline)]
                    );
                } else {
                    Subscription::query()->where('id', $sub->id)->update(
                        ['expire_at' => Carbon::parse($sub->expire_at)->addDays($added_deadline)]
                    );
                }
                $order = Order::query()->create([
                    "user_id" => $user->id,
                    "service_id" => $sub->service->id,
                    "status" => "success",
                    "payable_price" => $sub->service->price,
                    "price" => $sub->service->price,
                    "type" => "extension"
                ]);
                // if (is_null($user->uid)) {
                //     $message = "✅ سرویس با کد {$code} تمدید شد.";
                //     Telegram::sendMessage([
                //         'text' => $message,
                //         "chat_id" => $user->uid,
                //         'reply_markup' => KeyboardHandler::home(),
                //     ]);
                // }

                return $this->successResponse($sub, "");
            } catch (\Throwable $th) {
                // dd($th->getMessage());
                return $this->errorResponse($th->getMessage(), "");
            }
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
