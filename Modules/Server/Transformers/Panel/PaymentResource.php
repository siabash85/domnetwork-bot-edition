<?php

namespace Modules\Server\Transformers\Panel;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'user' => $this->user,
            'payment_method' => $this->payment_method,
            'reference_code' => $this->reference_code,
            'amount' => round($this->amount),
            'status' => $this->status,
            'created_at' =>  formatGregorian($this->created_at),
        ];
    }
}
