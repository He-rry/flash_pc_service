<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RouteResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'route_name' => $this->route_name,
            'waypoints' => $this->waypoints, // Cast to array in Model
            'distance' => $this->distance,
            'duration' => $this->duration,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
