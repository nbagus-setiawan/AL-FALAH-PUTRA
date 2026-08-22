@props(['color' => 'slate'])

@php
    $map = [
        'emerald' => 'bg-emerald-50 text-emerald-700',
        'amber' => 'bg-amber-50 text-amber-700',
        'rose' => 'bg-rose-50 text-rose-700',
        'slate' => 'bg-slate-100 text-slate-600',
        'gold' => 'bg-gold-100/60 text-gold-600',
        'forest' => 'bg-forest-50 text-forest-700',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex rounded-full px-2.5 py-1 text-xs font-medium '.($map[$color] ?? $map['slate'])]) }}>
    {{ $slot }}
</span>
