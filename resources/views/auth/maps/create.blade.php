@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 400px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        cursor: crosshair;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .flex-grow-1 {
        flex-grow: 1;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-map-marker-alt mr-2"></i> Add New Shop Location</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.shops.store') }}" method="POST" id="shopForm">
                @csrf
                <div class="form-group mb-3">
                    <label>Select Location <span class="text-danger">*</span></label>
                    <p class="text-muted small mb-2"><i class="fas fa-info-circle"></i> Click on the map or drag the marker</p>
                    <div id="map"></div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Latitude</label>
                        <input type="text" name="lat" id="lat" class="form-control @error('lat') is-invalid @enderror" value="{{ old('lat') }}" readonly required>
                        @error('lat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label>Longitude</label>
                        <input type="text" name="lng" id="lng" class="form-control @error('lng') is-invalid @enderror" value="{{ old('lng') }}" readonly required>
                        @error('lng')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="shopName">Shop Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="shopName" class="form-control @error('name') is-invalid @enderror"
                        placeholder="Enter shop name" value="{{ old('name') }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 w-25">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-save mr-1"></i> Save Shop Location
                    </button>
                    <a href="{{ route('admin.maps.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="container">
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @yield('content')
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Yangon ကို ဗဟိုပြုထားပါတယ်
        var defaultLat = 16.8331;
        var defaultLng = 96.1427;

        var map = L.map('map').setView([defaultLat, defaultLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        var currentMarker = null;

        function updateMarker(lat, lng) {
            // Validate coordinates
            if (isNaN(lat) || isNaN(lng)) {
                console.error('Invalid coordinates:', lat, lng);
                return;
            }

            // Remove old marker
            if (currentMarker) {
                map.removeLayer(currentMarker);
            }

            // Add new marker
            currentMarker = L.marker([lat, lng], {
                draggable: true,
                title: 'Drag to adjust position'
            }).addTo(map);

            // Update input values
            document.getElementById('lat').value = parseFloat(lat).toFixed(7);
            document.getElementById('lng').value = parseFloat(lng).toFixed(7);

            // Add tooltip
            currentMarker.bindTooltip('Shop Location', {
                permanent: false,
                direction: 'top'
            });

            // Handle drag event
            currentMarker.on('dragend', function(e) {
                var pos = e.target.getLatLng();
                document.getElementById('lat').value = pos.lat.toFixed(7);
                document.getElementById('lng').value = pos.lng.toFixed(7);
            });
        }

        // Map click event
        map.on('click', function(e) {
            updateMarker(e.latlng.lat, e.latlng.lng);
        });

        // Form validation
        document.getElementById('shopForm').addEventListener('submit', function(e) {
            var lat = document.getElementById('lat').value;
            var lng = document.getElementById('lng').value;

            if (!lat || !lng) {
                e.preventDefault();
                alert('Please select a location on the map before saving.');
                return false;
            }

            if (isNaN(parseFloat(lat)) || isNaN(parseFloat(lng))) {
                e.preventDefault();
                alert('Invalid location coordinates. Please select a valid location.');
                return false;
            }
        });

        // Validation error ပြန်တက်လာရင် တည်နေရာဟောင်းကို ပြန်ပြမယ်
        @if(old('lat') && old('lng'))
        try {
            var oldLat = parseFloat('{{ old('
                lat ') }}');
            var oldLng = parseFloat('{{ old('
                lng ') }}');

            if (!isNaN(oldLat) && !isNaN(oldLng)) {
                updateMarker(oldLat, oldLng);
                map.setView([oldLat, oldLng], 15);
            }
        } catch (e) {
            console.error('Error loading previous location:', e);
        }
        @endif
    });
</script>
@endpush