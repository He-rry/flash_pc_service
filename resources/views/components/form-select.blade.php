@props(['name', 'label' => null])
<div class="form-group mb-2">
    @if($label)
        <label class="small font-weight-bold text-muted ml-1 uppercase">{{ $label }}</label>
    @endif
    <select name="{{ $name }}" {{ $attributes->merge(['class' => 'form-control form-control-sm', 'id' => $name]) }}>
        {{ $slot }}
    </select>
</div>