@props([
    'label',
    'value',
    'icon' => 'chart',
    'accent' => 'forest',
    'sublabel' => null,
])

@php
    $accents = [
        'forest' => 'bg-forest-50 text-forest-700',
        'gold' => 'bg-gold-100/60 text-gold-600',
        'rose' => 'bg-rose-50 text-rose-600',
    ];
    $chip = $accents[$accent] ?? $accents['forest'];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm']) }}>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-sm text-slate-500">{{ $label }}</p>
            <p class="mt-2 break-words font-display text-xl font-semibold leading-tight text-ink-900 sm:text-2xl">{{ $value }}</p>
            @if ($sublabel)
                <p class="mt-1 text-xs text-slate-500">{{ $sublabel }}</p>
            @endif
        </div>
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $chip }}">
            <x-icon :name="$icon" class="h-5 w-5" />
        </span>
    </div>
</div>