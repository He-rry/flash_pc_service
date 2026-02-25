@props([
'type' => 'button',
'color' => 'primary',
'outline' => false,
'size' => null,
'icon' => null,
'href' => null,
'permission' => null,
'onclick' => null,
'textStyle' => null,
'confirm' => null,
'method' => 'GET', // DELETE, POST, PUT စတာတွေအတွက်
])

@php
$btnStyle = $outline ? "btn-outline-{$color}" : "btn-{$color}";
$btnSize = $size ? "btn-{$size}" : "";
$classes = "btn {$btnStyle} {$btnSize} d-inline-flex align-items-center justify-content-center gap-2 shadow-sm transition-all";

// JS Logic for confirm & method
$finalOnclick = $onclick;
if ($confirm && $method === 'GET') {
$finalOnclick = "return confirm('{$confirm}')";
}
@endphp
@if(!$permission || auth()->user()->can($permission))
@if($method !== 'GET')
<form action="{{ $href }}" method="POST" class="d-inline"
    @if($confirm) onsubmit="return confirm('{{ $confirm }}')" @endif>
    @csrf
    @method($method)
    <button type="submit" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon) <i class="{{ $icon }}"></i> @endif
        <span class="{{ $textStyle }}">{{ $slot }}</span>
    </button>
</form>
@elseif($href)
<a href="{{ $href }}"
    @if($finalOnclick) onclick="{!! $finalOnclick !!}" @endif
    {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon) <i class="{{ $icon }}"></i> @endif
    <span class="{{ $textStyle }}">{{ $slot }}</span>
</a>
@else
<button type="{{ $type }}"
    @if($finalOnclick) onclick="{!! $finalOnclick !!}" @endif
    {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon) <i class="{{ $icon }}"></i> @endif
    <span class="{{ $textStyle }}">{{ $slot }}</span>
</button>
@endif

@endif