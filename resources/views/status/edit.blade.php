@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Edit Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.statuses.update', $status->id) }}" method="POST">
                        @csrf
                        @method('PUT') <div class="mb-3">
                            <label for="name" class="form-label">Status Name</label>
                            <input type="text" 
                                   name="status_name" 
                                   id="status_name" 
                                   class="form-control @error('status_name') is-invalid @enderror" 
                                   value="{{ old('status_name', $status->status_name) }}" 
                                   required>
                            
                            @error('status_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.statuses.index') }}" class="btn btn-light">Cancel</a>
                            @can('edit-status')
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection