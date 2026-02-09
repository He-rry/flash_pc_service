@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="mb-0 text-primary"><i class="fas fa-history me-2"></i>System Activity Logs</h5>
            <div class="d-flex align-items-center">
                @if(request('shop_id'))
                <a href="{{ route('admin.logs.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                    <i class="fas fa-times me-1"></i> Clear Filter
                </a>
                @endif
                <span class="badge rounded-pill bg-light border text-muted px-3 py-2">Total: {{ $logs->total() }} logs</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                        <tr>
                            <th class="ps-4 py-3">Time</th>
                            <th>Admin</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th class="text-center">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4">
                                <span class="small text-dark d-block fw-bold">{{ $log->created_at->format('d M Y') }}</span>
                                <span class="small text-muted">{{ $log->created_at->format('h:i A') }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px; height:32px; font-size:12px;">
                                        {{ substr($log->user->name ?? 'S', 0, 1) }}
                                    </div>

                                    <div class="d-flex flex-column">
                                        <span class="fw-bold small">
                                            @if($log->user)
                                            {{ $log->user->name }}
                                            @if($log->user->trashed())
                                            <span class="text-danger" style="font-size: 10px;">(deleted)</span>
                                            @endif
                                            @else
                                            System
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                $badgeClass = match($log->action) {
                                'ADD', 'CREATE_USER' => 'bg-success',
                                'UPDATE' => 'bg-warning text-dark',
                                'DELETE' => 'bg-danger',
                                'IMPORT' => 'bg-info text-dark',
                                'RESTORE_USER' => 'bg-success',
                                'FORCE_DELETE_USER' => 'bg-danger',
                                default => 'bg-secondary'
                                };
                                @endphp
                                <span class="badge {{ $badgeClass }} small border-0 px-2 py-1">{{ $log->action }}</span>
                            </td>
                            <td class="small">
                                @if($log->shop_id)
                                <a href="{{ route('admin.logs.index', ['shop_id' => $log->shop_id]) }}" class="text-primary text-decoration-none fw-bold">
                                    <i class="fas fa-filter me-1 small"></i>{{ $log->description }}
                                </a>
                                @else
                                {{ $log->description }}
                                @endif
                            </td>
                            <td class="text-center">
                                @if($log->changes)
                                <button type="button" class="btn btn-sm btn-outline-primary py-1" data-bs-toggle="modal" data-bs-target="#modal{{ $log->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <div class="modal fade" id="modal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content shadow">
                                            <div class="modal-header border-0 bg-light">
                                                <h6 class="modal-title fw-bold">Activity Changes Details</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <p class="small text-muted mb-2 font-monospace text-uppercase">Raw Data (JSON):</p>
                                                <pre class="bg-dark text-success p-3 rounded small mb-0" style="max-height: 400px; overflow-y: auto; font-family: 'Courier New', Courier, monospace;">{{ json_encode($log->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
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
                                <div class="text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 d-block"></i>
                                    မှတ်တမ်းမရှိသေးပါ။
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3 border-top">
            <div class="d-flex justify-content-center">
                {{ $logs->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection