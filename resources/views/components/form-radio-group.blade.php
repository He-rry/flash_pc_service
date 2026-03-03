@props([
'name',
'label' => null,
'options' => [], {{-- ['value' => 'label'] --}}
'selected' => null
])

<div class="form-group mb-3">
    @if($label)
    <label class="small font-weight-bold text-muted mb-2 d-block text-uppercase">
        {{ $label }}
    </label>
    @endif

    <div class="btn-group btn-group-toggle d-flex width 25%" data-toggle="buttons">
        @foreach($options as $value => $text)
        <label class="btn btn-outline-primary btn-sm flex-fill {{ $selected == $value ? 'active' : '' }}">
            <input type="radio"
                name="{{ $name }}"
                value="{{ $value }}"
                autocomplete="off"
                {{ $selected == $value ? 'checked' : '' }}>
            {{ $text }}
        </label>
        @endforeach
    </div>
</div>