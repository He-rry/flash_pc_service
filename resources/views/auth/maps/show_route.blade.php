@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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

@section('content')
@php
    // Data Processing
    $wpData = [];
    if (is_array($route->waypoints)) {
        $wpData = $route->waypoints;
    } elseif (is_string($route->waypoints) && !empty($route->waypoints)) {
        $decoded = json_decode($route->waypoints, true);
        $wpData = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
    }
@endphp

<div class="container py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">{{ $route->route_name }} ({{ $route->distance ?? 'N/A' }})</h5>
            <a href="{{ route('admin.maps.saved') }}" class="btn btn-outline-secondary btn-sm">Back to List</a>
        </div>
        <div class="card-body p-0">
            <div id="map" 
                 data-waypoints='@json($wpData)' 
                 style="height: 550px; width: 100%;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/route-viewer.js') }}"></script>
@endpush