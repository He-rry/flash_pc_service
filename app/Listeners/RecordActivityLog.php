<?php

namespace App\Listeners;

use App\Events\ActivityLogged;
use App\Models\ActivityLog;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

class RecordActivityLog
{
    public function handle(ActivityLogged $event): void
    {
        if (!auth()->check()) return;

        $shopId = null;

        if ($event->model instanceof Shop && $event->model->exists) {
            $shopId = $event->model->id;
        }
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => $event->action,
            'description' => $event->description,
            'module'      => strtoupper(class_basename($event->model)),
            'shop_id'     => $shopId,
            'changes'     => $event->changes,
            'ip_address'  => request()->ip(),
        ]);
    }
}
