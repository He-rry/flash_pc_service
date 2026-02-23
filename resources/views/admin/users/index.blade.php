@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 text-primary font-weight-bold">Admin & User List</h4>
        @can('manage-users')
        <x-app-button href="{{ route('admin.users.create') }}" color="primary" icon="fas fa-plus">
            Add New User
        </x-app-button>
        @endcan
    </div>

    @php
    $tableHeaders = [
    'Name' => '25%',
    'Email' => '25%',
    'Role' => '20%',
    'Action' => [
    'width' => '30%',
    'align' => 'text-right',
    'canany' => ['manage-users', 'user-delete']
    ]
    ];
    @endphp

    <x-app-table :headers="$tableHeaders" :items="$users">
        @foreach($users as $user)
        <tr class="align-middle {{ $user->trashed() ? 'table-light text-muted' : '' }}">
            {{-- Name Column --}}
            <td class="py-3 px-4">
                <span class="font-weight-bold text-dark">{{ $user->name }}</span>
                @if($user->trashed())
                <span class="badge bg-danger ms-1" style="font-size: 10px;">Deleted</span>
                @endif
            </td>

            {{-- Email Column --}}
            <td class="py-3 px-4 text-secondary">{{ $user->email }}</td>

            {{-- Role Column --}}
            <td class="py-3 px-4">
                @foreach($user->roles as $role)
                <span class="badge bg-info text-dark rounded-pill px-3 shadow-sm">
                    {{ $role->name }}
                </span>
                @endforeach
            </td>
            {{-- Action Column --}}
            <td class="py-3 px-4">
                <div class="btn-action-group d-flex justify-content-start gap-2">
                    @if($user->trashed())
                    {{-- Restore Button (Original Green Style) --}}
                    <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline">
                        @csrf
                        <x-app-button type="submit" color="outline-success" size="sm" icon="fas fa-undo">
                            Restore
                        </x-app-button>
                    </form>
                    <form action="{{ route('admin.users.forceDelete', $user->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Are you sure you want to permanently delete this user?')">
                        @csrf
                        <x-app-button type="submit" color="outline-dark" size="sm" icon="fas fa-eraser">
                            Force Delete
                        </x-app-button>
                    </form>
                    @else
                    @if($user->hasRole('super-admin'))
                    <span class="badge bg-secondary px-2 py-2 shadow-sm" style="font-size: 11px;">
                        <i class="fas fa-lock me-1"></i> System Protected
                    </span>
                    @else
                    <x-app-button
                        href="{{ route('admin.users.edit', $user->id) }}"
                        color="light" size="sm"
                        icon="fas fa-edit text-warning"
                        textStyle="text-warning">
                        Edit
                    </x-app-button>
                    @can('user-delete')
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Are you sure you want to delete this user?')">
                        @csrf
                        @method('DELETE')
                        <x-app-button type="submit" color="light" size="sm" icon="fas fa-trash text-danger"
                            textStyle="text-danger">
                            Delete
                        </x-app-button>
                    </form>
                    @endcan
                    @endif
                    @endif
                </div>
            </td>
        </tr>
        @endforeach
    </x-app-table>
    <div class="mt-4">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection