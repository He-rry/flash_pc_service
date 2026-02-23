@props([
'type' => 'button',
'color' => 'primary',
'outline' => false,
'size' => null,
'icon' => null,
'href' => null,
'permission' => null,
'onclick' => null,
'textStyle'=> null,
])

@php
// Button Class
$btnStyle = $outline ? "btn-outline-{$color}" : "btn-{$color}";
$btnSize = $size ? "btn-{$size}" : "";
$classes = "btn {$btnStyle} {$btnSize} d-inline-flex align-items-center justify-content-center gap-2 shadow-sm transition-all";
@endphp

@if(!$permission || auth()->user()->can($permission))
@if($href)
<a href="{{ $href }}"
    {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon) <i class="{{ $icon }}"></i>
    @endif
    <span class="{{$textStyle}}">{{ $slot }}</span>
</a>
@else
<button type="{{ $type }}"
    @if($onclick) onclick="{{ $onclick }}" @endif
    {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon) <i class="{{ $icon }}"></i>
    @endif
    <span class="{{$textStyle}}">{{ $slot }}</span>
</button>
@endif

@endif