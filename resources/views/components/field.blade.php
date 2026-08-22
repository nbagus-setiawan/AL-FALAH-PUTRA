@props(['label', 'name', 'required' => false, 'hint' => null])
<div {{ $attributes }}>
    <label for="{{ $name }}" class="block text-sm font-medium text-ink-900">
        {{ $label }}
        @if ($required)
            <span class="text-rose-600">*</span>
        @endif
    </label>
    <div class="mt-1.5">
        {{ $slot }}
    </div>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>
