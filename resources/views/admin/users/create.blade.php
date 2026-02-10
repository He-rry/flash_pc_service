@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary"><i class="fas fa-user-plus me-2"></i>Create New Admin</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.users.store') }}" method="POST" id="userForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter full name" required>
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="example@mail.com" required>
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold small text-muted">Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold small text-muted">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-weight-bold small text-muted">Role</label>
                            <select name="role" id="roleSelect" class="form-select form-control @error('role') is-invalid @enderror" required>
                                <option value="">-- Select Role --</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                    {{ strtoupper($role->name) }}
                                </option>
                                @endforeach
                            </select>
                            @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="card mt-4 border-light shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                <h6 class="mb-0 small font-weight-bold text-dark">Custom Permissions</h6>
                                {{-- Select All Feature --}}
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label small font-weight-bold text-primary" for="selectAll" style="cursor: pointer;">
                                        Select All
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($permissions as $permission)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox" type="checkbox"
                                                name="custom_permissions[]"
                                                value="{{ $permission->name }}"
                                                id="perm-{{ $permission->id }}"
                                                {{ (is_array(old('custom_permissions')) && in_array($permission->name, old('custom_permissions'))) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="perm-{{ $permission->id }}" style="cursor: pointer;">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary py-2 font-weight-bold shadow-sm">
                                <i class="fas fa-save me-1"></i> Create
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

        // Role Change Event
        if (roleSelect) {
            roleSelect.addEventListener('change', function() {
                handleRoleChange(this.value, true);
            });
        }

        async function handleRoleChange(roleName, isManualChange) {
            if (!roleName) return;
            if (roleName === 'super-admin') {
                permissionCbs.forEach(cb => {
                    cb.checked = true;
                    cb.disabled = true;
                });
                if (selectAllCb) {
                    selectAllCb.checked = true;
                    selectAllCb.disabled = true;
                }
                return;
            } 
            permissionCbs.forEach(cb => cb.disabled = false);
            if (selectAllCb) selectAllCb.disabled = false;

            if (isManualChange) {
                permissionCbs.forEach(cb => cb.checked = false);
            }

            try {
                const url = "{{ route('admin.get_role_permissions', ':name') }}".replace(':name', roleName);
                
                // --- ပြင်ဆင်လိုက်သည့်အပိုင်း (Fetch ထည့်သွင်းခြင်း) ---
                const response = await fetch(url); 
                if (!response.ok) throw new Error('Network response was not ok');
                
                const permissionNames = await response.json();
                // -------------------------------------------

                // 3. Database မှလာသော Permission list အတိုင်း Checkbox များကို Update လုပ်ခြင်း
                permissionCbs.forEach(cb => {
                    // ပါဝင်သော permission ကိုသာ checked လုပ်ပြီး ကျန်တာကို uncheck လုပ်မည်
                    cb.checked = permissionNames.includes(cb.value);
                });

                updateSelectAllState();

            } catch (error) {
                console.error('Error fetching role permissions:', error);
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

        // တစ်ခုချင်းစီနှိပ်လျှင် Select All state ကို update လုပ်ခြင်း
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

        // Form Submit လုပ်လျှင် Disabled ဖြစ်နေသော value များပါ ပါသွားစေရန်
        if (form) {
            form.addEventListener('submit', function() {
                permissionCbs.forEach(cb => cb.disabled = false);
            });
        }
    });
</script>
@endpush
@endsection
