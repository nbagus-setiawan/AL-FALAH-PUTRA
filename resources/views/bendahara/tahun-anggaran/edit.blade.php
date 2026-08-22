@extends('layouts.bendahara')

@section('title', 'Ubah Tahun Anggaran')
@section('subtitle', $tahunAnggaran->nama)

@section('content')
@if ($tahunAnggaran->isApprovalLocked())
    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
        RAPB ini sudah disetujui Ketua Umum dan terkunci dari perubahan. Ajukan ulang dari halaman detail jika ingin merevisi.
    </div>
@endif

<form method="POST" action="{{ route('bendahara.tahun-anggaran.update', $tahunAnggaran) }}" class="space-y-6">
    @csrf @method('PUT')
    <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        @include('bendahara.tahun-anggaran._form', ['tahunAnggaran' => $tahunAnggaran])
    </div>
    <div class="flex justify-end gap-3">
        <a href="{{ route('bendahara.tahun-anggaran.show', $tahunAnggaran) }}" class="rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-ink-900/5">Batal</a>
        <button type="submit" @disabled($tahunAnggaran->isApprovalLocked())
                class="rounded-xl bg-forest-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700 disabled:cursor-not-allowed disabled:opacity-50">
            Simpan Perubahan
        </button>
    </div>
</form>
@endsection
