<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'module' => $this->module,
            'description' => $this->description,
            'changes' => $this->changes, // cast လုပ်ထားပြီးသား array အတိုင်းထွက်လာမည်
            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? 'System',
            ],
            'shop' => [
                'id' => $this->shop->id ?? null,
                'name' => $this->shop->name ?? 'N/A',
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'ip_address' => $this->ip_address,
        ];
    }
}