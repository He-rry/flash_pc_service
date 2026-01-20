@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="container py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">Add New Shop Location</div>
        <div class="card-body">
            <form action="{{ route('shops.store') }}" method="POST">
                @csrf
                <input type="text" name="name" class="form-control mb-3" placeholder="Shop Name" required>

                <div id="map" style="height: 400px; margin-bottom: 20px; border-radius: 8px;"></div>

                <div class="row">
                    <div class="col-6"><input type="text" name="lat" id="lat" class="form-control" placeholder="Lat" readonly required></div>
                    <div class="col-6"><input type="text" name="lng" id="lng" class="form-control" placeholder="Lng" readonly required></div>
                </div>

                <button type="submit" class="btn btn-primary mt-3 w-100">Save Shop & Return to Planner</button>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([16.8331, 96.1427], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    var marker;
    map.on('click', function(e) {
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        document.getElementById('lat').value = e.latlng.lat;
        document.getElementById('lng').value = e.latlng.lng;
    });
</script>
@endsection