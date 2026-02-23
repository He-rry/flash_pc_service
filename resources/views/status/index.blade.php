@extends('layouts.app')

@section('content')
{{-- Header Component --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Statuses Management</h4>
    <x-app-button
        href="{{ route('admin.statuses.create') }}"
        color="success"
        icon="fas fa-plus-circle mr-1"
        permission="add-status">
        Add New Status
    </x-app-button>
</div>
<div class="card-body">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statuses as $status)
            <tr>
                <td>{{ $status->status_name }}</td>
                <td class="text-right">
                    {{-- Action Buttons Component --}}
                    <x-table-action
                        :editRoute="route('admin.statuses.edit', $status->id)"
                        :deleteRoute="route('admin.statuses.destroy', $status->id)"
                        editPermission="edit-status"
                        deletePermission="delete-status" />
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection