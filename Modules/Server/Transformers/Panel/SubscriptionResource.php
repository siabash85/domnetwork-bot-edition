<?php

namespace Modules\Server\Transformers\Panel;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
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


        ];
    }
}
