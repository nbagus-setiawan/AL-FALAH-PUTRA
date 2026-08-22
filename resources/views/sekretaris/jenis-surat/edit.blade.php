@extends('layouts.sekretaris')

@section('title', 'Ubah Jenis Surat')
@section('subtitle', $jenisSurat->nama)

@section('content')
<form method="POST" action="{{ route('sekretaris.jenis-surat.update', $jenisSurat) }}" class="space-y-6">
    @csrf @method('PUT')
    <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        @include('sekretaris.jenis-surat._form', ['jenisSurat' => $jenisSurat])
    </div>
    <div class="flex justify-end gap-3">
        <a href="{{ route('sekretaris.jenis-surat.index') }}" class="rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-ink-900/5">Batal</a>
        <button type="submit" class="rounded-xl bg-forest-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">Simpan Perubahan</button>
    </div>
</form>
@endsection
