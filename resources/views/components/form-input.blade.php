@props([
'name',
'label' => null,
'type' => 'text',
'value' => '',
'placeholder' => '',
'required' => false
])

<div class="form-group mb-2">
    @if($label)
    <label class="small font-weight-bold text-muted ml-1 uppercase" for="{{ $attributes->get('id') ?? $name }}">
        {{ $label }} @if($required) <span class="text-danger">*</span> @endif
    </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        {{ $attributes->merge([
            'id' => $name,
            'class' => 'form-control form-control-sm ' . ($errors->has($name) ? 'is-invalid' : '')
        ]) }}
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}>

    @error($name)
    <div class="invalid-feedback font-weight-bold" style="font-size: 11px;">
        {{ $message }}
    </div>
    @enderror
</div>