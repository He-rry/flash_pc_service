let map, markerLayer, tempMarker = null;

const isLeafletReady = () => typeof L !== 'undefined';

document.addEventListener('DOMContentLoaded', function () {
    if (!isLeafletReady()) return;

    const mapElement = document.getElementById('map');
    if (mapElement) {
        // Map Init
        map = L.map('map').setView([16.8331, 96.1427], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        markerLayer = L.layerGroup().addTo(map);

        // Map Click to Fill Coordinates
        map.on('click', (e) => {
            const { lat, lng } = e.latlng;
            document.getElementById('form_lat').value = lat.toFixed(6);
            document.getElementById('form_lng').value = lng.toFixed(6);
            if (tempMarker) map.removeLayer(tempMarker);
            tempMarker = L.marker([lat, lng]).addTo(map).bindPopup("Selected").openPopup();
        });

        // Filter Listeners များကို စတင်ခြင်း
        setupFilterListeners();

        // Initial Data Load
        fetchData();

        setTimeout(() => { map.invalidateSize(); }, 500);
    }
});

function setupFilterListeners() {
    const searchInp = document.getElementById('mapSearch');
    const regionSel = document.getElementById('regionFilter');
    const periodRads = document.querySelectorAll('input[name="period"]');

    // Search Input (Debounce ပါပြီးသား)
    searchInp?.addEventListener('input', debounce(() => fetchData(1), 500));

    // Region Change
    regionSel?.addEventListener('change', () => fetchData(1));

    // Period Radio Change & Active Class Toggle
    periodRads.forEach(rad => {
        rad.addEventListener('change', function () {
            periodRads.forEach(r => r.parentElement.classList.remove('active'));
            if (this.checked) this.parentElement.classList.add('active');
            fetchData(1);
        });
    });
    document.addEventListener('click', function (e) {
        const link = e.target.closest('#paginationContainer a');
        if (link) {
            e.preventDefault();
            const page = new URL(link.href).searchParams.get('page');
            fetchData(page);
        }
    });
}
function updateExportLink() {
    const searchVal = document.getElementById('mapSearch')?.value || '';
    const regionVal = document.getElementById('regionFilter')?.value || '';
    const periodVal = document.querySelector('input[name="period"]:checked')?.value || 'all';

    const params = new URLSearchParams({
        search: searchVal,
        region: regionVal,
        period: periodVal
    });

    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.href = `${window.appConfig.exportUrl}?${params.toString()}`;
    }
}

async function fetchData(page = 1) {
    updateExportLink();
    const searchVal = document.getElementById('mapSearch')?.value || '';
    const regionVal = document.getElementById('regionFilter')?.value || '';
    const periodVal = document.querySelector('input[name="period"]:checked')?.value || 'all';

    const params = new URLSearchParams({
        search: searchVal,
        region: regionVal,
        period: periodVal,
        page: page,
        _t: new Date().getTime()
    });

    // Export Link Update
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.href = `${window.appConfig.exportUrl}?${params.toString()}`;
    }

    try {
        const response = await fetch(`${window.appConfig.apiUrl}?${params.toString()}`);
        const result = await response.json();

        renderTable(result.data || []);
        renderMarkers(result.all_filtered || result.data || []);
        renderPagination(result);
    } catch (e) {
        console.error('Fetch Error:', e);
    }
}


function renderTable(shops) {
    const tbody = document.getElementById('shopTableBody');
    if (!shops.length) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4">ဒေတာမတွေ့ပါ။</td></tr>';
        return;
    }

    tbody.innerHTML = shops.map(shop => `
        <tr>
            <td class="pl-4"><strong>${escapeHtml(shop.name)}</strong></td>
            <td>${parseFloat(shop.lat).toFixed(5)}, ${parseFloat(shop.lng).toFixed(5)}</td>
            <td class="text-center"><span class="badge badge-light border">${escapeHtml(shop.region || '-')}</span></td>
            <td class="text-right pr-4 small text-muted">${new Date(shop.created_at).toLocaleDateString('en-GB')}</td>
        </tr>
    `).join('');
}

function renderMarkers(shops) {
    if (!markerLayer) return;
    markerLayer.clearLayers();

    shops.forEach(shop => {
        const lat = parseFloat(shop.lat), lng = parseFloat(shop.lng);
        if (!isNaN(lat) && !isNaN(lng)) {
            const towerIcon = L.divIcon({
                html: `<div class="tower-icon"><i class="fas fa-broadcast-tower"></i></div>`,
                className: 'custom-div-icon', iconSize: [30, 30]
            });
            L.marker([lat, lng], { icon: towerIcon }).addTo(markerLayer).bindTooltip(shop.name);
        }
    });
}

function renderPagination(data) {
    const container = document.getElementById('paginationContainer');
    if (!data.links || data.last_page <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '<ul class="pagination mb-0">';
    data.links.forEach(link => {
        html += `
            <li class="page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}">
                <a class="page-link" href="${link.url || '#'}">${link.label.replace('&laquo;', '«').replace('&raquo;', '»')}</a>
            </li>`;
    });
    html += '</ul>';
    container.innerHTML = html;
}

function debounce(func, wait) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}