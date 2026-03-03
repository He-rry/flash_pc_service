<?php

namespace App\Http\Controllers\Traits;

use App\Events\ActivityLogged;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $nameField = self::getNameField($model);
            $description = "Added new " . class_basename($model) . ": " . ($model->$nameField ?? $model->id);
            self::dispatchLog($model, 'ADD', $description);
        });
        static::updated(function ($model) {
            $changes = [];
            foreach ($model->getChanges() as $field => $newValue) {
                if ($field === 'updated_at') continue;

                $oldValue = $model->getOriginal($field);
                $fieldName = ucfirst(str_replace('_', ' ', $field));
                $changes[] = "$fieldName ($oldValue -> $newValue)";
            }

            if (count($changes) > 0) {
                $description = "Updated fields: " . implode(', ', $changes);
                self::dispatchLog($model, 'UPDATE', $description, $model->getChanges());
            }
        });
        static::deleting(function ($model) {
            $nameField = self::getNameField($model);
            $description = "Deleted " . class_basename($model) . ": " . ($model->$nameField ?? "ID " . $model->id);
            self::dispatchLog($model, 'DELETE', $description);
        });
    }

    protected static function dispatchLog($model, $action, $desc, $changes = null)
    {
        event(new ActivityLogged($model, $action, $desc, $changes));
    }

    protected static function getNameField($model)
    {
        if (isset($model->service_name)) return 'service_name';
        if (isset($model->name)) return 'name';
        return 'id';
    }
}
