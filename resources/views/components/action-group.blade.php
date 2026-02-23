@props([
    'viewRoute' => null,
    'editRoute' => null, 
    'deleteRoute' => null, 
    'permission' => null {{-- လိုအပ်ရင် permission စစ်ဖို့ --}}
])

<div {{ $attributes->merge(['class' => 'd-flex align-items-center gap-2']) }}>
    
    {{-- View Button --}}
    @if($viewRoute)
        <a href="{{ $viewRoute }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-eye"></i> View
        </a>
    @endif

    {{-- Edit Button --}}
    @if($editRoute)
        <a href="{{ $editRoute }}" class="btn btn-sm btn-info text-white shadow-sm">
            <i class="fas fa-edit"></i> Edit
        </a>
    @endif

    {{-- Delete Button --}}
    @if($deleteRoute)
        <form action="{{ $deleteRoute }}" method="POST" class="d-inline" onsubmit="return confirm('သေချာပါသလား?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger shadow-sm">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    @endif

    {{ $slot }}
</div>