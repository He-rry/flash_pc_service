<div class="modal fade" id="shopLogModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg shadow-lg" role="document">
        <div class="modal-content border-0">
            <div class="modal-header bg-white border-bottom py-3">
                <h5 class="modal-title text-primary font-weight-bold">
                    <i class="fas fa-history mr-2"></i><span id="logModalTitle">Shop Activity Logs</span>
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="max-height: 450px; overflow-y: auto;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small font-weight-bold">
                            <tr>
                                <th class="pl-4">Time</th>
                                <th>Admin</th>
                                <th>Action</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody id="shopLogTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>