@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

<style>
    #map {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background: #f8f9fa;
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
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
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
        <div class="col-md-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary small font-weight-bold">SHOP-TO-SHOP ROUTE PLANNER</h5>
                    <div id="route-info-card" class="badge badge-light p-2 shadow-sm border">
                        <span id="route-info" class="text-dark">Distance: 0 km</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 650px; width: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h6 class="font-weight-bold mb-3 text-secondary small">SETTINGS</h6>
                    <form action="{{ route('routes.store') }}" method="POST">
                        @csrf
                        <input type="text" name="route_name" class="form-control mb-3" placeholder="Route Name" required>
                        <input type="hidden" name="waypoints" id="waypoints_input">
                        <input type="hidden" name="distance" id="distance_input">
                        <input type="hidden" name="duration" id="duration_input">
                        <button type="submit" class="btn btn-primary w-100 mb-2">Save Route</button>
                        <button type="button" class="btn btn-outline-danger w-100" onclick="location.reload()">Reset Map</button>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light small font-weight-bold">VISIT SEQUENCE</div>
                <ul id="shop-list" class="list-group list-group-flush small" style="max-height: 250px; overflow-y: auto;">
                    <li class="list-group-item text-muted">Select 2 or more shops to see route.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<script>
    var map = L.map('map').setView([16.8331, 96.1427], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    var allShops = @json($shops);
    var areaMarkers = []; // START/END points only
    var routeWaypoints = []; // ONLY selected shops for routing
    var routingControl = null;
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
        allShops.forEach(shop => {
            var shopPos = L.latLng(parseFloat(shop.lat), parseFloat(shop.lng));
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
                        var orderNum = routeWaypoints.length;

                        // Shop အတွက် Numbered Marker ချမယ်
                        addNumberMarker(shopPos, orderNum, "#e74c3c");
                        sm.setOpacity(0.2);
                        updateSidebar(shop.name, orderNum);
                        drawRoute(); // Route ဆွဲမယ်
                    }
                });
            }
        });
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

    function drawRoute() {
        if (routingControl) map.removeControl(routingControl);

        // ဆိုင် ၂ ဆိုင်နဲ့ အထက်ရှိမှ Shop-to-Shop ဆွဲမယ်
        if (routeWaypoints.length < 2) return;

        routingControl = L.Routing.control({
            waypoints: routeWaypoints.map(p => L.latLng(p.lat, p.lng)),
            show: false,
            addWaypoints: false,
            draggableWaypoints: false,
            createMarker: function() {
                return null;
            }, // အပေါ်က Manual Marker တွေရှိလို့ hide ထားမယ်
            lineOptions: {
                styles: [{
                    color: '#e74c3c',
                    opacity: 0.7,
                    weight: 6
                }]
            }
        }).on('routesfound', function(e) {
            var s = e.routes[0].summary;
            var dist = (s.totalDistance / 1000).toFixed(2);
            document.getElementById('route-info').innerHTML = `Route Distance: ${dist} km`;
            document.getElementById('distance_input').value = dist + " km";
            document.getElementById('waypoints_input').value = JSON.stringify(routeWaypoints);
        }).addTo(map);
    }

    function updateSidebar(name, order) {
        var list = document.getElementById('shop-list');
        if (order === 1) list.innerHTML = '';
        list.innerHTML += `<li class="list-group-item d-flex justify-content-between">
            <span>${order}. ${name}</span>
            <i class="fas fa-store text-danger"></i>
        </li>`;
    }
</script>
@endsection