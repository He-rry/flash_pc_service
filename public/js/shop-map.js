let map, markerLayer, tempMarker = null;
let editMarker = null;

const isLeafletReady = () => typeof L !== 'undefined';

document.addEventListener('DOMContentLoaded', function () {
    if (!isLeafletReady()) return;

    const mapElement = document.getElementById('map');
    if (mapElement) {
        // Map ကို ရန်ကုန်တည်နေရာဖြင့် အစပြုခြင်း
        map = L.map('map').setView([16.8331, 96.1427], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{y}.png').addTo(map);
        markerLayer = L.layerGroup().addTo(map);

        // မြေပုံပေါ်ကလစ်နှိပ်လျှင် ဆိုင်အသစ်အတွက် နေရာမှတ်ခြင်း
        map.on('click', (e) => {
            const { lat, lng } = e.latlng;
            const latField = document.getElementById('form_lat');
            const lngField = document.getElementById('form_lng');

            if (latField) latField.value = lat.toFixed(6);
            if (lngField) lngField.value = lng.toFixed(6);

            if (tempMarker) map.removeLayer(tempMarker);
            tempMarker = L.marker([lat, lng]).addTo(map).bindPopup("Selected Location").openPopup();
        });

        setupFilterListeners();
        fetchData();
        
        // Map အရွယ်အစား ပြန်ညှိခြင်း
        setTimeout(() => { map.invalidateSize(); }, 500);
    }
});

function setupFilterListeners() {
    const searchBtn = document.getElementById('searchBtn');
    const filterForm = document.getElementById('filterForm');
    const periodRads = document.querySelectorAll('input[name="period"]');

    periodRads.forEach(rad => {
        rad.addEventListener('change', function () {
            periodRads.forEach(r => r.parentElement.classList.remove('active'));
            if (this.checked) this.parentElement.classList.add('active');
        });
    });

    searchBtn?.addEventListener('click', () => fetchData(1));

    filterForm?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchData(1);
        }
    });

    document.addEventListener('click', (e) => {
        const link = e.target.closest('#paginationContainer a');
        if (link) {
            e.preventDefault();
            const page = new URL(link.href).searchParams.get('page');
            fetchData(page);
        }
    });
}

async function fetchData(page = 1) {
    const searchVal = document.getElementById('mapSearch')?.value || '';
    const regionVal = document.getElementById('regionFilter')?.value || '';
    const fromDate = document.getElementById('from_date')?.value || '';
    const toDate = document.getElementById('to_date')?.value || '';
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

    // Export Link ကို လက်ရှိ filter အတိုင်း update လုပ်ခြင်း (4af82d4 Fix)
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.href = `${window.appConfig.exportUrl}?${params.toString()}`;
    }

    try {
        const response = await fetch(`${window.appConfig.apiUrl}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();
        const total = result.total || 0;

        // Count Badge Update
        const countBadge = document.getElementById('filterCountBadge');
        if (countBadge) {
            countBadge.innerText = `${total} Shops`;
            countBadge.className = `badge shadow-sm ${total === 0 ? 'badge-danger' : 'badge-primary'}`;
        }

        const tbody = document.getElementById('shopTableBody');
        if (!tbody) return;

        if (total === 0) {
            const colCount = document.querySelectorAll('thead tr th').length || 6;
            tbody.innerHTML = `
                <tr>
                    <td colspan="${colCount}" class="text-center py-5 text-danger font-weight-bold">
                        <i class="fas fa-exclamation-circle mr-2"></i>ကိုက်ညီသော ဒေတာ မရှိပါ။
                    </td>
                </tr>`;
        } else {
            renderTable(result.data || []);
        }

        renderMarkers(result.all_filtered || result.data || []);
        renderPagination(result);

    } catch (e) {
        console.error('Fetch Error:', e);
        const tbody = document.getElementById('shopTableBody');
        if (tbody) tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-danger">စနစ်အတွင်း ချို့ယွင်းချက် ရှိနေပါသည်။</td></tr>`;
    }
}

function renderTable(shops) {
    const tbody = document.getElementById('shopTableBody');
    if (!tbody) return;

    // Permissions (Single Source of Truth from appConfig)
    const perms = window.appConfig.permissions || [];
    const canViewLogs = perms.includes('view-logs');
    const canEdit = perms.includes('shop-edit');
    const showActionCol = canEdit || canViewLogs;

    tbody.innerHTML = shops.map(shop => {
        const adminName = shop.admin ? shop.admin.name : 'System';
        const shopString = JSON.stringify(shop).replace(/"/g, '&quot;').replace(/'/g, "\\'");
        const createdDate = new Date(shop.created_at).toLocaleDateString('en-GB');

        let rowHtml = `<tr>`;

        // Col 1: Name (Clickable if canEdit)
        rowHtml += `
            <td class="pl-4">
                <a href="javascript:void(0)" class="text-dark font-weight-bold text-decoration-none" 
                   ${canEdit ? `onclick="openEditModal(${shopString})"` : ''}>
                    ${shop.name}
                </a>
            </td>`;

        // Col 2: Coordinates
        rowHtml += `
            <td>
                <span class="text-monospace small bg-light px-2 py-1 rounded border">
                    ${parseFloat(shop.lat).toFixed(5)}, ${parseFloat(shop.lng).toFixed(5)}
                </span>
            </td>`;

        // Col 3: Region
        rowHtml += `<td class="text-center"><span class="badge badge-light border">${shop.region || '-'}</span></td>`;

        // Col 4: Added By (Visible if canViewLogs)
        if (canViewLogs) {
            rowHtml += `
                <td class="text-center">
                    <span class="badge badge-info-soft text-info px-2 py-1" style="background-color: #e0f2ff;">
                        <i class="fas fa-user-check mr-1 small"></i>${adminName}
                    </span>
                </td>`;
        }

        // Col 5: Registered At
        rowHtml += `<td class="text-right small text-muted">${createdDate}</td>`;

        // Col 6: Actions
        if (showActionCol) {
            rowHtml += `<td class="text-right pr-4">`;
            if (canEdit) {
                rowHtml += `
                    <button class="btn btn-sm btn-light shadow-sm border mr-1" onclick="openEditModal(${shopString})">
                        <i class="fas fa-edit text-warning"></i>
                    </button>`;
            }
            if (canViewLogs) {
                rowHtml += `
                    <button class="btn btn-sm btn-light shadow-sm border" onclick="showShopLogs(${shop.id}, '${shop.name.replace(/'/g, "\\'")}')">
                        <i class="fas fa-history text-info"></i>
                    </button>`;
            }
            rowHtml += `</td>`;
        }

        rowHtml += `</tr>`;
        return rowHtml;
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
        if (container) container.innerHTML = '';
        return;
    }
    let html = '<ul class="pagination mb-0">';
    data.links.forEach(link => {
        html += `<li class="page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}">
                    <a class="page-link" href="${link.url || '#'}">${link.label.replace('&laquo;', '«').replace('&raquo;', '»')}</a>
                 </li>`;
    });
    if (container) container.innerHTML = html + '</ul>';
}

function openEditModal(shop) {
    document.getElementById('edit_shop_id').value = shop.id;
    document.getElementById('edit_name').value = shop.name;
    document.getElementById('edit_lat').value = shop.lat;
    document.getElementById('edit_lng').value = shop.lng;
    document.getElementById('edit_region').value = shop.region || '';
    $('#singleShopModal').modal('show');
}

async function updateShop() {
    const payload = {
        id: document.getElementById('edit_shop_id').value,
        name: document.getElementById('edit_name').value,
        region: document.getElementById('edit_region').value,
        lat: document.getElementById('edit_lat').value,
        lng: document.getElementById('edit_lng').value
    };
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        const response = await fetch(`/admin/shops/${payload.id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (response.ok) {
            $('#singleShopModal').modal('hide');
            if (tempMarker) { map.removeLayer(tempMarker); tempMarker = null; }
            fetchData();
            showToast('ဆိုင်အချက်အလက် ပြင်ဆင်မှု အောင်မြင်ပါသည်', 'success');
        } else {
            showToast('ပြင်ဆင်မှု မအောင်မြင်ပါ။ ပြန်လည်ကြိုးစားပါ', 'danger');
        }
    } catch (e) { showToast('System Error ဖြစ်ပွားနေပါသည်', 'danger'); }
}

async function deleteShop() {
    if (!confirm('Are you sure you want to delete this shop?')) return;
    const id = document.getElementById('edit_shop_id').value;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        const response = await fetch(`/admin/shops/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
        });

        if (response.ok) {
            $('#singleShopModal').modal('hide');
            fetchData();
            showToast('ဆိုင်ကို အောင်မြင်စွာ ဖျက်သိမ်းပြီးပါပြီ', 'danger');
        } else {
            showToast('ဖျက်၍မရပါ', 'danger');
        }
    } catch (e) { console.error(e); }
}

function showToast(message, type = 'success') {
    const toastElement = $('#toast-message');
    $('#toast-header').removeClass('bg-success bg-danger').addClass(type === 'success' ? 'bg-success' : 'bg-danger');
    document.getElementById('toast-body').innerText = message;
    toastElement.toast({ delay: 1500 }).toast('show');
}

async function showShopLogs(shopId, shopName) {
    const tbody = document.getElementById('shopLogTableBody');
    document.getElementById('logModalTitle').innerText = `${shopName} - Logs`;
    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-5"><div class="spinner-border spinner-border-sm text-primary"></div> ခဏစောင့်ပါ...</td></tr>';
    $('#shopLogModal').modal('show');

    try {
        const response = await fetch(`/admin/shops/${shopId}/logs`);
        const logs = await response.json();
        if (logs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center py-5">မှတ်တမ်းမရှိသေးပါ။</td></tr>';
            return;
        }
        tbody.innerHTML = logs.map(log => {
            const badgeClass = { 'ADD': 'bg-success', 'UPDATE': 'bg-warning', 'DELETE': 'bg-danger', 'IMPORT': 'bg-info' }[log.action] || 'bg-light';
            const adminInitial = log.user ? log.user.name.charAt(0) : 'S';

            return `
                <tr>
                    <td class="pl-4">
                        <div class="small text-dark">${new Date(log.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</div>
                        <div class="small text-muted">${new Date(log.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm mr-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:25px; height:25px; font-size:10px;">
                                ${adminInitial}
                            </div>
                            <span class="small font-weight-bold">${log.user ? log.user.name : 'System'}</span>
                        </div>
                    </td>
                    <td><span class="badge ${badgeClass} border text-white" style="font-size: 0.7rem;">${log.action}</span></td>
                    <td class="small text-wrap" style="max-width: 250px;">${log.description}</td>
                </tr>`;
        }).join('');
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-5 text-danger">Error fetching logs.</td></tr>';
    }
}