@props(['title', 'addRoute' => null, 'addPermission' => null, 'linkText' => 'Add New'])

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>{{ $title }}</h4>
    @if($addRoute && (!$addPermission || auth()->user()->can($addPermission)))
        <a href="{{ $addRoute }}" class="btn btn-primary">
            <i class="fas fa-plus-circle mr-1"></i> {{ $linkText }}
        </a>
    @endif
</div>