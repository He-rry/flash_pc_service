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
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold">{{ $user->name }}</span>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->roles->count() > 0)
                                @foreach($user->roles as $role)
                                <span class="badge bg-info text-dark rounded-pill shadow-xs">{{ $role->name }}</span>
                                @endforeach
                                @else
                                <span class="badge bg-secondary rounded-pill">No Role</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @can('manage-users')
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('ဒီ User ကို ဖျက်မှာ သေချာပါသလား?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger ms-1">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection