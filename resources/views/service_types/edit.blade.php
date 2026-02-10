@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Edit ServiceTypes</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.service-types.update', $type->id) }}" method="POST">
                        @csrf
                        @method('PUT') <div class="mb-3">
                            <label for="name" class="form-label">Service Type Name</label>
                            <input type="text"
                                name="service_name"
                                id="service_name"
                                class="form-control @error('service_name') is-invalid @enderror"
                                value="{{ old('service_name', $type->service_name) }}"
                                required>

                            @error('service_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.service-types.index') }}" class="btn btn-light">Cancel</a>
                            @can('edit-services')
                                <button type="submit" class="btn btn-primary">Update Service Type</button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection