@php
    $isMultiple = isset($attributes['multiple']) ? true : false;
    $placeHolder = isset($attributes['placeHolder']) ? $attributes['placeHolder'] : __('Search...');
    $parentId = isset($attributes['parentId']) ? $attributes['parentId'] : "temp";
@endphp

<div x-data="{
    model: @entangle($attributes->wire('model')),
}" 
x-init="
    $($refs.select).select2({
        placeholder: '{{ $placeHolder }}',
        allowClear: true,
        dropdownParent: $('#{{ $parentId }}'),
        multiple: {{ $isMultiple ? 'true' : 'false' }},
    })
    .on('select2:select', (event) => {
        // Update the Alpine model when a selection is made
        let value = $(event.target).val();
        if ({{ $isMultiple ? 'true' : 'false' }}) {
            model = value ? value : [];
        } else {
            model = value ? value : '';
        }
        $wire.set($refs.select.name, model);
    })
    .on('select2:clear', () => {
        // Handle clearing the selection
        model = {{ $isMultiple ? '[]' : 'null' }};
        $wire.set($refs.select.name, model);
    });

    // Watch for model changes from Livewire and update Select2 accordingly
    $watch('model', (value) => {
        $($refs.select).val(value).trigger('change');
    });

    // Set initial value on load
    $nextTick(() => {
        $($refs.select).val(model).trigger('change');
    });
" wire:ignore>
    <select x-ref="select" {{ $attributes->merge(['class' => 'custom-select2']) }}>
        {{ $slot }}
    </select>
</div>
