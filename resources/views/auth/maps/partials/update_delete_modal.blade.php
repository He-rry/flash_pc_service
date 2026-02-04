<div class="modal fade" id="singleShopModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title" id="modalTitle">Edit Shop</h5>
                <button type="button" class="close text-danger" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editShopForm">
                    <input type="hidden" id="edit_shop_id">
                    <div class="form-group">
                        <label class="small font-weight-bold text-muted">Shop Name</label>
                        <input type="text" id="edit_name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="small font-weight-bold text-muted">Lat</label>
                            <input type="text" id="edit_lat" class="form-control bg-light">
                        </div>
                        <div class="col-6">
                            <label class="small font-weight-bold text-muted">Lng</label>
                            <input type="text" id="edit_lng" class="form-control bg-light">
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-info btn-sm btn-block font-weight-bold" onclick="enableMapPicker()">
                                <i class="fas fa-map-marker-alt mr-1"></i> PICK NEW POSITION FROM MAP
                            </button>
                            <div id="picker-hint" class="alert alert-info py-1 px-2 mt-2 small d-none">
                                <i class="fas fa-info-circle mr-1"></i> မြေပုံပေါ်တွင် စိတ်ကြိုက်နေရာသို့ Click ထောက်၍ ရွေးချယ်ပါ။
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label class="small font-weight-bold text-muted">Region</label>
                        <input type="text" id="edit_region" class="form-control">
                    </div>
                </form>

                <hr>
                @can('manage-shops')
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger" onclick="deleteShop()">
                        <i class="fas fa-trash-alt mr-1"></i> Delete
                    </button>
                    <button type="button" class="btn btn-primary" onclick="updateShop()">
                        <i class="fas fa-save mr-1"></i> Save Changes
                    </button>
                </div>
                @endcan
            </div>
        </div>
    </div>
</div>