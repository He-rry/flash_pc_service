@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@extends('layouts.app')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white">
        <h4>Edit Service Task</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('services.update', $service->id) }}" method="POST">
            @csrf
            @method('PUT') <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Customer Name</label>
                    <input type="text" name="customer_name" value="{{ $service->customer_name }}" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Customer Phone *</label>
                    <input type="text" name="customer_phone" value="{{ $service->customer_phone }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Service Type *</label>
                    <select name="service_type_id" class="form-select" required>
                        @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ $service->service_type_id == $type->id ? 'selected' : '' }}>
                            {{ $type->service_name}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Status</label>
                    <select name="status_id" class="form-select">
                        @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ $service->status_id == $status->id ? 'selected' : '' }}>
                            {{ $status->status_name}}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Service</button>
            <a href="{{ route('services.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>
</div>
@endsection