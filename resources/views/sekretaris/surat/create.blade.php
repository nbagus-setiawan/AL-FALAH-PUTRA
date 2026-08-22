@extends('layouts.sekretaris')

@section('title', 'Buat Surat')
@section('subtitle', 'Generator surat keluar — nomor surat dibuat otomatis.')

@section('content')
<form method="POST" action="{{ route('sekretaris.surat.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-field label="Jenis Surat" name="jenis_surat_id" required>
                <x-select name="jenis_surat_id" :options="$jenisSurats->pluck('nama', 'id')" />
            </x-field>
            <x-field label="Tanggal" name="tanggal" required>
                <x-input type="date" name="tanggal" :value="now()->format('Y-m-d')" />
            </x-field>
            <x-field label="Perihal" name="perihal" required class="sm:col-span-2">
                <x-input name="perihal" />
            </x-field>
            <x-field label="Tujuan / Pengirim" name="tujuan_pengirim" required class="sm:col-span-2">
                <x-input name="tujuan_pengirim" />
            </x-field>
            <x-field label="Santri Terkait" name="santri_id" hint="Opsional — isi jika surat ini terkait santri tertentu." class="sm:col-span-2">
                <x-select name="santri_id" :options="$santris->mapWithKeys(fn ($s) => [$s->id => \"{$s->nama_lengkap} ({$s->nis})\"])" />
            </x-field>
            <x-field label="Isi Surat" name="isi_lengkap" required class="sm:col-span-2">
                <x-textarea name="isi_lengkap" :rows="8" />
            </x-field>
            <x-field label="Lampiran" name="lampiran" hint="PDF/JPG/PNG, maksimal 5MB." class="sm:col-span-2">
                <input type="file" name="lampiran" accept=".pdf,.jpg,.jpeg,.png"
                       class="block w-full rounded-xl border border-ink-900/10 bg-white px-3.5 py-2.5 text-sm text-ink-900 shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-forest-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-forest-700" />
            </x-field>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('sekretaris.surat.index') }}" class="rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-ink-900/5">
            Batal
        </a>
        <button type="submit" class="rounded-xl bg-forest-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
            Buat Surat
        </button>
    </div>
</form>
@endsection
