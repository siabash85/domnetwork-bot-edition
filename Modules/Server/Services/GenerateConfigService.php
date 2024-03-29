<?php

namespace Modules\Server\Services;

use Illuminate\Support\Carbon;
use Modules\Order\Entities\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\Server\Entities\Subscription;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Modules\Server\Entities\PackageDuration;




class GenerateConfigService
{
    public static function generate($subscription)
    {
        $subscription = Subscription::query()->find($subscription);
        $server_address = $subscription->service->server->address;
        $res = Http::post("$server_address/login", [
            "username" => $subscription->service->server->username,
            "password" => $subscription->service->server->password
        ]);
        $cookieJar = $res->cookies();
        $cookiesArray = [];
        foreach ($cookieJar as $cookie) {
            $cookiesArray[] = $cookie->getName() . '=' . $cookie->getValue();
        }
        $cookiesString = implode('; ', $cookiesArray);
        $server_inbound_id = $subscription->service->server->inbound;
        try {
            $inbound = Http::withHeaders(['Cookie' => $cookiesString])->get("$server_address/xui/API/inbounds/get/$server_inbound_id");
            $inbound_res = json_decode($inbound->body());
            $inbound_obj = $inbound_res->obj;
            $network = json_decode($inbound_obj->streamSettings)->network;
            $inbound_port = $inbound_obj->port;
            $inbound_remark = $inbound_obj->remark;
            $parts = parse_url($server_address);
            $clean_server_url = $parts['host'];
            if ($inbound->successful()) {
                $service_link = "vless://$subscription->uuid@$clean_server_url:$inbound_port?type=$network&path=%2F&security=none#$inbound_remark-$subscription->code";
                return $service_link;
            }
        } catch (\Throwable $th) {
            return null;
            // return $th->getMessage();
        }
    }
    public static function generateSubscription($subscription)
    {
        $subscription = Subscription::query()->find($subscription);
        $server_address = $subscription->service->server->address;
        $res = Http::post("$server_address/login", [
            "username" => $subscription->service->server->username,
            "password" => $subscription->service->server->password
        ]);
        $cookieJar = $res->cookies();
        $cookiesArray = [];
        foreach ($cookieJar as $cookie) {
            $cookiesArray[] = $cookie->getName() . '=' . $cookie->getValue();
        }
        $cookiesString = implode('; ', $cookiesArray);
        $server_inbound_id = $subscription->service->server->inbound;
        try {
            $inbound = Http::withHeaders(['Cookie' => $cookiesString])->get("$server_address/xui/API/inbounds/get/$server_inbound_id");
            $inbound_res = json_decode($inbound->body());
            $inbound_obj = $inbound_res->obj;
            $network = json_decode($inbound_obj->streamSettings)->network;
            $inbound_port = $inbound_obj->port;
            $inbound_remark = $inbound_obj->remark;
            $parts = parse_url($server_address);
            $clean_server_url = $parts['host'];
            if ($inbound->successful()) {
                $service_link = "https://$clean_server_url:23590/sub/$subscription->subId?name=$inbound_remark-$subscription->code";
                return $service_link;
            }
        } catch (\Throwable $th) {
            return null;
            // return $th->getMessage();
        }
    }
    public static function getClientTraffics($subscription)
    {
        $subscription = Subscription::query()->find($subscription);
        $server_address = $subscription->service->server->address;
        $res = Http::post("$server_address/login", [
            "username" => $subscription->service->server->username,
            "password" => $subscription->service->server->password
        ]);;
        $cookieJar = $res->cookies();
        $cookiesArray = [];
        foreach ($cookieJar as $cookie) {
            $cookiesArray[] = $cookie->getName() . '=' . $cookie->getValue();
        }
        $cookiesString = implode('; ', $cookiesArray);
        $sub_code = $subscription->code;
        $response = Http::withHeaders([
            'Cookie' => $cookiesString,
        ])->get("$server_address/xui/API/inbounds/getClientTraffics/{$sub_code}");
        $inbound_res = json_decode($response->body());
        $inbound_obj = $inbound_res->obj;
        return $inbound_obj;
    }

    public static function generateConfigQrCode($link)
    {
        $qrCode = QrCode::format('png')->margin(2)->size(300)->generate($link);
        $path = 'public/images/qrcodes/' . uniqid() . '.png';
        Storage::put($path, $qrCode);
        $qrcode = Storage::url($path);
        return $qrcode;
    }

    public static function extensionMarzbanService($package_duration, $volume, $subscription_id)
    {
        $sub = Subscription::query()->where('id', $subscription_id)->first();
        $user = $sub->user;
        $sub_package_duration = PackageDuration::query()->where('id', $package_duration)->first();
        $server_address = $sub->service->server->address;
        $service = $sub->service;
        $res = Http::asForm()->post("$server_address/api/admin/token", [
            "username" => $service->server->username,
            "password" => $service->server->password,
            "grant_type" => "password"
        ]);

        $auth_res = json_decode($res->body());

        $auth_access_token = $auth_res->access_token;
        $sub_username = $sub->code;

        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->withToken($auth_access_token)->get("$server_address/api/user/{$sub_username}");
        $user_res = json_decode($res->body());
        $volume_consumed = $user_res->used_traffic;
        $total = $user_res->data_limit;
        $remaining_volume = $total - $volume_consumed;
        $extension_service_total = $volume * pow(1024, 3);
        $today = Carbon::now();
        $givenDate = Carbon::parse($sub->expire_at)->addDays($sub_package_duration->value);
        $new_volume = $remaining_volume + $extension_service_total;
        $added_deadline = $sub_package_duration->value;
        $settings = [
            "username" => $sub_username,
            "note" => "",
            "data_limit_reset_strategy" => "no_reset",
            "data_limit" => $new_volume,
            "expire" => $givenDate->timestamp,
            "status" => "active",
            "proxies" => array(
                "vless" => array(
                    "flow" => ""
                ),
                "trojan" => array(),
                "shadowsocks" => array(
                    "method" => "chacha20-ietf-poly1305"
                ),
                "vmess" => array()
            ),
            "inbounds" => array(
                "vmess" => array(
                    "VMess TCP",
                    "VMess Websocket"
                ),
                "vless" => array(
                    "VLESS TCP REALITY",
                    "VLESS GRPC REALITY"
                ),
                "trojan" => array(
                    "Trojan Websocket TLS"
                ),
                "shadowsocks" => array(
                    "Shadowsocks TCP"
                )
            )
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->withToken($auth_access_token)->put("$server_address/api/user/{$sub_username}", $settings);

        if ($response->successful()) {
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
        }
    }
}
