@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 text-primary">Admin & User List</h4>
        @can('manage-users')
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Add New User
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="{{ $user->trashed() ? 'table-light text-muted' : '' }}">
                        <td>
                            {{ $user->name }}
                            @if($user->trashed())
                            <span class="badge bg-danger ms-1" style="font-size: 10px;">Deleted</span>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                            <span class="badge bg-info text-dark">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($user->trashed())
                                <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline me-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('admin.users.forceDelete', $user->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('သတိပြုရန်! ဤ User ကို အပြီးတိုင်ဖျက်ပါက ပြန်ယူ၍မရတော့ပါ။')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-dark">
                                        <i class="fas fa-eraser"></i> Force Delete
                                    </button>
                                </form>
                                @else
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary me-2">
                                    <i class="fas fa-edit"></i> Edit
                                </a>

                                @can('user-delete')
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                    onsubmit="return confirm('ဒီ User ကို ဖျက်မှာ သေချာပါသလား?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                                @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection