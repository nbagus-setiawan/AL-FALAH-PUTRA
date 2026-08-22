@extends('layouts.sekretaris')

@section('title', 'Catat Pelanggaran')

@section('content')
<form method="POST" action="{{ route('sekretaris.pelanggaran.store') }}" class="space-y-6">
    @csrf

    <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-field label="Santri" name="santri_id" required class="sm:col-span-2">
                <x-select name="santri_id" :options="$santris->mapWithKeys(fn ($s) => [$s->id => \"{$s->nama_lengkap} ({$s->nis})\"])" />
            </x-field>
            <x-field label="Jenis Pelanggaran" name="jenis_pelanggaran" required class="sm:col-span-2">
                <x-input name="jenis_pelanggaran" />
            </x-field>
            <x-field label="Kategori" name="kategori" required
                     :hint="'Bobot poin: Ringan '.$bobotPoin['Ringan'].', Sedang '.$bobotPoin['Sedang'].', Berat '.$bobotPoin['Berat'].'.'">
                <x-select name="kategori" :options="['Ringan' => 'Ringan ('.$bobotPoin['Ringan'].' poin)', 'Sedang' => 'Sedang ('.$bobotPoin['Sedang'].' poin)', 'Berat' => 'Berat ('.$bobotPoin['Berat'].' poin)']" />
            </x-field>
            <x-field label="Tanggal" name="tanggal" required>
                <x-input type="date" name="tanggal" :value="now()->format('Y-m-d')" />
            </x-field>
            <x-field label="Sanksi" name="sanksi" class="sm:col-span-2">
                <x-textarea name="sanksi" :rows="2" />
            </x-field>
            <x-field label="Catatan" name="catatan" class="sm:col-span-2">
                <x-textarea name="catatan" :rows="2" />
            </x-field>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('sekretaris.pelanggaran.index') }}" class="rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-ink-900/5">Batal</a>
        <button type="submit" class="rounded-xl bg-forest-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">Simpan</button>
    </div>
</form>
@endsection
