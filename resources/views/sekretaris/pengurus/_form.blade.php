@php $p = $pengurus ?? null; @endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <x-field label="Nama" name="nama" required>
        <x-input name="nama" :value="$p?->nama" />
    </x-field>
    <x-field label="NIP Internal" name="nip_internal">
        <x-input name="nip_internal" :value="$p?->nip_internal" />
    </x-field>
    <x-field label="Jabatan" name="jabatan" required>
        <x-input name="jabatan" :value="$p?->jabatan" />
    </x-field>
    <x-field label="Periode" name="periode" required hint="Contoh: 2026/2027">
        <x-input name="periode" :value="$p?->periode" />
    </x-field>
    <x-field label="Kontak" name="kontak">
        <x-input name="kontak" :value="$p?->kontak" />
    </x-field>
    <x-field label="Status" name="is_active">
        <x-select name="is_active" :value="$p ? (int) $p->is_active : 1" :placeholder="null" :options="[1 => 'Aktif', 0 => 'Nonaktif']" />
    </x-field>
</div>
