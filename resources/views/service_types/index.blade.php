@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>Service Types Management</h4>
    <a href="{{ route('admin.service-types.create') }}" class="btn btn-primary">Add New Service Type</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($types as $type)
                <tr>
                    <td>{{ $type->service_name }}</td>
                    <td>
                        <a href="{{ route('admin.service-types.edit', $type->id) }}" class="btn btn-sm btn-info text-white">Edit</a>
                        <form action="{{ route('admin.service-types.destroy', $type->id) }}" method="POST" class="d-inline">
                            @csrf 
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection