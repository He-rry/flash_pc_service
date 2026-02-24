document.addEventListener('DOMContentLoaded', function() {
    const mapElement = document.getElementById('map');
    if (!mapElement) return;
    const waypoints = JSON.parse(mapElement.dataset.waypoints || '[]');

    var map = L.map('map').setView([16.8331, 96.1427], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var pathPoints = [];
    var markers = [];

    if (waypoints && Array.isArray(waypoints) && waypoints.length > 0) {
        waypoints.forEach(function(point, index) {
            if (point && typeof point.lat === 'number' && typeof point.lng === 'number') {
                var latlng = [point.lat, point.lng];
                pathPoints.push(latlng);

                var marker = L.marker(latlng).bindTooltip("Stop " + (index + 1), {
                    permanent: true,
                    direction: 'top',
                    className: 'custom-tooltip'
                });

                var name = point.name ? String(point.name) : '';
                var region = point.region ? String(point.region) : '';

                markers.push({ marker: marker, name: name, region: region });
                marker.addTo(map);
            }
        });
        if (pathPoints.length >= 2) {
            var polyline = L.polyline(pathPoints, {
                color: '#e74c3c',
                weight: 5,
                opacity: 0.7,
                dashArray: '10, 5'
            }).addTo(map);
            map.fitBounds(polyline.getBounds(), { padding: [50, 50] });
        } else if (pathPoints.length === 1) {
            map.setView(pathPoints[0], 15);
        }

        // Filtering Logic
        const searchInput = document.getElementById('mapSearch');
        const regionSelect = document.getElementById('regionFilter');

        function filterMarkers() {
            var q = searchInput ? searchInput.value.toLowerCase().trim() : '';
            var region = regionSelect ? regionSelect.value : 'all';

            markers.forEach(function(obj) {
                var matchesName = q === '' || (obj.name && obj.name.toLowerCase().includes(q));
                var matchesRegion = (region === 'all') || (obj.region && obj.region === region);
                
                if (matchesName && matchesRegion) {
                    if (!map.hasLayer(obj.marker)) obj.marker.addTo(map);
                } else {
                    if (map.hasLayer(obj.marker)) map.removeLayer(obj.marker);
                }
            });
        }

        if (searchInput) searchInput.addEventListener('input', filterMarkers);
        if (regionSelect) regionSelect.addEventListener('change', filterMarkers);
        
        filterMarkers();
    } else {
        console.warn('No valid waypoints found');
    }
});