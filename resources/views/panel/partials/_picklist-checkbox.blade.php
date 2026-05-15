@php
    $picklistId = $id ?? 'picklist-' . uniqid();
    $multiple = (bool) ($multiple ?? false);
    $max = (int) ($max ?? 5);
    $placeholder = $placeholder ?? ($multiple ? 'Seleccione servicio' : 'Seleccione paquete');
    $name = $name ?? ($multiple ? 'servicio_ids' : 'paquete_id');
    $selectedRaw = $selected ?? [];
    $selectedValues = array_values(array_filter(array_map('strval', is_array($selectedRaw) ? $selectedRaw : [$selectedRaw]), static fn (string $v): bool => $v !== ''));
    $items = $items ?? [];
    $role = $role ?? null;
    $inputName = $multiple ? $name . '[]' : $name;
@endphp

<div
    class="picklist-checkbox"
    id="{{ $picklistId }}"
    data-picklist
    data-picklist-name="{{ $inputName }}"
    @if ($multiple) data-picklist-multiple data-picklist-max="{{ $max }}" @else data-picklist-single @endif
    @if ($role) data-role="{{ $role }}" @endif
>
    <button
        type="button"
        id="{{ $picklistId }}-trigger"
        class="picklist-checkbox__trigger form-select text-start position-relative"
        aria-haspopup="listbox"
        aria-expanded="false"
        aria-controls="{{ $picklistId }}-panel"
    >
        <span class="picklist-checkbox__label {{ $selectedValues === [] ? '' : 'is-selected' }}" data-placeholder="{{ $placeholder }}">{{ $placeholder }}</span>
        <i class="fas fa-chevron-down picklist-checkbox__chevron" aria-hidden="true"></i>
    </button>

    <div id="{{ $picklistId }}-panel" class="picklist-checkbox__panel" role="listbox" hidden>
        <div class="picklist-checkbox__panel-inner">
            @foreach ($items as $item)
                @php
                    $val = (string) ($item['value'] ?? $item['id'] ?? '');
                    $lab = (string) ($item['label'] ?? $item['nombre'] ?? '');
                    $checked = in_array($val, $selectedValues, true);
                @endphp
                @if ($val !== '')
                    <label class="picklist-checkbox__option">
                        <input type="checkbox" class="form-check-input" value="{{ $val }}" @checked($checked)>
                        <span class="picklist-checkbox__option-text">{{ $lab }}</span>
                    </label>
                @endif
            @endforeach
        </div>
    </div>

    <div class="picklist-checkbox__inputs" aria-hidden="true">
        @if ($multiple)
            @foreach ($selectedValues as $sv)
                <input type="hidden" name="{{ $name }}[]" value="{{ $sv }}">
            @endforeach
        @elseif ($selectedValues !== [])
            <input type="hidden" name="{{ $name }}" value="{{ $selectedValues[0] }}">
        @endif
    </div>
</div>
