@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 text-primary font-weight-bold">
            <i class="fas fa-list mr-2"></i>Service Types
        </h4>
        @can('create-service-type')
        <x-app-button
            href="{{ route('admin.service-types.create') }}"
            color="primary"
            icon="fas fa-plus">
            Add New Type
        </x-app-button>
        @endcan
    </div>

    @php
    // Table Header သတ်မှတ်ချက်
    $tableHeaders = [
    'Name' => '30%',
    'Actions' => [
    'width' => '25%',
    'align' => 'text-center',
    'canany' => ['edit-service-type', 'delete-service-type']
    ]
    ];
    @endphp

    <x-app-table :headers="$tableHeaders" :items="$types">
        @foreach($types as $type)
        <tr class="align-middle">
            {{-- Name Column --}}
            <td class="py-3 px-4 font-weight-bold text-dark">
                {{ $type->service_name }}
            </td>

            {{-- Actions Column --}}
            {{-- Permission ရှိမှသာ Actions column ထဲက code တွေကို render လုပ်မယ် --}}
            @if(isset($tableHeaders['Actions']))
            <td>
                <div class="btn-action-group d-flex justify-content-center gap-2">
                    {{-- Edit Button --}}
                    <x-app-button
                        permission="edit-service-type"
                        color="lights" size="sm"
                        icon="fas fa-edit text-warning"
                        href="{{ route('admin.service-types.edit', $type->id) }}"
                        textStyle="text-warning">
                        Edit
                    </x-app-button>

                    {{-- Delete Button --}}
                    @can('delete-service-type')
                    <form action="{{ route('admin.service-types.destroy', $type->id) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this service type?')">
                        @csrf
                        @method('DELETE')
                        <x-app-button
                            type="submit"
                            color="light" size="sm"
                            icon="fas fa-trash text-danger"
                            textStyle="text-danger">
                            Delete
                        </x-app-button>
                    </form>
                    @endcan
                </div>
            </td>
            @endif
        </tr>
        @endforeach
    </x-app-table>
</div>
@endsection