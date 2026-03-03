<?php
namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityLogged
{
    use Dispatchable, SerializesModels;

    public $model, $action, $description, $changes;

    public function __construct($model, $action, $description, $changes = null)
    {
        $this->model = $model;
        $this->action = $action;
        $this->description = $description;
        $this->changes = $changes;
    }
}