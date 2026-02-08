@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

<style>
    .map-centered-container {
        max-width: 80%;
        margin: 0 auto;
    }

    #map {
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        background: #f8f9fa;
        height: 400px !important;
    }

    .tower-icon {
        background: #28a745;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        cursor: pointer;
    }

    .number-icon {
        background: #e74c3c;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        text-align: center;
        line-height: 24px;
        font-size: 11px;
        font-weight: bold;
        border: 2px solid white;
    }

    .custom-div-icon {
        background: transparent;
        border: none;
    }

    .pulse {
        animation: pulse-animation 2s infinite;
    }

    @keyframes pulse-animation {
        0% {
            box-shadow: 0 0 0 0px rgba(40, 167, 69, 0.4);
        }

        100% {
            box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
        }
    }

    .leaflet-tooltip-shop {
        background: #333;
        color: #fff;
        border: none;
        padding: 4px 8px;
        font-size: 11px;
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="container-fluid py-4">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="map-centered-container">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-primary font-weight-bold">ROUTE PLANNER</h6>
                                <span id="route-info" class="badge badge-info p-2">0 km</span>
                            </div>
                        </div>
                        <div id="map"></div>
                        <div class="row mt-3 gx-2">
                            <div class="col-md-6">
                                <div class="card shadow-sm border-0 p-3 h-100">
                                    @can('manage-routes')
                                    <form action="{{ route('admin.maps.store') }}" method="POST">
                                        @csrf
                                        <input type="text" name="route_name" class="form-control form-control-sm mb-2" placeholder="Route Name" required>
                                        <input type="hidden" name="waypoints" id="waypoints_input">
                                        <input type="hidden" name="distance" id="distance_input">
                                        <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">Save Route</button>
                                        <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="location.reload()">Reset</button>
                                    </form>
                                    @else
                                    <p class="text-muted small mb-0">You can view the map and plan routes. Saving routes is restricted.</p>
                                    @endcan
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-header bg-light py-1 small font-weight-bold">VISIT SEQUENCE</div>
                                    <ul id="shop-list" class="list-group list-group-flush small" style="max-height: 120px; overflow-y: auto;">
                                        <li class="list-group-item text-muted p-2">Select shops...</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([16.8331, 96.1427], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    var allShops = [];
    var areaMarkers = [];
    var routeWaypoints = [];
    var currentPolyline = null;
    var startLatLng = null;
    var selectionRect = null;

    map.on('mousemove', function(e) {
        if (startLatLng && areaMarkers.length === 1) {
            var bounds = [startLatLng, e.latlng];
            if (!selectionRect) {
                selectionRect = L.rectangle(bounds, {
                    color: "#007bff",
                    weight: 1,
                    dashArray: '5, 10',
                    fillOpacity: 0.05
                }).addTo(map);
            } else {
                selectionRect.setBounds(bounds);
            }
        }
    });

    map.on('click', function(e) {
        if (!startLatLng) {
            startLatLng = e.latlng;
            addAreaMarker(e.latlng, "START", "#007bff");
            areaMarkers.push(e.latlng);
        } else if (startLatLng && areaMarkers.length === 1) {
            addAreaMarker(e.latlng, "END", "#333");
            areaMarkers.push(e.latlng);
            if (selectionRect) selectionRect.setStyle({
                color: '#28a745',
                fillOpacity: 0.1
            });
            filterShopsInArea(startLatLng, e.latlng);
        }
    });

    function filterShopsInArea(start, end) {
        var bounds = L.latLngBounds(start, end);
        allShops.forEach(function(shop) {
            var shopPos = L.latLng(parseFloat(shop.lat), parseFloat(shop.lng));

            // Area (Bounds) 
            if (bounds.contains(shopPos)) {
                var shopIcon = L.divIcon({
                    html: `<div class="tower-icon pulse"><i class="fas fa-broadcast-tower" style="font-size:10px;"></i></div>`,
                    className: 'custom-div-icon',
                    iconSize: [24, 24]
                });

                var sm = L.marker(shopPos, {
                    icon: shopIcon
                }).addTo(map);

                sm.bindTooltip(shop.name, {
                    direction: 'top',
                    className: 'leaflet-tooltip-shop'
                });

                sm.on('click', function(ev) {
                    L.DomEvent.stopPropagation(ev);
                    if (!routeWaypoints.some(p => p.lat === shopPos.lat && p.lng === shopPos.lng)) {
                        routeWaypoints.push(shopPos);
                        addNumberMarker(shopPos, routeWaypoints.length, "#e74c3c");
                        sm.setOpacity(0.2);
                        updateSidebar(shop.name, routeWaypoints.length);
                        drawStraightRoute();
                    }
                });
            }
        });
    }

    function drawStraightRoute() {
        if (currentPolyline) map.removeLayer(currentPolyline);
        if (routeWaypoints.length < 2) return;

        currentPolyline = L.polyline(routeWaypoints, {
            color: '#e74c3c',
            weight: 6,
            opacity: 0.8
        }).addTo(map);

        var totalDist = 0;
        for (var i = 0; i < routeWaypoints.length - 1; i++) {
            totalDist += routeWaypoints[i].distanceTo(routeWaypoints[i + 1]);
        }
        var distKm = (totalDist / 1000).toFixed(2);
        document.getElementById('route-info').innerHTML = `Direct Distance: ${distKm} km`;
        document.getElementById('distance_input').value = distKm + " km";
        document.getElementById('waypoints_input').value = JSON.stringify(routeWaypoints);
    }

    function addAreaMarker(latlng, label, color) {
        var icon = L.divIcon({
            html: `<div style="background:${color}; color:white; width:30px; height:30px; border-radius:50%; text-align:center; line-height:26px; font-size:10px; font-weight:bold; border:2px solid white;">${label}</div>`,
            className: 'custom-div-icon'
        });
        L.marker(latlng, {
            icon: icon
        }).addTo(map);
    }

    function addNumberMarker(latlng, label, color) {
        var icon = L.divIcon({
            html: `<div class="number-icon" style="background:${color};">${label}</div>`,
            className: 'custom-div-icon'
        });
        L.marker(latlng, {
            icon: icon,
            zIndexOffset: 1000
        }).addTo(map);
    }

    function updateSidebar(name, order) {
        var list = document.getElementById('shop-list');
        if (order === 1) list.innerHTML = '';
        list.innerHTML += `<li class="list-group-item d-flex justify-content-between"><span>${order}. ${name}</span></li>`;
    }

    function loadShops() {
        fetch("{{ route('api.shops.index') }}")
            .then(response => response.json())
            .then(payload => {
                allShops = payload.all_filtered || payload.data || [];
                console.log("Shops loaded:", allShops.length);
            })
            .catch(error => console.error('Error:', error));
    }
    loadShops();
</script>
@endsection