<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-warning text-dark border-0">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-exclamation-circle mr-2">
                    </i> Import Report (Duplicates Found)</h6>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 mb-3 small shadow-sm">
                    <i class="fas fa-info-circle mr-2"></i> {{ session('warning_msg') }}
                </div>

                <div class="table-responsive border rounded bg-white" style="max-height: 300px;">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Shop Name</th>
                                <th>Reason</th>
                                <th>Lat, Lng</th>
                                <th>Region</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(session('duplicates'))
                            @foreach(session('duplicates') as $index => $row)
                            <tr>
                                <td class="text-center text-muted small">{{ $index + 1 }}</td>
                                <td class="font-weight-bold small">{{ $row['shop_name'] ?? '-' }}</td>
                                <td><span class="badge badge-danger-light text-danger">{{ $row['reason'] ?? 'Duplicate' }}</span></td>
                                <td class="small">{{ $row['latitude'] ?? '-' }}, {{ $row['longitude'] ?? '-' }}</td>
                                <td><span class="badge badge-light border">{{ $row['region'] ?? '-' }}</span></td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                @can('manage-shops')
                <a href="{{ route('admin.shops.download.duplicates') }}" class="btn btn-success btn-sm px-4 font-weight-bold">
                    <i class="fas fa-file-excel mr-1"></i> DOWNLOAD EXCEL
                </a>
                @endcan
                <button type="button" class="btn btn-secondary btn-sm px-3" id="btn-close" data-bs-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>