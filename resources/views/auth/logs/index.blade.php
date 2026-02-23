@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        {{-- Card Header with Filter & Stats --}}
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="mb-0 text-primary fw-bold">
                <i class="fas fa-history me-2"></i>System Activity Logs
            </h5>
            <div class="d-flex align-items-center gap-2">
                @if(request('shop_id'))
                <x-app-button href="{{ route('admin.logs.index') }}" color="outline-secondary" size="sm" icon="fas fa-times">
                    Clear Filter
                </x-app-button>
                @endif
                <span class="badge rounded-pill bg-light border text-muted px-3 py-2">
                    Total: {{ number_format($logs->total()) }} logs
                </span>
            </div>
        </div>
        @php
        $tableHeaders = [
        'Time' => '15%',
        'Admin' => '20%',
        'Action' => '15%',
        'Description' => '40%',
        'Details' => ['width' => '10%', 'align' => 'text-center']
        ];
        @endphp

        <x-app-table :headers="$tableHeaders" :items="$logs">
            @forelse($logs as $log)
            <tr class="align-middle">
                {{-- Time Column --}}
                <td class="ps-4">
                    <span class="small text-dark d-block fw-bold">{{ $log->created_at->format('d M Y') }}</span>
                    <span class="small text-muted">{{ $log->created_at->format('h:i A') }}</span>
                </td>

                {{-- Admin Column --}}
                <td>
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                            style="width:32px; height:32px; font-size:12px; flex-shrink: 0;">
                            {{ substr($log->user->name ?? 'S', 0, 1) }}
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fw-bold small text-dark">
                                @if($log->user)
                                {{ $log->user->name }}
                                @if($log->user->trashed())
                                <span class="text-danger" style="font-size: 10px;">(deleted)</span>
                                @endif
                                @else
                                <span class="text-muted">System</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </td>

                {{-- Action Column --}}
                <td>
                    @php
                    $badgeClass = match($log->action) {
                    'ADD', 'CREATE_USER', 'RESTORE_USER' => 'bg-success',
                    'UPDATE' => 'bg-warning text-dark',
                    'DELETE', 'FORCE_DELETE_USER' => 'bg-danger',
                    'IMPORT' => 'bg-info text-dark',
                    default => 'bg-secondary'
                    };
                    @endphp
                    <span class="badge {{ $badgeClass }} small border-0 px-2 py-1" style="font-size: 10px;">
                        {{ $log->action }}
                    </span>
                </td>

                {{-- Description Column --}}
                <td class="small">
                    @if($log->shop_id)
                    <a href="{{ route('admin.logs.index', ['shop_id' => $log->shop_id]) }}"
                        class="text-primary text-decoration-none fw-bold hover-link">
                        <i class="fas fa-filter me-1 small"></i>{{ $log->description }}
                    </a>
                    @else
                    <span class="text-dark">{{ $log->description }}</span>
                    @endif
                </td>

                {{-- Details Column --}}
                <td class="text-center">
                    @if($log->changes)
                    <x-app-button
                        type="button"
                        color="outline-primary"
                        size="sm"
                        icon="fas fa-eye"
                        data-bs-toggle="modal"
                        data-bs-target="#modal{{ $log->id }}" />

                    {{-- Details Modal --}}
                    <div class="modal fade" id="modal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content shadow-lg border-0">
                                <div class="modal-header border-0 bg-light py-3">
                                    <h6 class="modal-title fw-bold text-dark">
                                        <i class="fas fa-terminal me-2 text-primary"></i>Activity Changes Details
                                    </h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-start p-4">
                                    <p class="small text-muted mb-2 text-uppercase fw-bold" style="letter-spacing: 1px;">Raw Data (JSON):</p>
                                    <pre class="bg-dark text-success p-3 rounded small mb-0 shadow-inner"
                                        style="max-height: 450px; overflow-y: auto; font-family: 'Fira Code', 'Courier New', monospace; line-height: 1.6;">{{ json_encode($log->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <span class="text-muted small">-</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-5">
                    <div class="text-muted opacity-50">
                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                        <span class="fw-bold">မှတ်တမ်းမရှိသေးပါ။</span>
                    </div>
                </td>
            </tr>
            @endforelse
        </x-app-table>
        {{-- Pagination Footer --}}
        <div class="card-footer bg-white py-3 border-top">
            <div class="d-flex justify-content-center">
                {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<style>
    .hover-link:hover {
        text-decoration: underline !important;
        opacity: 0.8;
    }

    .shadow-inner {
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
    }
</style>
@endsection