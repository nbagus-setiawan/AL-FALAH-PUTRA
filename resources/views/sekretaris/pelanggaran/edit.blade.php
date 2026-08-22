@extends('layouts.sekretaris')

@section('title', 'Ubah Catatan Pelanggaran')
@section('subtitle', $pelanggaran->santri->nama_lengkap ?? '')

@section('content')
<form method="POST" action="{{ route('sekretaris.pelanggaran.update', $pelanggaran) }}" class="space-y-6">
    @csrf @method('PUT')

    <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-field label="Jenis Pelanggaran" name="jenis_pelanggaran" required class="sm:col-span-2">
                <x-input name="jenis_pelanggaran" :value="$pelanggaran->jenis_pelanggaran" />
            </x-field>
            <x-field label="Kategori" name="kategori" required
                     :hint="'Bobot poin: Ringan '.$bobotPoin['Ringan'].', Sedang '.$bobotPoin['Sedang'].', Berat '.$bobotPoin['Berat'].'.'">
                <x-select name="kategori" :value="$pelanggaran->kategori" :options="['Ringan' => 'Ringan ('.$bobotPoin['Ringan'].' poin)', 'Sedang' => 'Sedang ('.$bobotPoin['Sedang'].' poin)', 'Berat' => 'Berat ('.$bobotPoin['Berat'].' poin)']" />
            </x-field>
            <x-field label="Tanggal" name="tanggal" required>
                <x-input type="date" name="tanggal" :value="$pelanggaran->tanggal->format('Y-m-d')" />
            </x-field>
            <x-field label="Sanksi" name="sanksi" class="sm:col-span-2">
                <x-textarea name="sanksi" :value="$pelanggaran->sanksi" :rows="2" />
            </x-field>
            <x-field label="Catatan" name="catatan" class="sm:col-span-2">
                <x-textarea name="catatan" :value="$pelanggaran->catatan" :rows="2" />
            </x-field>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('sekretaris.pelanggaran.show', $pelanggaran) }}" class="rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-ink-900/5">Batal</a>
        <button type="submit" class="rounded-xl bg-forest-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">Simpan Perubahan</button>
    </div>
</form>
@endsection
