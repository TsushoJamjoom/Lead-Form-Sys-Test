<div x-data="{
    model: @entangle($attributes->wire('model')),
}" x-init="
    $($refs.dateRangeFilter).daterangepicker({
        opens: 'left',
        locale: {format: 'DD/MM/YYYY'}
    },function(start, end, label) {
        Livewire.dispatch('dateRangeFilter', {
            startDate: start.format('YYYY-MM-DD'),
            endDate: end.format('YYYY-MM-DD')
        });
    });" wire:ignore>
    <input type="text" x-ref="dateRangeFilter" {{ $attributes->merge(['class' => 'custom-date-range']) }} />
</div>
