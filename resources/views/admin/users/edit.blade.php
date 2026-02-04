@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit User</h3>
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password (leave blank to keep)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-control">
                <option value="">-- Select Role --</option>
                @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <hr>
        <h5>Permission Matrix</h5>
        <p class="small text-muted">Permissions inherited from the user's role are marked as <span class="badge bg-secondary text-white">inherited</span>. You may grant additional direct permissions below.</p>
        <div class="card p-3 mb-3">
            @foreach($matrix as $moduleKey => $perms)
            <div class="mb-2">
                <strong class="mb-1 d-block">{{ ucwords($moduleKey) }}</strong>
                <div class="d-flex flex-wrap">
                    @foreach($perms as $perm)
                    @php
                        $isDirect = in_array($perm['name'], $directPerms ?? []);
                        $isInherited = false;
                        foreach($user->roles as $r) {
                            if(in_array($perm['name'], $rolePerms[$r->name] ?? [])) { $isInherited = true; break; }
                        }
                    @endphp
                    <label class="mr-3 mb-2" style="min-width:150px;">
                        <input type="checkbox" name="permissions[]" value="{{ $perm['name'] }}" class="perm-checkbox"
                            {{ $isDirect ? 'checked' : '' }} {{ $isInherited && ! $isDirect ? 'disabled' : '' }}>
                        {{ $perm['label'] }}
                        @if($isInherited && ! $isDirect)
                            <span class="ms-1"><span class="badge bg-secondary text-white">inherited</span></span>
                        @endif
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        @can('manage-users')
            <button class="btn btn-primary">Update</button>
        @endcan
    </form>
</div>

<script>
    const rolePerms = @json($rolePerms ?? []);
    const roleSelect = document.querySelector('select[name="role"]');
    function applyRoleInheritance() {
        const selected = roleSelect.value;
        const inherited = rolePerms[selected] || [];
        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            const val = cb.value;
            const parent = cb.parentElement;
            const existingBadge = parent.querySelector('.perm-badge');
            // If this permission is inherited and not a direct grant, mark and disable
            if (inherited.includes(val) && !cb.checked) {
                cb.checked = true;
                cb.disabled = true;
                if(!existingBadge) {
                    const span = document.createElement('span');
                    span.className = 'ms-1 perm-badge';
                    span.innerHTML = '<span class="badge bg-secondary text-white">inherited</span>';
                    parent.appendChild(span);
                }
            } else if (!inherited.includes(val)) {
                if (cb.disabled) cb.disabled = false;
                if (existingBadge) existingBadge.remove();
            }
        });
    }
    roleSelect?.addEventListener('change', applyRoleInheritance);
    document.addEventListener('DOMContentLoaded', applyRoleInheritance);
</script>
@endsection
