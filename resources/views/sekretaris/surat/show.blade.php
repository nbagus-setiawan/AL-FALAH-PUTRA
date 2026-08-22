@extends('layouts.sekretaris')

@section('title', $surat->nomor_surat)
@section('subtitle', $surat->perihal)

@section('header-actions')
    <a href="{{ route('sekretaris.surat.cetak', $surat) }}" target="_blank"
       class="inline-flex items-center gap-2 rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
        Cetak PDF
    </a>
@endsection

@section('content')
@php
    $approvalColor = ['Pending' => 'amber', 'Approved' => 'emerald', 'Rejected' => 'rose'];
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-base font-semibold text-ink-900">Detail Surat</h2>
            <x-badge :color="$approvalColor[$surat->status_approval] ?? 'slate'">{{ $surat->status_approval }}</x-badge>
        </div>
        <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
            <div><dt class="text-slate-500">Nomor Surat</dt><dd class="font-medium text-ink-900">{{ $surat->nomor_surat }}</dd></div>
            <div><dt class="text-slate-500">Jenis Surat</dt><dd class="text-ink-900">{{ $surat->jenisSurat->nama ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Tanggal</dt><dd class="text-ink-900">{{ $surat->tanggal->format('d-m-Y') }}</dd></div>
            <div><dt class="text-slate-500">Arah</dt><dd class="text-ink-900">{{ $surat->arah }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-slate-500">Perihal</dt><dd class="text-ink-900">{{ $surat->perihal }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-slate-500">Tujuan / Pengirim</dt><dd class="text-ink-900">{{ $surat->tujuan_pengirim }}</dd></div>
            @if ($surat->santri)
                <div class="sm:col-span-2">
                    <dt class="text-slate-500">Santri Terkait</dt>
                    <dd><a href="{{ route('sekretaris.santri.show', $surat->santri) }}" class="text-forest-700 hover:underline">{{ $surat->santri->nama_lengkap }} ({{ $surat->santri->nis }})</a></dd>
                </div>
            @endif
            <div><dt class="text-slate-500">Dibuat Oleh</dt><dd class="text-ink-900">{{ $surat->pembuat->name ?? '—' }}</dd></div>
            @if ($surat->status_approval !== 'Pending')
                <div><dt class="text-slate-500">Diproses Oleh</dt><dd class="text-ink-900">{{ $surat->penyetuju->name ?? '—' }} · {{ $surat->disetujui_pada?->format('d-m-Y H:i') }}</dd></div>
            @endif
            @if ($surat->catatan_penolakan)
                <div class="sm:col-span-2"><dt class="text-slate-500">Catatan Penolakan</dt><dd class="text-rose-600">{{ $surat->catatan_penolakan }}</dd></div>
            @endif
        </dl>

        @if ($surat->isi_lengkap)
            <div class="mt-6 border-t border-ink-900/5 pt-6">
                <h3 class="text-sm font-medium text-slate-500">Isi Surat</h3>
                <p class="mt-2 whitespace-pre-line text-sm text-ink-900">{{ $surat->isi_lengkap }}</p>
            </div>
        @endif
    </div>

    <div class="space-y-6">
        @if ($surat->lampiran_path)
            <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
                <h2 class="font-display text-base font-semibold text-ink-900">Lampiran</h2>
                <a href="{{ route('sekretaris.surat.lampiran', $surat) }}" target="_blank"
                   class="mt-3 inline-flex items-center gap-2 rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-ink-900 hover:bg-ink-900/5">
                    Buka Lampiran
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
