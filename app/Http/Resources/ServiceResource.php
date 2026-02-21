<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'pc_model' => $this->pc_model,
            'issue' => $this->issue_description,
            'location' => [
                'lat' => $this->lat,
                'long' => $this->long,
            ],
            'status' => $this->status ? $this->status->status_name : 'N/A',
            'service_type' => $this->serviceType ? $this->serviceType->service_name : 'N/A',
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
