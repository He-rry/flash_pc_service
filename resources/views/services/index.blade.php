@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-weight-bold text-dark mb-0">
        <i class="fas fa-tools mr-2 text-primary"></i>Service Records
    </h3>
    <x-app-button
        href="{{ route('admin.services.create') }}"
        color="primary"
        icon="fas fa-plus-circle mr-1"
        permission="add-services">
        Add New Service
    </x-app-button>
</div>
@php
$rawHeaders = [
'Customer' => '15%',
'PC Model' => '15%',
'Status' => ['width' => '10%', 'align' => 'text-center'],
'Date' => '15%',
'Actions' => [
'width' => '15%',
'align' => 'text-right',
'canany' => ['edit-services', 'delete-services', 'view-services-info']
]
];
@endphp
<x-app-table :headers="$rawHeaders" :items="$services">
    @foreach($services as $service)
    <tr class="align-middle">
        <td class="py-3 px-4 font-weight-bold text-dark">{{ $service->customer_name }}</td>
        <td class="py-3 px-4 text-secondary">{{ $service->pc_model }}</td>
        <td class="py-3 px-4 text-center">
            <span class="badge {{ $service->status->status_name == 'Finish' ? 'bg-success' : 'bg-info' }} rounded-pill px-3">
                {{ $service->status->status_name }}
            </span>
        </td>
        <td class="py-3 px-4 text-muted small">
            {{ $service->created_at->format('d/m/Y') }}
        </td>

        {{-- Show Actions column only if the header exists in $activeHeaders (permission granted) --}}
        @if(isset($rawHeaders['Actions']))
        <td>
            <div class="btn-action-group">
                <x-app-button permission="view-services-info" color="light" size="sm" icon="fas fa-eye text-info"
                    data-bs-toggle="modal" data-bs-target="#viewModal{{ $service->id }}"
                    textStyle="text-info">
                    View
                </x-app-button>

                <x-app-button permission="edit-services" color="light" size="sm" icon="fas fa-edit text-warning"
                    href="{{ route('admin.services.edit', $service->id) }}"
                    textStyle="text-warning">
                    Edit
                </x-app-button>


                <x-app-button permission="delete-services" color="light" size="sm" icon="fas fa-trash text-danger"
                    textStyle="text-danger">
                    Delete
                </x-app-button>
            </div>
        </td>
        @endif
    </tr>

    {{-- Modal – remains unchanged --}}
    <div class="modal fade" id="viewModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title font-weight-bold">Service Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <div class="row g-3">
                        <div class="col-12 border-bottom pb-2">
                            <label class="text-muted small d-block">Customer</label>
                            <p class="h6 font-weight-bold mb-0">{{ $service->customer_name }}</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block">Phone</label>
                            <p class="mb-0 font-weight-bold">{{ $service->customer_phone }}</p>
                        </div>
                        <div class="col-12 bg-light p-3 rounded">
                            <label class="text-muted small d-block">Issue</label>
                            <p class="text-danger mb-0 font-weight-bold">{{ $service->issue_description }}</p>
                        </div>
                        <div class="col-12 mt-3">
                            <a href="https://www.google.com/maps?q={{ $service->lat }},{{ $service->long }}"
                                target="_blank" class="btn btn-outline-danger btn-sm w-100 py-2">
                                <i class="fas fa-map-marked-alt mr-1"></i> View on Google Maps
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</x-app-table>
@endsection