<?php

namespace Modules\Server\Services;

use Illuminate\Support\Facades\Http;
use Modules\Server\Entities\Subscription;




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
}
