@extends('layouts.app')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white">
        <h4>Add New PC Service Task</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.shops.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Customer Name *</label>
                    <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" required>
                    @error('customer_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label>Customer Phone *</label>
                    <input type="text" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" required>
                    @error('customer_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label>Service Type *</label>
                    <select name="service_type_id" class="form-select" required>
                        @foreach($types as $type)
                        <option value="{{ $type->id }}">{{ $type->service_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>PC Model</label>
                    <input type="text" name="pc_model" class="form-control">
                </div>
                @error('pc_model')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="col-md-12 mb-3">
                    <label>Address *</label>
                    <textarea name="customer_address" class="form-control" rows="2" required></textarea>
                </div>
            </div>
            @can('manage-services')
                <button type="submit" class="btn btn-success">Save Service</button>
            @endcan
            <a href="{{ route('admin.services.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>
</div>
@endsection