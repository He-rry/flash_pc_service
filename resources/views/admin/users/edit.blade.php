@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary"><i class="fas fa-user-edit me-2"></i>Edit Admin: {{ $user->name }}</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light border"><i class="fas fa-arrow-left me-1"></i> Back</a>
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

                        <div class="mb-4">
                            <label class="form-label font-weight-bold small text-muted">Role</label>
                            {{-- id="roleSelect" --}}
                            <select name="role" id="roleSelect" class="form-select form-control" required>
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ in_array($role->name, $userRoles) ? 'selected' : '' }}>
                                    {{ strtoupper($role->name) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="card mb-4 bg-light border-0 shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 border-0">
                                <h6 class="mb-0 font-weight-bold small text-muted">Custom Permissions</h6>
                                {{-- Select All Feature --}}
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label small font-weight-bold text-primary" for="selectAll" style="cursor: pointer;">
                                        select all
                                    </label>
                                </div>
                            </div>
                            <div class="card-body bg-white rounded-bottom">
                                <div class="row">
                                    @foreach($permissions as $permission)
                                    <div class="col-md-4 col-lg-3 mb-2">
                                        <div class="form-check">
                                            {{-- class="permission-checkbox"--}}
                                            <input class="form-check-input permission-checkbox" type="checkbox" name="custom_permissions[]"
                                                value="{{ $permission->name }}" id="perm-{{ $permission->id }}"
                                                {{ in_array($permission->name, $userPermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="perm-{{ $permission->id }}" style="cursor: pointer;">
                                                {{ str_replace('-', ' ', $permission->name) }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light px-4 border">Discard</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
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
        const roleSelect = document.getElementById('roleSelect');
        const selectAllCb = document.getElementById('selectAll');
        const permissionCbs = document.querySelectorAll('.permission-checkbox');
        const form = roleSelect ? roleSelect.closest('form') : null;

        // Initial State Check
        if (roleSelect) handleRoleChange(roleSelect.value, false);
        updateSelectAllState();

        //  Role Change Event
        if (roleSelect) {
            roleSelect.addEventListener('change', function() {
                handleRoleChange(this.value, true);
            });
        }

        async function handleRoleChange(roleName, isManualChange) {
            if (!roleName) return;

            // Super Admin Tick  Disable
            if (roleName === 'super-admin') {
                permissionCbs.forEach(cb => {
                    cb.checked = true;
                    cb.disabled = true;
                });
                if (selectAllCb) {
                    selectAllCb.checked = true;
                    selectAllCb.disabled = true;
                }
            } else {
                // Role Reset
                if (isManualChange) {
                    permissionCbs.forEach(cb => {
                        cb.checked = false;
                        cb.disabled = false;
                    });
                } else {
                    permissionCbs.forEach(cb => cb.disabled = false);
                }

                if (selectAllCb) selectAllCb.disabled = false;

                try {
                    const url = "{{ route('admin.get_role_permissions', ':name') }}".replace(':name', roleName);
                    const response = await fetch(url);
                    if (!response.ok) throw new Error('Route not found');

                    const permissionNames = await response.json();
                    permissionCbs.forEach(cb => {
                        if (permissionNames.includes(cb.value)) {
                            cb.checked = true;
                        }
                    });
                    updateSelectAllState();
                } catch (error) {
                    console.error('Error fetching role permissions:', error);
                }
            }
        }

        // Select All Logic
        if (selectAllCb) {
            selectAllCb.addEventListener('change', function() {
                permissionCbs.forEach(cb => {
                    if (!cb.disabled) cb.checked = this.checked;
                });
            });
        }

        // Select All Update
        permissionCbs.forEach(cb => {
            cb.addEventListener('change', updateSelectAllState);
        });

        function updateSelectAllState() {
            if (!selectAllCb) return;
            const enabledCbs = Array.from(permissionCbs);
            const checkedCount = enabledCbs.filter(c => c.checked).length;

            selectAllCb.checked = (enabledCbs.length > 0 && checkedCount === enabledCbs.length);
            selectAllCb.indeterminate = (checkedCount > 0 && checkedCount < enabledCbs.length);
        }

        // Submit  Disabled Checkboxes 
        if (form) {
            form.addEventListener('submit', function() {
                permissionCbs.forEach(cb => cb.disabled = false);
            });
        }
    });
</script>
@endpush
@endsection
