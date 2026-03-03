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
        @can('view-shop-management')
        <div class="col-lg-8">
            <div class="custom-card p-4">
                @can ('shop-create')
                <h6 class="section-title mb-3"><i class="fas fa-plus-circle mr-2"></i>Register & Import Shops</h6>
                <form action="{{ route('admin.shops.store') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row align-items-start">
                        <div class="col-md-4 px-1">
                            <x-form-input name="name" label="Shop Name" placeholder="Enter name" required />
                        </div>
                        <div class="col-md-2 px-1">
                            <x-form-input
                                name="lat"
                                id="form_lat"
                                label="Latitude"
                                type="number"
                                step="any"
                                placeholder="16.8..." />
                        </div>
                        <div class="col-md-2 px-1">
                            <x-form-input
                                name="lng"
                                id="form_lng"
                                label="Longitude"
                                type="number"
                                step="any"
                                placeholder="96.1..." />
                        </div>
                        <div class="col-md-2 px-1">
                            <x-form-input name="region" label="Region" placeholder="Region" />
                        </div>
                        <div class="col-md-2 px-1 mt-4">
                            <x-app-button type="submit" color="primary" size="sm" icon="fas fa-plus" class="btn-block font-weight-bold py-2">
                                ADD
                            </x-app-button>
                        </div>
                        <div class="col-md-2 px-1">
                            <x-form-input name="created_at" label="Reg Date" type="date" :value="date('Y-m-d')" />
                        </div>
                    </div>
                </form>
                @endcan
                <hr class="my-4">
                <div class="row align-items-center">
                    <div class="col-md-7 px-1">
                        @can ('shop-import')
                        <form action="{{ route('admin.shops.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                            @csrf
                            <div class="custom-file">
                                <input type="file" name="file" class="custom-file-input" id="importFile" required>
                                <label class="custom-file-label custom-file-label-sm small" for="importFile">Choose Excel/CSV</label>
                            </div>
                            @endcan
                    </div>
                    <div class="col-md-2 px-1">
                        @can ('shop-import')
                        <x-app-button type="submit" color="dark" size="sm" icon="fas fa-upload"
                            permission="shop-import"
                            class="btn-block font-weight-bold">
                            IMPORT
                        </x-app-button>
                        </form>
                        @endcan
                    </div>
                    <div class="col-md-3 px-1">
                        @can ('shop-export')
                        <x-app-button :href="route('admin.shops.export')" id="exportBtn" color="success" size="sm" icon="fas fa-file-excel" class="btn-block font-weight-bold">
                            EXPORT RESULT
                        </x-app-button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @endcan
        <div class="col-lg-4">
            @can('view-filters')
            <div class="custom-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="section-title mb-0"><i class="fas fa-filter mr-2"></i>Data Filters</h6>
                    <span id="filterCountBadge" class="badge badge-dark shadow-sm" style="font-size: 11px; padding: 5px 10px; background-color: yellow !important; color:black !important;">
                        {{ $shops->total() }} Shops
                    </span>
                </div>
                <form id="filterForm" onsubmit="return false;">
                    <div class="mb-3">
                        {{-- Search Input --}}
                        <x-form-input
                            name="search"
                            id="mapSearch"
                            placeholder="Search by shop name..."
                            class="mb-2" />

                        {{-- Region Select --}}
                        <x-form-select name="region" id="regionFilter">
                            <option value="">All Regions</option>
                            @foreach($regions as $reg)
                            <option value="{{ $reg }}">{{ $reg }}</option>
                            @endforeach
                        </x-form-select>
                        <x-form-radio-group
                            name="period"
                            label="Time Period"
                            :selected="'all'"
                            :options="[
                                 'all' => 'All',
                                  '3'   => '3M',
                                  '6'   => '6M',
                                 '12'  => '1Y',
                         ]" />
                        <div class="row">
                            <div class="col-md-6 px-1">
                                <x-form-input
                                    name="from_date"
                                    id="from_date"
                                    label="From Date"
                                    type="date" />
                            </div>
                            <div class="col-md-6 px-1">
                                <x-form-input
                                    name="to_date"
                                    id="to_date"
                                    label="To Date"
                                    type="date" />
                            </div>
                        </div>
                    </div>
                </form>
                <div class="mt-3">
                    <x-app-button id="searchBtn" color="primary" size="sm" icon="fas fa-search" class="btn-block font-weight-bold">
                        APPLY & SEARCH
                    </x-app-button>
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>

<div class="custom-card p-3 shadow-sm mt-3">
    <div id="map" style="height: 400px; border-radius: 8px;"></div>
</div>

<div class="custom-card overflow-hidden shadow-sm mt-3">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="section-title mb-0"><i class="fas fa-store mr-2 text-primary"></i>Shop Directory</h6>

        {{-- ၅။ Activity Logs Button (Link with Permission) --}}
        <x-app-button :href="route('admin.logs.index')" permission="view-logs" color="primary" :outline="true" size="sm" icon="fas fa-history" class="rounded-pill px-3">
            View Activity Logs
        </x-app-button>
    </div>

    @php
    //Header
    $rawHeaders = [
    'Shop Name' => '25%',
    'Coordinates' => '20%',
    'Region' => ['width' => '15%', 'align' => 'text-center'],
    'Added By' => ['width' => '15%', 'align' => 'text-center', 'permission' => 'view-logs'],
    'Registered At' => ['width' => '15%', 'align' => 'text-right'],
    'Actions' => ['width' => '15%', 'align' => 'text-right', 'canany' => ['shop-edit', 'view-logs']]
    ];
    @endphp
    <div class="dashboard-container">
        <x-app-table id="shopTable" :items="$shops" :headers="$rawHeaders">
            @slot('default')
            @foreach($shops as $shop)
            <tr class="align-middle">
                {{-- Shop Name Column --}}
                <td class="py-3 px-4 font-weight-bold">
                    @can('shop-edit')
                    <x-app-button color="link" class="p-0 text-dark text-decoration-none" :onclick="'openEditModal(' . json_encode($shop) . ')'">
                        {{ $shop->name }}
                    </x-app-button>
                    @else
                    {{ $shop->name }}
                    @endcan
                </td>
                {{-- Coordinates Column --}}
                <td class="py-3 px-4 small text-monospace text-muted">
                    <i class="fas fa-map-marker-alt mr-1 text-danger opacity-75"></i>
                    {{ number_format($shop->lat, 4) }}, {{ number_format($shop->lng, 4) }}
                </td>
                {{-- Region Column --}}
                <td class="py-3 px-4 text-center">
                    <span class="badge badge-light border text-dark font-weight-normal px-2 py-1">
                        {{ $shop->region }}
                    </span>
                </td>
                {{-- Added By Column (Permission Check) --}}
                @if(isset($activeHeaders['Added By']))
                <td class="py-3 px-4 text-center text-muted small">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-circle mr-1 opacity-50"></i>
                        {{ $shop->admin->name ?? 'System' }}
                    </div>
                </td>
                @endif
                {{-- Registered At Column --}}
                <td class="py-3 px-4 text-right text-muted small">
                    {{ $shop->created_at->format('d/m/Y') }}
                </td>
                {{-- Actions Column (Permission Check) --}}
                @if(isset($activeHeaders['Actions']))
                <td>
                    <div class="btn-action-group">
                        <x-app-button
                            color="light" size="sm"
                            icon="fas fa-edit text-warning"
                            permission="shop-edit"
                            title="Edit Shop"
                            :onclick="'openEditModal(' . json_encode($shop) . ')'" />
                        <x-app-button
                            color="light" size="sm"
                            icon="fas fa-history text-info"
                            permission="view-logs"
                            title="View Logs"
                            :onclick="'viewLog(' . $shop->id . ')'" />
                    </div>
                </td>
                @endif
            </tr>
            @endforeach
            @endslot
        </x-app-table>
    </div>
    @include('auth.maps.partials.update_delete_modal')
    @include('auth.maps.partials.import_modal')
    @include('auth.logs.shopslog')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        window.appConfig = {
            apiUrl: "{{ url('/api/v1/shops') }}",
            exportUrl: "{{ route('admin.shops.export') }}",
            permissions: @json(auth()->user()->getAllPermissions()->pluck('name')),
        };
    </script>
    @if(session('warning'))
    <script>
        $(document).ready(function() {
            $('#reportModal').modal('show');
        });
    </script>
    @endif
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/shop-map.js') }}"></script>
    @endsection