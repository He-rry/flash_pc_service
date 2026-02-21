@extends('layouts.app')

@section('content')
    {{-- Header Component --}}
    <x-page-header 
        title="Status Management" 
        :addRoute="route('admin.statuses.create')" 
        addPermission="add-status" 
    />

    <div class="card d-flex justify-content-between mb-3">
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
                                deletePermission="delete-status" 
                            />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection