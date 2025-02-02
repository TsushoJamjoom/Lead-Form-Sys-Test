<tr>
    <td>
        <input type="text" class="form-control" placeholder="Model" maxlength="20"
            wire:model="$parent.fields.{{ $index }}.model" @if ($key !== 0) disabled @endif
            required>
    </td>
    <td>
        <input type="number" class="form-control" min="1" placeholder="QTY" maxlength="20"
            wire:model="$parent.fields.{{ $index }}.qty" @if ($key !== 0) disabled @endif
            required>
    </td>
    <td>
        <select class="form-select" wire:model="$parent.fields.{{ $index }}.sales_month"
            @if ($key !== 0) disabled @endif required>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>
    </td>
    <td>
        <textarea class="form-control" rows="2" placeholder="Comment"
            wire:model="$parent.fields.{{ $index }}.comment" @if ($key !== 0) disabled @endif>
        </textarea>
    </td>
    <td class="text-end">
        @if ($key == 0)
            <button type="button" class="btn btn-danger"
                wire:click="$parent.removeSalesLeadField({{ $index }})">X</button>
        @endif
    </td>
</tr>
