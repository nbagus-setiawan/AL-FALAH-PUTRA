@php $j = $jenisSurat ?? null; @endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <x-field label="Nama Jenis Surat" name="nama" required hint="Contoh: Surat Undangan">
        <x-input name="nama" :value="$j?->nama" />
    </x-field>
    <x-field label="Kode" name="kode" required hint="Contoh: UND — dipakai dalam format nomor surat">
        <x-input name="kode" :value="$j?->kode" />
    </x-field>
</div>
