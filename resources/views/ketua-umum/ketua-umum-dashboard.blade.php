@extends('layouts.ketua-umum')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan lintas modul untuk persetujuan & pengawasan.')

@section('content')
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card label="Santri Aktif" :value="$total_santri_aktif" icon="users" accent="forest" />
    <x-stat-card label="Izin Menunggu Persetujuan" :value="$izin_pending" icon="calendar" accent="amber" sublabel="Perlu ditinjau" />
    <x-stat-card label="Izin Terlambat" :value="$izin_terlambat" icon="alert-triangle" accent="rose" />
    <x-stat-card label="Surat Keluar Bulan Ini" :value="$surat_keluar_bulan_ini" icon="mail" accent="gold" />
</div>

<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-5">
    <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm lg:col-span-2">
        <h2 class="font-display text-base font-semibold text-ink-900">RAPB {{ $tahun_anggaran_aktif->nama ?? '—' }}</h2>
        <p class="mt-1 text-sm text-slate-500">Realisasi terhadap rencana anggaran tahun berjalan.</p>

        @php $persen = $rapb_rencana > 0 ? min(100, round($rapb_realisasi / $rapb_rencana * 100)) : 0; @endphp

        <div class="mt-6 flex items-end justify-between gap-2">
            <div>
                <p class="text-xs text-slate-500">Realisasi</p>
                <p class="font-display text-xl font-semibold text-ink-900">Rp{{ number_format($rapb_realisasi, 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500">Rencana</p>
                <p class="text-sm font-medium text-slate-600">Rp{{ number_format($rapb_rencana, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="mt-3 h-2.5 rounded-full bg-cream-100">
            <div class="h-2.5 rounded-full bg-gold-500" style="width: {{ $persen }}%"></div>
        </div>
        <p class="mt-2 text-xs text-slate-500">{{ $persen }}% dari rencana terserap</p>
    </div>

    <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm lg:col-span-3">
        <h2 class="font-display text-base font-semibold text-ink-900">Santri Poin Kedisiplinan Tertinggi</h2>
        <p class="mt-1 text-sm text-slate-500">5 santri dengan akumulasi poin pelanggaran tertinggi saat ini.</p>

        <div class="mt-5 divide-y divide-ink-900/5">
            @php
                $warnaMap = [
                    'Hijau' => 'bg-emerald-50 text-emerald-700',
                    'Kuning' => 'bg-amber-50 text-amber-700',
                    'Merah' => 'bg-rose-50 text-rose-700',
                ];
            @endphp
            @forelse ($santri_poin_tertinggi as $santri)
                <div class="flex items-center justify-between py-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-ink-900">{{ $santri->nama_lengkap }}</p>
                        <p class="text-xs text-slate-500">NIS {{ $santri->nis }}</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-3">
                        <span class="text-sm font-semibold text-ink-900">{{ $santri->total_poin }} poin</span>
                        <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $warnaMap[$santri->warna_poin] ?? '' }}">
                            {{ $santri->warna_poin }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="py-3 text-sm text-slate-500">Belum ada catatan pelanggaran.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
