@php $t = $tingkat ?? null; @endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <x-field label="Nama Tingkat" name="nama" required hint="Contoh: Ula">
        <x-input name="nama" :value="$t?->nama" />
    </x-field>
    <x-field label="Urutan" name="urutan" required hint="Menentukan urutan naik tingkat, harus unik">
        <x-input type="number" name="urutan" :value="$t?->urutan" min="1" />
    </x-field>
</div>
