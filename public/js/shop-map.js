let map, markerLayer, tempMarker = null;

const isLeafletReady = () => typeof L !== 'undefined';

document.addEventListener('DOMContentLoaded', function () {
    if (!isLeafletReady()) return;

    const mapElement = document.getElementById('map');
    if (mapElement) {
        map = L.map('map').setView([16.8331, 96.1427], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        markerLayer = L.layerGroup().addTo(map);
        map.on('click', (e) => {
            const { lat, lng } = e.latlng;
            document.getElementById('form_lat').value = lat.toFixed(6);
            document.getElementById('form_lng').value = lng.toFixed(6);
            if (tempMarker) map.removeLayer(tempMarker);
            tempMarker = L.marker([lat, lng]).addTo(map).bindPopup("Selected Location").openPopup();
        });
        setupFilterListeners();
        fetchData();
        setTimeout(() => { map.invalidateSize(); }, 500);
    }
});

function setupFilterListeners() {
    const searchBtn = document.getElementById('searchBtn');
    const periodRads = document.querySelectorAll('input[name="period"]');
    periodRads.forEach(rad => {
        rad.addEventListener('change', function () {
            periodRads.forEach(r => r.parentElement.classList.remove('active'));
            if (this.checked) this.parentElement.classList.add('active');
        });
    });

    // Search Button 
    searchBtn?.addEventListener('click', () => {
        fetchData(1);
    });
    document.getElementById('filterForm')?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchData(1);
        }
    });

    // Pagination Click 
    document.addEventListener('click', function (e) {
        const link = e.target.closest('#paginationContainer a');
        if (link) {
            e.preventDefault();
            const page = new URL(link.href).searchParams.get('page');
            fetchData(page);
        }
    });
}
async function fetchData(page = 1) {
    const fromDate = document.getElementById('from_date')?.value || '';
    const toDate = document.getElementById('to_date')?.value || '';
    const searchVal = document.getElementById('mapSearch')?.value || '';
    const regionVal = document.getElementById('regionFilter')?.value || '';
    const periodElement = document.querySelector('input[name="period"]:checked');
    const periodVal = periodElement ? periodElement.value : 'all';
    const params = new URLSearchParams({
        search: searchVal,
        region: regionVal,
        period: periodVal,
        from_date: fromDate,
        to_date: toDate,
        page: page
    });
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.href = `${window.appConfig.exportUrl}?${params.toString()}`;
    }

    try {
        const response = await fetch(`${window.appConfig.apiUrl}?${params.toString()}`);
        const result = await response.json();
        const total = result.total || 0;
        const countBadge = document.getElementById('filterCountBadge');
        if (countBadge) {
            countBadge.innerText = `${total} Shops`;
            countBadge.className = total === 0 ? 'badge badge-danger shadow-sm' : 'badge badge-primary shadow-sm';
        }
        if (exportBtn) {
            if (total === 0) {
                exportBtn.classList.add('disabled', 'btn-secondary');
                exportBtn.style.pointerEvents = 'none';
                exportBtn.style.opacity = '0.6';
            } else {
                exportBtn.classList.remove('disabled', 'btn-secondary');
                exportBtn.style.pointerEvents = 'auto';
                exportBtn.style.opacity = '1';
            }
        }
        const tbody = document.getElementById('shopTableBody');
        if (total === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center py-5 text-danger font-weight-bold"><i class="fas fa-exclamation-circle mr-2"></i>ကိုက်ညီသော ဒေတာ မရှိပါ။</td></tr>';
        } else {
            renderTable(result.data || []);
        }

        renderMarkers(result.all_filtered || result.data || []);
        renderPagination(result);

    } catch (e) {
        console.error('Fetch Error:', e);
    }
}

function renderTable(shops) {
    const tbody = document.getElementById('shopTableBody');
    tbody.innerHTML = shops.map(shop => {
        const adminName = shop.admin ? shop.admin.name : 'System';

        return `
            <tr>
                <td class="pl-4"><strong>${shop.name}</strong></td>
                <td>${parseFloat(shop.lat).toFixed(5)}, ${parseFloat(shop.lng).toFixed(5)}</td>
                <td class="text-center"><span class="badge badge-light border">${shop.region || '-'}</span></td>
                <td class="text-center">
                    <span class="badge badge-info-soft text-info px-2 py-1" style="background-color: #e0f2ff; font-size: 0.85rem;">
                        <i class="fas fa-user-check mr-1"></i>${adminName}
                    </span>
                </td>
                <td class="text-right pr-4 small text-muted">${new Date(shop.created_at).toLocaleDateString('en-GB')}</td>
            </tr>
        `;
    }).join('');
}

function renderMarkers(shops) {
    if (!markerLayer) return;
    markerLayer.clearLayers();

    shops.forEach(shop => {
        const lat = parseFloat(shop.lat), lng = parseFloat(shop.lng);
        if (!isNaN(lat) && !isNaN(lng)) {
            const icon = L.divIcon({
                html: `<div class="tower-icon"><i class="fas fa-broadcast-tower"></i></div>`,
                className: 'custom-div-icon',
                iconSize: [30, 30]
            });
            L.marker([lat, lng], { icon: icon }).addTo(markerLayer).bindTooltip(shop.name);
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