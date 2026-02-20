<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'lat'        => $this->lat,
            'lng'        => $this->lng,
            'address'    => $this->address,
            'region'     => $this->region,
            'added_by'   => $this->admin->name ?? 'System', //
            'waypoints'  => $this->waypoints, //
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
