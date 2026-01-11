@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Service Records</h3>
    <a href="{{route('services.create')}}" class="btn btn-primary">Add New Service</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Customer</th>
                    <th>PC Model</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr>
                    <td>
                        <strong>{{ $service->customer_name }}</strong><br>
                        <small class="text-muted">{{ $service->customer_phone }}</small>
                    </td>
                    <td>{{ $service->pc_model }}</td>
                    <td>{{ $service->serviceType->service_name }}</td>
                    <td>
                        <span class="badge {{ $service->status->status_name == 'Finish' ? 'bg-success' : 'bg-info' }}">
                            {{ $service->status->status_name }}
                        </span>
                    </td>
                    <td>{{ $service->created_at->format('d-M-Y') }}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-info text-white"
                            data-bs-toggle="modal"
                            data-bs-target="#viewModal{{ $service->id }}">
                            View Info
                        </button>
                        <a href="{{ route('services.edit', $service->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('services.destroy', $service->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>

                <!--customers _info  box popup pya  -->
                <div class="modal fade" id="viewModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Customer Information</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-start">
                                <div class="mb-3">
                                    <label class="text-muted small d-block">Customer Name</label>
                                    <p class="fw-bold mb-0">{{ $service->customer_name }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small d-block">Phone Number</label>
                                    <p class="fw-bold mb-0">{{ $service->customer_phone }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small d-block">Address</label>
                                    <p class="mb-0">{{ $service->customer_address }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small d-block">Location Coordinates</label>
                                    <p class="mb-2">{{ $service->lat }}, {{ $service->long }}</p>
                                    <a href="https://www.google.com/maps?q={{ $service->lat }},{{ $service->long }}"
                                        target="_blank"
                                        class="btn btn-danger btn-sm w-100">
                                        Open in Google Maps
                                    </a>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <label class="text-muted small d-block">Issue Description</label>
                                    <p class="text-danger mb-0">{{ $service->issue_description }}</p>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label class="text-muted small d-block">PC Model</label>
                                        <p class="mb-0">{{ $service->pc_model }}</p>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small d-block">Service Type</label>
                                        <p class="mb-0">{{ $service->serviceType->service_name }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <a href="{{ route('services.edit', $service->id) }}" class="btn btn-primary">Edit Service</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            {{ $services->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection