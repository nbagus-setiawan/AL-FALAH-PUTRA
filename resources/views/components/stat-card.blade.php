@props([
    'label',
    'value',
    'icon' => 'chart',
    'accent' => 'forest', // forest | gold | rose | amber
    'sublabel' => null,
])

@php
    $accentMap = [
        'forest' => ['text' => 'text-forest-700', 'chip' => 'bg-forest-50'],
        'gold' => ['text' => 'text-gold-600', 'chip' => 'bg-gold-100/60'],
        'rose' => ['text' => 'text-rose-600', 'chip' => 'bg-rose-50'],
        'amber' => ['text' => 'text-amber-600', 'chip' => 'bg-amber-50'],
    ];
    $a = $accentMap[$accent] ?? $accentMap['forest'];
@endphp

<div class="rounded-2xl border border-ink-900/5 bg-white p-5 shadow-sm">
    <div class="flex items-center justify-between">
        <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $a['chip'] }}">
            <x-icon :name="$icon" class="h-5 w-5 {{ $a['text'] }}" />
        </span>
    </div>
    <p class="mt-3 font-display text-2xl font-semibold text-ink-900">{{ $value }}</p>
    @if ($sublabel)
        <p class="mt-1 text-xs text-slate-500">{{ $sublabel }}</p>
    @endif
</div>
