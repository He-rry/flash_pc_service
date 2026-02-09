@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
<style>
    /* Table Row Hover effect ကို data မြင်သာအောင် လျှော့ချထားပါတယ် */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05) !important;
    }

    /* Badge များကို အမြဲတမ်း မြင်နေရအောင် background color သတ်မှတ်ချက်ကို ခိုင်မာအောင် လုပ်ထားပါတယ် */
    .badge-custom {
        display: inline-block;
        padding: 0.5em 0.8em;
        font-size: 85%;
        font-weight: 600;
        border-radius: 4px;
        width: 100%;
        /* Full width လိုချင်ရင် ထားပါ */
        text-align: center;
    }

    /* Info Badge (Distance အတွက်) */
    .badge-dist {
        background-color: #17a2b8 !important;
        color: white !important;
    }

    /* Secondary Badge (Shops အတွက်) */
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
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list mr-2"></i> Saved Routes</h5>
            <a href="{{ route('admin.maps.index') }}" class="btn btn-sm btn-light">
                <i class="fas fa-arrow-left mr-1"></i> Back to Planner
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover border">
                    <thead class="thead-light">
                        <tr>
                            <th>Route Name</th>
                            <th style="width: 150px;">Distance</th>
                            <th style="width: 150px;">Stops</th>
                            <th>Date Created</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($routes as $route)
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $route->route_name }}</strong>
                            </td>
                            <td>
                                <span class="badge-custom badge-dist">
                                    <i class="fas fa-road mr-1"></i> {{ $route->distance ?? '0 km' }}
                                </span>
                            </td>
                            <td>
                                @php
                                $waypoints = is_array($route->waypoints) ? $route->waypoints : json_decode($route->waypoints, true);
                                $stopCount = is_array($waypoints) ? count($waypoints) : 0;
                                @endphp
                                <span class="badge-custom badge-shops">
                                    <i class="fas fa-store mr-1"></i> {{ $stopCount }} {{ $stopCount <= 1 ? 'Shop' : 'Shops' }}
                                </span>
                            </td>
                            <td>
                                <div class="text-muted small">
                                    <i class="far fa-calendar-alt mr-1"></i> {{ $route->created_at->format('d M Y') }}
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.maps.show', $route->id) }}" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @can('manage-routes')
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $route->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $route->id }}" action="{{ route('admin.maps.destroy', $route->id) }}" method="POST" style="display: none;">
                                        @csrf @method('DELETE')
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                No saved routes found. <a href="{{ route('admin.maps.index') }}">Create one now.</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($routes, 'hasPages') && $routes->hasPages())
            <div class="mt-3">{{ $routes->links() }}</div>
            @endif
        </div>
    </div>
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