<?php

namespace Modules\Server\Transformers\Panel;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Server\Services\GenerateConfigService;

class SubscriptionDetailResource  extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $server_type = $this->service->server->type;
        $service = $this->service;
        $server_address = $service->server->address;

        if ($server_type == "marzban") {

            $res = Http::asForm()->post("$server_address/api/admin/token", [
                "username" => $service->server->username,
                "password" => $service->server->password,
                "grant_type" => "password"
            ]);

            $auth_res = json_decode($res->body());

            $auth_access_token = $auth_res->access_token;

            $sub_username = $this->code;

            $res = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->withToken($auth_access_token)->get("$server_address/api/user/{$sub_username}");
            $user_res = json_decode($res->body());

            $sub_link = "{$server_address}$user_res->subscription_url";

            return [
                'id' => $this->id,
                'service' => new ServiceResource($this->service),
                'user' => $this->user,
                'status' => $this->status,
                'name' => $this->name,
                'code' => $this->code,
                'subId' => $this->subId,
                'uuid' => $this->uuid,
                'slug' => $this->slug,
                'tgId' => $this->tgId,
                'flow' => $this->flow,
                'totalGB' => $this->totalGB,
                'limit' => $this->limit,
                'created_at' =>  formatGregorian($this->created_at),
                'updated_at' =>  formatGregorian($this->updated_at),
                'expire_at' =>  formatGregorian($this->expire_at),
                'config' => $sub_link,
                'sub_config' => $sub_link,
                'sub_qrcode' =>  GenerateConfigService::generateConfigQrCode($sub_link),
                'v2ray_qrcode' =>  GenerateConfigService::generateConfigQrCode($sub_link),
            ];
        } else {
            $sub_config = GenerateConfigService::generateSubscription($this->id);
            $v2ray_config = GenerateConfigService::generate($this->id);


            return [
                'id' => $this->id,
                'service' => new ServiceResource($this->service),
                'user' => $this->user,
                'status' => $this->status,
                'name' => $this->name,
                'code' => $this->code,
                'subId' => $this->subId,
                'uuid' => $this->uuid,
                'slug' => $this->slug,
                'tgId' => $this->tgId,
                'flow' => $this->flow,
                'totalGB' => $this->totalGB,
                'limit' => $this->limit,
                'created_at' =>  formatGregorian($this->created_at),
                'updated_at' =>  formatGregorian($this->updated_at),
                'expire_at' =>  formatGregorian($this->expire_at),
                'config' => $v2ray_config,
                'sub_config' => $sub_config,
                'sub_qrcode' =>  GenerateConfigService::generateConfigQrCode($sub_config),
                'v2ray_qrcode' =>  GenerateConfigService::generateConfigQrCode($v2ray_config),
            ];
        }
    }
}
