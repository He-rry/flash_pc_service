{{-- resources/views/components/app-table.blade.php --}}
@props([
'headers' => [],
'items' => null,
'id' => null,
'tableClass' => '',
'theadClass' => 'bg-light border-bottom'
])

@php
// Logic: Permission
$activeHeaders = [];
foreach($headers as $name => $config) {
$permission = is_array($config) ? ($config['permission'] ?? null) : null;
$canany = is_array($config) ? ($config['canany'] ?? null) : null;

if ( (!$permission || auth()->user()->can($permission)) &&
(!$canany || auth()->user()->canany($canany)) ) {
$activeHeaders[$name] = $config;
}
}
$columnCount = count($activeHeaders);
@endphp

@once
<style>
    .app-custom-table {
        table-layout: fixed;
        width: 100%;
        border-collapse: separate;
    }

    .app-custom-table thead th {
        white-space: nowrap;
        vertical-align: middle;
    }

    .app-custom-table tbody td {
        vertical-align: middle !important;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .btn-action-group {
        display: flex;
        flex-wrap: nowrap;
        justify-content:flex-start;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-action-group .btn {
        justify-content: center;
    }
</style>
@endonce

<div class="card border shadow-sm mt-3 overflow-hidden">
    <div {{ $attributes->merge(['class' => 'table-responsive']) }}>
        <table class="table table-hover app-custom-table {{ $tableClass }} mb-0" @if($id) id="{{ $id }}" @endif>
            <thead class="{{ $theadClass }}">
                <tr>
                    @foreach($activeHeaders as $name => $config)
                    @php
                    $width = is_array($config) ? ($config['width'] ?? 'auto') : $config;
                    $align = is_array($config) ? ($config['align'] ?? ($loop->last ? 'text-right' : '')) : ($loop->last ? 'text-right' : '');
                    @endphp
                    <th style="width: {{ $width }};" class="py-3 px-4 small font-weight-bold text-muted {{ $align }}">
                        {{ is_numeric($name) ? $config : $name }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="{{ $id ? $id . 'Body' : '' }}" class="bg-white">
                @if($items && $items->count() > 0)
                {{ $slot->withAttributes(['activeHeaders' => $activeHeaders]) }}
                @else
                <tr>
                    <td colspan="{{ count($activeHeaders) }}" class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i> No data found.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @if($items && method_exists($items, 'links'))
    <div class="card-footer bg-white py-3 border-top">
        <div class="d-flex justify-content-center">
            {{ $items->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
</div>