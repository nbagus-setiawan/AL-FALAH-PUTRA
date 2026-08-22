@props(['name', 'value' => null, 'rows' => 4])

<textarea
    name="{{ $name }}"
    id="{{ $name }}"
    rows="{{ $rows }}"
    {{ $attributes->merge(['class' => 'block w-full rounded-xl border border-ink-900/10 bg-white px-3.5 py-2.5 text-sm text-ink-900 shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20']) }}
>{{ old($name, $value) }}</textarea>
