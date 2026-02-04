@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
<link rel="stylesheet" href="{{ asset('css/shop-map.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- noti msg -->
<div aria-live="polite" aria-atomic="true" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
    <div id="toast-message" class="toast hide shadow-lg border-0" role="alert" aria-live="assertive" aria-atomic="true" data-delay="1500">
        <div class="toast-header text-white" id="toast-header">
            <strong class="mr-auto">System Notification</strong>
            <button type="button" class=" ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body text-dark font-weight-bold" id="toast-body"></div>
    </div>
</div>

<div class="dashboard-container">
    <div class="row">
        <div class="col-lg-8">
            <div class="custom-card p-4">
                @can('manage-shops')
                <h6 class="section-title mb-3"><i class="fas fa-plus-circle mr-2"></i>Register & Import Shops</h6>
                <form action="{{ route('admin.shops.store') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row align-items-start">
                        <div class="col-md-4 px-1 mb-2">
                            <label class="small font-weight-bold text-muted ml-1 uppercase">Shop Name</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control form-control-sm @error('name') is-invalid @enderror"
                                placeholder="Enter name">
                            @error('name')
                            <div class="invalid-feedback font-weight-bold" style="font-size: 11px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2 px-1 mb-2">
                            <label class="small font-weight-bold text-muted ml-1 uppercase">Latitude</label>
                            <input type="number" step="any" name="lat" id="form_lat" value="{{ old('lat') }}"
                                class="form-control form-control-sm @error('lat') is-invalid @enderror"
                                placeholder="16.8...">
                            @error('lat')
                            <div class="invalid-feedback font-weight-bold" style="font-size: 11px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2 px-1 mb-2">
                            <label class="small font-weight-bold text-muted ml-1 uppercase">Longitude</label>
                            <input type="number" step="any" name="lng" id="form_lng" value="{{ old('lng') }}"
                                class="form-control form-control-sm @error('lng') is-invalid @enderror"
                                placeholder="96.1...">
                            @error('lng')
                            <div class="invalid-feedback font-weight-bold" style="font-size: 11px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2 px-1 mb-2">
                            <label class="small font-weight-bold text-muted ml-1 uppercase">Region</label>
                            <input type="text" name="region" value="{{ old('region') }}"
                                class="form-control form-control-sm" placeholder="Region">
                        </div>
                        <div class="col-md-2 px-1 mt-4">
                            <button type="submit" class="btn btn-primary btn-sm btn-block font-weight-bold shadow-sm py-2">
                                <i class="fas fa-plus mr-1"></i> ADD
                            </button>
                        </div>
                        <div class="col-md-2 px-1 mb-2">
                            <label class="small font-weight-bold text-muted ml-1 uppercase">Reg Date</label>
                            <input type="date" name="created_at" value="{{ date('Y-m-d') }}"
                                class="form-control form-control-sm">
                        </div>
                    </div>
                </form>

                <hr class="my-4">

                <form action="{{ route('admin.shops.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-md-7 px-1">
                            <div class="custom-file">
                                <input type="file" name="file" class="custom-file-input" id="importFile" required>
                                <label class="custom-file-label custom-file-label-sm small" for="importFile">Choose Excel/CSV</label>
                            </div>
                        </div>
                        <div class="col-md-2 px-1">
                            <button type="submit" class="btn btn-dark btn-sm btn-block font-weight-bold shadow-sm">
                                <i class="fas fa-upload mr-1"></i> IMPORT
                            </button>
                        </div>
                        <div class="col-md-3 px-1">
                            <a href="{{ route('admin.shops.export') }}" id="exportBtn" class="btn btn-success btn-sm btn-block font-weight-bold shadow-sm">
                                <i class="fas fa-file-excel mr-1"></i> EXPORT RESULT
                            </a>
                        </div>
                    </div>
                </form>
                @else
                <h6 class="section-title mb-3"><i class="fas fa-store mr-2"></i>Shops (View Only)</h6>
                <p class="text-muted small mb-0">Use filters on the right to search. You do not have permission to add, edit, or import/export shops.</p>
                @endcan
            </div>
        </div>
        <div class="col-lg-4">
            <div class="custom-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="section-title mb-0"><i class="fas fa-filter mr-2"></i>Data Filters</h6>
                    <span id="filterCountBadge" class="badge badge-dark shadow-sm" style="font-size: 11px; padding: 5px 10px; background-color: yellow !important; color:black !important;">
                        {{ $shops->total() }} Shops
                    </span>
                </div>

                <form id="filterForm" onsubmit="return false;">
                    <div class="mb-3">
                        <input type="text" id="mapSearch" class="form-control form-control-sm mb-2" placeholder="Search by shop name...">
                        <select id="regionFilter" class="form-control form-control-sm">
                            <option value="">All Regions</option>
                            @foreach($regions as $reg)
                            <option value="{{ $reg }}">{{ $reg }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="small font-weight-bold text-muted mb-2 d-block">TIME PERIOD</label>
                    <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                        <label class="btn btn-outline-primary btn-sm active flex-fill">
                            <input type="radio" name="period" value="all" checked> All
                        </label>
                        <label class="btn btn-outline-primary btn-sm flex-fill">
                            <input type="radio" name="period" value="3"> 3M
                        </label>
                        <label class="btn btn-outline-primary btn-sm flex-fill">
                            <input type="radio" name="period" value="6"> 6M
                        </label>
                        <label class="btn btn-outline-primary btn-sm flex-fill">
                            <input type="radio" name="period" value="12"> 1Y
                        </label>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="small font-weight-bold text-muted">From Date</label>
                            <input type="date" name="from_date" id="from_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="small font-weight-bold text-muted">To Date</label>
                            <input type="date" name="to_date" id="to_date" class="form-control form-control-sm">
                        </div>
                    </div>
                </form>
                <div class="mt-3">
                    <button type="button" id="searchBtn" class="btn btn-primary btn-sm btn-block font-weight-bold shadow-sm">
                        <i class="fas fa-search mr-1"></i> APPLY & SEARCH
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

<div class="custom-card p-3 shadow-sm mt-3">
    <div id="map" style="height: 400px; border-radius: 8px;"></div>
</div>

<div class="custom-card overflow-hidden shadow-sm mt-3">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="section-title mb-0">
            <i class="fas fa-store mr-2 text-primary"></i>Shop Directory
        </h6>

        @can('view-logs')
        <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            <i class="fas fa-history mr-1"></i> View Activity Logs
        </a>
        @endcan
    </div>
    <div class="table-container">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="pl-4"><i class="fas fa-store mr-1 text-muted"></i> Shop Name</th>
                    <th><i class="fas fa-map-marker-alt mr-1 text-muted"></i> Coordinates</th>
                    <th class="text-center"><i class="fas fa-globe mr-1 text-muted"></i> Region</th>
                    <th class="text-center"><i class="fas fa-user-shield mr-1 text-muted"></i> Added By</th>
                    <th class="text-right pr-4"><i class="fas fa-calendar-alt mr-1 text-muted"></i> Registered At</th>
                </tr>
            </thead>
            <tbody id="shopTableBody">
                @foreach($shops as $shop)
                <tr>
                    <td class="pl-4"><strong>{{ $shop->name }}</strong></td>
                    <td>
                        <span class="text-monospace small bg-light px-2 py-1 rounded">
                            {{ number_format($shop->lat, 5) }}, {{ number_format($shop->lng, 5) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-light border font-weight-normal">{{ $shop->region }}</span>
                    </td>
                    <td class="text-center">
                        @if($shop->admin)
                        <span class="badge badge-info-soft text-info px-2 py-1" style="background-color: #e0f2ff;">
                            <i class="fas fa-user-check mr-1 small"></i>{{ $shop->admin->name }}
                        </span>
                        @else
                        <span class="text-muted small">System</span>
                        @endif
                    </td>
                    <td class="text-right pr-4 small text-muted">
                        {{ $shop->created_at->format('d/m/Y') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="paginationContainer" class="card-footer bg-light py-3 d-flex justify-content-center">
        {{ $shops->links('pagination::bootstrap-4') }}
    </div>
</div>
@include('auth.maps.partials.update_delete_modal')
@include('auth.maps.partials.import_modal')
@include('auth.logs.shopslog')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    window.appConfig = {
        apiUrl: "{{ url('/api/v1/shops') }}",
        exportUrl: "{{ route('admin.shops.export') }}",
        canManageShops: @json(auth()->user()->can('manage-shops')),
        canViewLogs: @json(auth()->user()->can('view-logs'))
    };
</script>
@if(session('warning'))
<script>
    $(document).ready(function() {
        $('#reportModal').modal('show');
    });
</script>
@endif
<script src="{{ asset('js/shop-map.js') }}"></script>
@endsection