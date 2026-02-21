@props(['editRoute' => null, 'deleteRoute' => null, 'editPermission' => null, 'deletePermission' => null])

<div class="d-flex align-items-center gap-3">
    {{-- Edit Button --}}
    @if($editRoute && (!$editPermission || auth()->user()->can($editPermission)))
    <a href="{{ $editRoute }}" class="btn btn-sm btn-info text-white">
        <i class="fas fa-edit"></i> Edit
    </a>
    @endif

    {{-- Delete Button --}}
    @if($deleteRoute && (!$deletePermission || auth()->user()->can($deletePermission)))
    <form action="{{ $deleteRoute }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">
            <i class="fas fa-trash"></i> Delete
        </button>
    </form>
    @endif
</div>