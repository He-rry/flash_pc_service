@extends('layouts.app')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary"><i class="fas fa-history mr-2"></i>System Activity Logs</h5>
        <div>
            @if(request('shop_id'))
            <a href="{{ route('admin.logs.index') }}" class="btn btn-sm btn-outline-secondary mr-2">
                <i class="fas fa-times mr-1"></i> Clear Filter
            </a>
            @endif
        </div>
        <span class="badge badge-pill badge-light border text-muted">Total: {{ $logs->total() }} logs</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small uppercase font-weight-bold">
                    <tr>
                        <th class="pl-4">Time</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="pl-4">
                            <span class="small text-dark d-block">{{ $log->created_at->format('d M Y') }}</span>
                            <span class="small text-muted">{{ $log->created_at->format('h:i A') }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm mr-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:30px; height:30px; font-size:12px;">
                                    {{ substr($log->user->name, 0, 1) }}
                                </div>
                                <span class="font-weight-bold">{{ $log->user->name }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                            $badgeClass = match($log->action) {
                            'ADD' => 'bg-success',
                            'UPDATE' => 'bg-warning',
                            'DELETE' => 'bg-danger',
                            'IMPORT' => 'bg-info',
                            'EXPORT' => 'bg-secondary',
                            default => 'bg-light'
                            };
                            @endphp
                            <span class="badge {{$badgeClass}} border">{{ $log->action }}</span>
                        </td>
                        <td class="small text-wrap" style="max-width: 300px;">
                            @if($log->shop_id)
                            <a href="{{ route('admin.logs.index', ['shop_id' => $log->shop_id]) }}" class="text-primary font-weight-bold">
                                <i class="fas fa-filter mr-1 small"></i>{{ $log->description }}
                            </a>
                            @else
                            {{ $log->description }}
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">မှတ်တမ်းမရှိသေးပါ။</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-3 d-flex justify-content-center">
        {{ $logs->links('pagination::bootstrap-4') }}
    </div>
</div>
</div>
@endsection