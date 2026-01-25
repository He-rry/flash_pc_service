<?php

namespace App\Helpers;

class Waypoints
{
    /**
     * Normalize waypoints input (JSON string or array) into validated array.
     * Each waypoint will be ['name' => string|null, 'lat' => float, 'lng' => float, 'region' => string|null]
     * Invalid entries (missing lat/lng) are skipped.
     *
     * @param mixed $input
     * @return array
     */
    public static function normalize($input): array
    {
        $items = [];

        if (is_string($input)) {
            $decoded = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $items = $decoded;
            } else {
                return [];
            }
        } elseif (is_array($input)) {
            $items = $input;
        } else {
            return [];
        }

        $result = [];
        foreach ($items as $item) {
            if (!is_array($item)) continue;

            $lat = $item['lat'] ?? ($item[1] ?? null);
            $lng = $item['lng'] ?? ($item[2] ?? null);

            if ($lat === null || $lng === null) continue;

            if (is_numeric($lat)) $lat = (float)$lat;
            if (is_numeric($lng)) $lng = (float)$lng;

            if (!is_float($lat) && !is_int($lat)) continue;
            if (!is_float($lng) && !is_int($lng)) continue;

            $result[] = [
                'name' => isset($item['name']) ? (string)$item['name'] : (isset($item[0]) ? (string)$item[0] : null),
                'lat' => (float)$lat,
                'lng' => (float)$lng,
                'region' => isset($item['region']) ? (string)$item['region'] : (isset($item[3]) ? (string)$item[3] : null),
            ];
        }

        return $result;
    }
}
