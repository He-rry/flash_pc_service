@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Add User</h3>
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-control">
                <option value="">-- Select Role --</option>
                @foreach($roles as $role)
                <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <hr>
        <h5>Permission Matrix</h5>
        <p class="small text-muted">Select direct permissions for this user. Permissions that come from the selected role will be shown as <span class="badge bg-secondary text-white">inherited</span>.</p>
        <div class="card p-3 mb-3">
            @foreach($matrix as $moduleKey => $perms)
            <div class="mb-2">
                <strong class="mb-1 d-block">{{ ucwords($moduleKey) }}</strong>
                <div class="d-flex flex-wrap">
                    @foreach($perms as $perm)
                    <label class="mr-3 mb-2" style="min-width:150px;">
                        <input type="checkbox" name="permissions[]" value="{{ $perm['name'] }}" class="perm-checkbox"> {{ $perm['label'] }}
                        <span class="perm-badge text-muted small ms-1"></span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @can('manage-users')
            <button class="btn btn-primary">Create</button>
        @endcan
    </form>
</div>
<script>
    // rolePerms passed from controller
    const rolePerms = @json($rolePerms ?? []);
    const roleSelect = document.querySelector('select[name="role"]');

    function applyRoleInheritance() {
        const selected = roleSelect.value;
        const inherited = rolePerms[selected] || [];
        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            const val = cb.value;
            const badge = cb.parentElement.querySelector('.perm-badge');
            if (inherited.includes(val)) {
                cb.checked = true;
                cb.disabled = true;
                if (badge) badge.innerHTML = '<span class="badge bg-secondary text-white">inherited</span>';
            } else {
                cb.disabled = false;
                if (badge) badge.innerHTML = '';
            }
        });
    }

    roleSelect?.addEventListener('change', applyRoleInheritance);
    // apply on load
    document.addEventListener('DOMContentLoaded', applyRoleInheritance);
</script>
@endsection
