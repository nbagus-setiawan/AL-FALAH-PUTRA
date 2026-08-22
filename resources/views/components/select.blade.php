@props(['name', 'options' => [], 'value' => null, 'placeholder' => '— Pilih —'])

<select
    name="{{ $name }}"
    id="{{ $name }}"
    {{ $attributes->merge(['class' => 'block w-full rounded-xl border border-ink-900/10 bg-white px-3.5 py-2.5 text-sm text-ink-900 shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20']) }}
>
    @if ($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif
    @foreach ($options as $optValue => $optLabel)
        <option value="{{ $optValue }}" @selected((string) old($name, $value) === (string) $optValue)>
            {{ $optLabel }}
        </option>
    @endforeach
</select>
