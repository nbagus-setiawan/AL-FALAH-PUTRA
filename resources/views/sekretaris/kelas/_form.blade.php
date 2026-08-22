@php $k = $kelas ?? null; @endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <x-field label="Tingkat" name="tingkat_id" required>
        <x-select name="tingkat_id" :value="$k?->tingkat_id" :options="$tingkatList->pluck('nama', 'id')" />
    </x-field>
    <x-field label="Nama Kelas" name="nama" required hint="Contoh: Ula 1A">
        <x-input name="nama" :value="$k?->nama" />
    </x-field>
    <x-field label="Tahun Ajaran" name="tahun_ajaran" required hint="Contoh: 2026/2027">
        <x-input name="tahun_ajaran" :value="$k?->tahun_ajaran" />
    </x-field>
</div>
