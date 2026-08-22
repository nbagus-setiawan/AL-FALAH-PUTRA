@php $k = $kategori ?? null; @endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <x-field label="Nama Kategori" name="nama" required>
        <x-input name="nama" :value="$k?->nama" />
    </x-field>
    <x-field label="Jenis" name="jenis" required>
        <x-select name="jenis" :value="$k?->jenis" :options="['Pemasukan' => 'Pemasukan', 'Pengeluaran' => 'Pengeluaran']" />
    </x-field>
</div>
