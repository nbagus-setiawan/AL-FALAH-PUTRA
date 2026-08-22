@extends('layouts.sekretaris')

@section('title', 'Detail Pelanggaran')
@section('subtitle', $pelanggaran->santri->nama_lengkap ?? '')

@section('header-actions')
    <a href="{{ route('sekretaris.pelanggaran.edit', $pelanggaran) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-ink-900 hover:bg-ink-900/5">
        Ubah
    </a>
@endsection

@section('content')
@php $kategoriColor = ['Ringan' => 'emerald', 'Sedang' => 'amber', 'Berat' => 'rose']; @endphp

<div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <h2 class="font-display text-base font-semibold text-ink-900">{{ $pelanggaran->jenis_pelanggaran }}</h2>
        <x-badge :color="$kategoriColor[$pelanggaran->kategori] ?? 'slate'">{{ $pelanggaran->kategori }} · {{ $pelanggaran->poin }} poin</x-badge>
    </div>
    <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
        <div><dt class="text-slate-500">Santri</dt>
            <dd><a href="{{ route('sekretaris.santri.show', $pelanggaran->santri) }}" class="font-medium text-forest-700 hover:underline">{{ $pelanggaran->santri->nama_lengkap }}</a></dd>
        </div>
        <div><dt class="text-slate-500">Tanggal</dt><dd class="text-ink-900">{{ $pelanggaran->tanggal->format('d-m-Y') }}</dd></div>
        <div><dt class="text-slate-500">Dicatat Oleh</dt><dd class="text-ink-900">{{ $pelanggaran->pencatat->name ?? '—' }}</dd></div>
        <div class="sm:col-span-2"><dt class="text-slate-500">Sanksi</dt><dd class="text-ink-900">{{ $pelanggaran->sanksi ?? '—' }}</dd></div>
        <div class="sm:col-span-2"><dt class="text-slate-500">Catatan</dt><dd class="text-ink-900">{{ $pelanggaran->catatan ?? '—' }}</dd></div>
    </dl>
</div>
@endsection
