@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05) !important;
    }

    .badge-custom {
        display: inline-block;
        padding: 0.5em 0.8em;
        font-size: 85%;
        font-weight: 600;
        border-radius: 4px;
        width: 100%;
        text-align: center;
    }

    .badge-dist {
        background-color: #17a2b8 !important;
        color: white !important;
    }

    .badge-shops {
        background-color: #6c757d !important;
        color: white !important;
    }

    .table td {
        vertical-align: middle !important;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 text-primary font-weight-bold">
            <i class="fas fa-map-marked-alt mr-2"></i>Saved Routes
        </h4>
        <x-app-button href="{{ route('admin.maps.index') }}" color="primary" icon="fas fa-plus">
            Create New Route
        </x-app-button>
    </div>

    @php
    $tableHeaders = [
    'Route Name' => '35%',
    'Distance' => '15%',
    'Stops' => '15%',
    'Date Created' => '20%',
    'Action' => ['width' => '15%', 'align' => 'text-center']
    ];
    @endphp

    <x-app-table :headers="$tableHeaders" :items="$routes">
        @forelse($routes as $route)
        <tr class="align-middle">
            {{-- Route Name --}}
            <td class="py-3 px-4">
                <strong class="text-dark d-block">{{ $route->route_name }}</strong>
            </td>

            {{-- Distance --}}
            <td>
                <span class="badge rounded-pill bg-light text-primary border px-3 py-2 small">
                    <i class="fas fa-road me-1"></i> {{ $route->distance ?? '0 km' }}
                </span>
            </td>

            {{-- Stops (Waypoints) --}}
            <td>
                @php
                $waypoints = is_array($route->waypoints) ? $route->waypoints : json_decode($route->waypoints, true);
                $stopCount = is_array($waypoints) ? count($waypoints) : 0;
                @endphp
                <span class="badge rounded-pill bg-light text-success border px-3 py-2 small">
                    <i class="fas fa-store me-1"></i> {{ $stopCount }} {{ Str::plural('Shop', $stopCount) }}
                </span>
            </td>

            {{-- Date Created --}}
            <td>
                <div class="text-muted small">
                    <i class="far fa-calendar-alt me-1"></i> {{ $route->created_at->format('d M Y') }}
                </div>
            </td>

            {{-- Action Buttons --}}
            <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                    {{-- View Button --}}
                    <x-app-button
                        href="{{ route('admin.maps.show', $route->id) }}"
                        color="info"
                        size="sm"
                        class="text-white"
                        icon="fas fa-eye">
                        View
                    </x-app-button>

                    {{-- Delete Button --}}
                    @can('manage-routes')
                    <form action="{{ route('admin.maps.destroy', $route->id) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this route?')">
                        @csrf
                        @method('DELETE')
                        <x-app-button type="submit" color="danger" size="sm" icon="fas fa-trash">
                            Delete
                        </x-app-button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-5">
                <div class="text-muted">
                    <i class="fas fa-route fa-3x mb-3 opacity-25"></i>
                    <p class="mb-0">No saved routes found.</p>
                    <a href="{{ route('admin.maps.index') }}" class="small text-primary fw-bold">Create one now.</a>
                </div>
            </td>
        </tr>
        @endforelse
    </x-app-table>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(routeId) {
        if (confirm('Are you sure you want to delete this route?')) {
            document.getElementById('delete-form-' + routeId).submit();
        }
    }
</script>
@endpush