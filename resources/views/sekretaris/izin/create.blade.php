@extends('layouts.sekretaris')

@section('title', 'Ajukan Izin')
@section('subtitle', 'Pengajuan izin akan menunggu persetujuan Ketua Umum.')

@section('content')
<form method="POST" action="{{ route('sekretaris.izin.store') }}" class="space-y-6">
    @csrf

    <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-field label="Santri" name="santri_id" required class="sm:col-span-2">
                <x-select name="santri_id" :options="$santris->mapWithKeys(fn ($s) => [$s->id => \"{$s->nama_lengkap} ({$s->nis})\"])" />
            </x-field>
            <x-field label="Jenis Izin" name="jenis_izin" required>
                <x-select name="jenis_izin" :options="['Sakit' => 'Sakit', 'Kepentingan' => 'Kepentingan']" />
            </x-field>
            <div></div>
            <x-field label="Tanggal Keluar" name="tanggal_keluar" required>
                <x-input type="date" name="tanggal_keluar" />
            </x-field>
            <x-field label="Rencana Kembali" name="rencana_kembali" required>
                <x-input type="date" name="rencana_kembali" />
            </x-field>
            <x-field label="Alasan" name="alasan" required class="sm:col-span-2">
                <x-textarea name="alasan" :rows="3" />
            </x-field>
            <x-field label="Penjemput" name="penjemput" required>
                <x-input name="penjemput" />
            </x-field>
            <x-field label="Kontak Penjemput" name="kontak_penjemput" required>
                <x-input name="kontak_penjemput" />
            </x-field>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('sekretaris.izin.index') }}" class="rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-ink-900/5">
            Batal
        </a>
        <button type="submit" class="rounded-xl bg-forest-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
            Ajukan Izin
        </button>
    </div>
</form>
@endsection
