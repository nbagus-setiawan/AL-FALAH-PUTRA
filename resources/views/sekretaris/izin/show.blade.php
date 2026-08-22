@extends('layouts.sekretaris')

@section('title', 'Detail Izin')
@section('subtitle', $izin->santri->nama_lengkap)

@section('content')
@php
    $statusColor = [
        'Pending' => 'amber', 'Approved' => 'emerald', 'Rejected' => 'rose',
        'Sedang Izin' => 'forest', 'Sudah Kembali' => 'slate', 'Terlambat' => 'rose',
    ];
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-base font-semibold text-ink-900">Detail Pengajuan</h2>
            <x-badge :color="$statusColor[$izin->status] ?? 'slate'">{{ $izin->status }}</x-badge>
        </div>
        <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
            <div><dt class="text-slate-500">Santri</dt>
                <dd><a href="{{ route('sekretaris.santri.show', $izin->santri) }}" class="font-medium text-forest-700 hover:underline">{{ $izin->santri->nama_lengkap }}</a></dd>
            </div>
            <div><dt class="text-slate-500">Jenis Izin</dt><dd class="text-ink-900">{{ $izin->jenis_izin }}</dd></div>
            <div><dt class="text-slate-500">Tanggal Keluar</dt><dd class="text-ink-900">{{ $izin->tanggal_keluar->format('d-m-Y') }}</dd></div>
            <div><dt class="text-slate-500">Rencana Kembali</dt><dd class="text-ink-900">{{ $izin->rencana_kembali->format('d-m-Y') }}</dd></div>
            @if ($izin->tanggal_kembali_aktual)
                <div><dt class="text-slate-500">Kembali Aktual</dt><dd class="text-ink-900">{{ $izin->tanggal_kembali_aktual->format('d-m-Y') }}</dd></div>
            @endif
            <div class="sm:col-span-2"><dt class="text-slate-500">Alasan</dt><dd class="text-ink-900">{{ $izin->alasan }}</dd></div>
            <div><dt class="text-slate-500">Penjemput</dt><dd class="text-ink-900">{{ $izin->penjemput }}</dd></div>
            <div><dt class="text-slate-500">Kontak Penjemput</dt><dd class="text-ink-900">{{ $izin->kontak_penjemput }}</dd></div>
            <div><dt class="text-slate-500">Diajukan Oleh</dt><dd class="text-ink-900">{{ $izin->pengaju->name ?? '—' }}</dd></div>
            @if ($izin->status !== 'Pending')
                <div><dt class="text-slate-500">Diproses Oleh</dt><dd class="text-ink-900">{{ $izin->penyetuju->name ?? '—' }} · {{ $izin->disetujui_pada?->format('d-m-Y H:i') }}</dd></div>
            @endif
            @if ($izin->catatan_penolakan)
                <div class="sm:col-span-2"><dt class="text-slate-500">Catatan Penolakan</dt><dd class="text-rose-600">{{ $izin->catatan_penolakan }}</dd></div>
            @endif
        </dl>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
            <h2 class="font-display text-base font-semibold text-ink-900">Aksi</h2>
            <div class="mt-4 space-y-3">
                @if (in_array($izin->status, ['Sedang Izin', 'Terlambat']))
                    <form method="POST" action="{{ route('sekretaris.izin.tandai-kembali', $izin) }}"
                          onsubmit="return confirm('Tandai {{ $izin->santri->nama_lengkap }} sudah kembali?')">
                        @csrf
                        <button type="submit" class="w-full rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest-700">
                            Tandai Sudah Kembali
                        </button>
                    </form>
                @endif

                @if (! in_array($izin->status, ['Pending', 'Rejected']) && ! $izin->surat_id)
                    <form method="POST" action="{{ route('sekretaris.izin.generate-surat', $izin) }}">
                        @csrf
                        <button type="submit" class="w-full rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-ink-900 hover:bg-ink-900/5">
                            Buat Surat Izin
                        </button>
                    </form>
                @endif

                @if ($izin->surat)
                    <a href="{{ route('sekretaris.surat.show', $izin->surat) }}"
                       class="block rounded-xl border border-ink-900/10 px-4 py-2.5 text-center text-sm font-medium text-forest-700 hover:bg-ink-900/5">
                        Lihat Surat Izin
                    </a>
                @endif

                @if ($izin->status === 'Pending')
                    <form method="POST" action="{{ route('sekretaris.izin.destroy', $izin) }}"
                          onsubmit="return confirm('Batalkan pengajuan izin ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-medium text-rose-600 hover:bg-rose-50">
                            Batalkan Pengajuan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
