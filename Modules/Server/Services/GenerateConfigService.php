<?php

namespace Modules\Server\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\Server\Entities\Subscription;
use SimpleSoftwareIO\QrCode\Facades\QrCode;




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
        $qrCode = QrCode::format('svg')->generate($link);
        $path = 'public/images/qrcodes/' . uniqid() . '.svg';
        Storage::put($path, $qrCode);
        $qrcode = Storage::url($path);
        return $qrcode;
    }
}
