@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>⚠️ မအောင်မြင်ပါ!</strong> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>Status Management</h4>
    <a href="{{ route('statuses.create') }}" class="btn btn-primary">Add New Status</a>
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
                @foreach($statuses as $status)
                <tr>
                    <td>{{ $status->status_name }}</td>
                    <td>
                        <a href="{{ route('statuses.edit', $status->id) }}" class="btn btn-sm btn-info text-white">Edit</a>
                        <form action="{{ route('statuses.destroy', $status->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
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