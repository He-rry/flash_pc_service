@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Add New Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.statuses.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Status Name</label>
                            <input type="text" name="status_name" id="status_name" class="form-control @error('status_name') is-invalid @enderror" placeholder="e.g. Completed" required>
                            @error('status_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.statuses.index') }}" class="btn btn-light">Back</a>
                            <button type="submit" class="btn btn-success">Save Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection