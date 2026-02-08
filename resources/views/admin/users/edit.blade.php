@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary"><i class="fas fa-user-edit me-2"></i>Edit User: {{ $user->name }}</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" id="userEditForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold small text-muted">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold small text-muted">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold small text-muted">Password (ချန်ထားခဲ့ပါက အဟောင်းအတိုင်း ဖြစ်ပါမည်)</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold small text-muted">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-weight-bold small text-muted">Role</label>
                            <select name="role" id="roleSelect" class="form-select form-control" required>
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ (old('role') ?? ($user->roles->first()->name ?? '')) == $role->name ? 'selected' : '' }}>
                                    {{ strtoupper($role->name) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 font-weight-bold text-dark">Permission Matrix</h6>
                            <div class="form-check m-0">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label small font-weight-bold text-primary" for="selectAll" style="cursor: pointer;">Select All</label>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Role မှ ဆင်းသက်လာသော Permission များကို <span class="badge bg-secondary text-white">inherited</span> အဖြစ် တွေ့မြင်နိုင်ပါသည်။</p>

                        <div class="card p-3 mb-3 border-light shadow-sm bg-light">
                            @foreach($matrix as $moduleKey => $perms)
                            <div class="mb-3">
                                <strong class="mb-2 d-block text-dark border-bottom pb-1" style="font-size: 0.9rem;">{{ ucwords($moduleKey) }}</strong>
                                <div class="row">
                                    @foreach($perms as $perm)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm['name'] }}" 
                                                class="form-check-input perm-checkbox" 
                                                id="perm-{{ str_replace(' ', '-', $perm['name']) }}"
                                                {{ $user->hasPermissionTo($perm['name']) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="perm-{{ str_replace(' ', '-', $perm['name']) }}" style="cursor: pointer;">
                                                {{ $perm['label'] }}
                                            </label>
                                            <span class="perm-badge ms-1"></span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            @can('manage-users')
                            <button type="submit" class="btn btn-primary py-2 font-weight-bold shadow-sm">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rolePerms = @json($rolePerms ?? []);
        const roleSelect = document.getElementById('roleSelect');
        const selectAllCb = document.getElementById('selectAll');
        const permissionCbs = document.querySelectorAll('.perm-checkbox');
        const form = document.getElementById('userEditForm');

        function applyRoleInheritance() {
            const selected = roleSelect.value;
            const inherited = rolePerms[selected] || [];
            
            permissionCbs.forEach(cb => {
                const val = cb.value;
                const badge = cb.parentElement.querySelector('.perm-badge');
                
                if (selected === 'super-admin') {
                    cb.checked = true;
                    cb.disabled = true;
                    if (badge) badge.innerHTML = '<span class="badge bg-secondary text-white">inherited</span>';
                } 
                else if (inherited.includes(val)) {
                    cb.checked = true;
                    cb.disabled = true;
                    if (badge) badge.innerHTML = '<span class="badge bg-secondary text-white">inherited</span>';
                } 
                else {
                    // If it was checked but not from the role, it's a direct permission (Keep it checked)
                    cb.disabled = false;
                    if (badge) badge.innerHTML = '';
                }
            });
            updateSelectAllState();
        }

        if (selectAllCb) {
            selectAllCb.addEventListener('change', function() {
                permissionCbs.forEach(cb => {
                    if (!cb.disabled) cb.checked = this.checked;
                });
            });
        }

        function updateSelectAllState() {
            if (!selectAllCb) return;
            const checkedCount = Array.from(permissionCbs).filter(c => c.checked).length;
            selectAllCb.checked = (permissionCbs.length > 0 && checkedCount === permissionCbs.length);
            selectAllCb.indeterminate = (checkedCount > 0 && checkedCount < permissionCbs.length);
        }

        roleSelect?.addEventListener('change', applyRoleInheritance);
        permissionCbs.forEach(cb => cb.addEventListener('change', updateSelectAllState));

        form?.addEventListener('submit', function() {
            permissionCbs.forEach(cb => cb.disabled = false);
        });

        // Initialize
        applyRoleInheritance();
    });
</script>
@endpush
@endsection