@php
    $isMultiple = isset($attributes['multiple']) ? true : false;
    $placeHolder = isset($attributes['placeHolder']) ? $attributes['placeHolder'] : __('Search...');
    $parentId = isset($attributes['parentId']) ? $attributes['parentId'] : "temp";
@endphp

<div x-data="{
    model: @entangle($attributes->wire('model')),
}" x-init="
    $($refs.select).select2({
        placeholder: '{{ $placeHolder }}',
        allowClear: true,
        tags: true,
        dropdownParent: $('#{{ $parentId }}'),
        multiple: {{ $isMultiple ? 'true' : 'false' }},
        // Add more Select2 options here as needed
    }).on('select2:clear', (event) => {
        $wire.set($refs.select.name, '');
    });
    $refs.select.onchange = (event) => {
        if (event.target.hasAttribute('multiple')) {
            model = Array.from(event.target.options).filter(option => option.selected).map(option => option.value);
        } else {
            model = event.target.value;
        }
        $wire.set($refs.select.name, model);
    };
" wire:ignore>
    <select x-ref="select" {{ $attributes->merge(['class' => 'custom-select2']) }}>
        {{ $slot }}
    </select>
</div>
