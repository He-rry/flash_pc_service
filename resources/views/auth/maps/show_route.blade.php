@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="container py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">{{ $route->route_name }} ({{ $route->distance ?? 'N/A' }})</h5>
            <a href="{{ route('admin.maps.saved') }}" class="btn btn-outline-secondary btn-sm">Back to List</a>
        </div>
        <div class="card-body p-0">
            <div id="map" style="height: 550px; width: 100%;"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map').setView([16.8331, 96.1427], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        @php
            $wpData = [];
            
            // Array ဖြစ်နေပြီးသားလား စစ်
            if (is_array($route->waypoints)) {
                $wpData = $route->waypoints;
            } 
            // String ဖြစ်နေရင် decode လုပ်
            elseif (is_string($route->waypoints) && !empty($route->waypoints)) {
                try {
                    $decoded = json_decode($route->waypoints, true);
                    $wpData = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
                } catch (\Exception $e) {
                    $wpData = [];
                }
            }
        @endphp

        // JS သို့ data ပို့ခြင်း
        var waypoints = @json($wpData);
        var pathPoints = [];

        // Waypoints ရှိမရှိ နှင့် valid ဖြစ်မဖြစ် စစ်ဆေးခြင်း
        if (waypoints && Array.isArray(waypoints) && waypoints.length > 0) {
            waypoints.forEach(function(point, index) {
                // lat, lng ရှိမရှိ စစ်ဆေးခြင်း
                if (point && typeof point.lat === 'number' && typeof point.lng === 'number') {
                    var lat = point.lat;
                    var lng = point.lng;
                    var latlng = [lat, lng];
                    pathPoints.push(latlng);

                    // Marker ထည့်ခြင်း
                    L.marker(latlng).addTo(map)
                        .bindTooltip("Stop " + (index + 1), { 
                            permanent: true, 
                            direction: 'top',
                            className: 'custom-tooltip'
                        });
                } else {
                    console.warn('Invalid waypoint at index ' + index, point);
                }
            });

            // မျဉ်းဆွဲခြင်း
            if (pathPoints.length >= 2) {
                var polyline = L.polyline(pathPoints, {
                    color: '#e74c3c',
                    weight: 5,
                    opacity: 0.7,
                    dashArray: '10, 5'
                }).addTo(map);

                map.fitBounds(polyline.getBounds(), { padding: [50, 50] });
            } else if (pathPoints.length === 1) {
                // Stop တစ်ခုပဲ ရှိရင် အဲ့ဒီနေရာကို zoom လုပ်
                map.setView(pathPoints[0], 15);
            }
        } else {
            // Waypoints မရှိရင် default location ပြခြင်း
            console.warn('No valid waypoints found');
            alert('No route data available to display.');
        }
    });
</script>

<style>
    .custom-tooltip {
        background-color: #2c3e50;
        border: none;
        border-radius: 4px;
        color: white;
        font-weight: bold;
        padding: 5px 10px;
    }
</style>
@endpush