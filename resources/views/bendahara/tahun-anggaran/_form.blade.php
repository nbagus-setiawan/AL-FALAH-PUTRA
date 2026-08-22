@php $t = $tahunAnggaran ?? null; @endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <x-field label="Nama Tahun Anggaran" name="nama" required hint="Maks 20 karakter, contoh: 2026/2027" class="sm:col-span-2">
        <x-input name="nama" :value="$t?->nama" maxlength="20" />
    </x-field>
    <x-field label="Tanggal Mulai" name="tanggal_mulai" required>
        <x-input type="date" name="tanggal_mulai" :value="$t?->tanggal_mulai?->format('Y-m-d')" />
    </x-field>
    <x-field label="Tanggal Selesai" name="tanggal_selesai" required>
        <x-input type="date" name="tanggal_selesai" :value="$t?->tanggal_selesai?->format('Y-m-d')" />
    </x-field>
    <div class="sm:col-span-2">
        <label class="inline-flex items-center gap-2 text-sm text-ink-900">
            <input type="checkbox" name="is_active" value="1" @checked($t?->is_active) class="rounded border-ink-900/20 text-forest-700 focus:ring-forest-600/30" />
            Jadikan tahun anggaran aktif
        </label>
        <p class="mt-1 text-xs text-slate-500">Menonaktifkan tahun anggaran aktif lain yang sedang berjalan.</p>
    </div>
</div>
