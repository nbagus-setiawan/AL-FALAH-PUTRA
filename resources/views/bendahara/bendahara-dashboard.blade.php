@extends('layouts.bendahara')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan RAPB tahun anggaran berjalan.')

@section('content')
<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <x-stat-card label="Tahun Anggaran Aktif" :value="$tahunAktif->nama ?? '—'" icon="calendar" accent="forest" />
    <x-stat-card label="Total Rencana" :value="'Rp'.number_format($totalRencana, 0, ',', '.')" icon="banknote" accent="gold" />
    <x-stat-card label="Total Realisasi" :value="'Rp'.number_format($totalRealisasi, 0, ',', '.')" icon="trending-up" accent="rose" />
</div>

<div class="mt-6 rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
    <h2 class="font-display text-base font-semibold text-ink-900">Riwayat Tahun Anggaran</h2>
    <p class="mt-1 text-sm text-slate-500">5 tahun anggaran terakhir.</p>

    <div class="mt-5 overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="pb-3 font-medium">Tahun Anggaran</th>
                    <th class="pb-3 font-medium">Periode</th>
                    <th class="pb-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @php
                    $statusMap = [
                        'Pending' => 'bg-amber-50 text-amber-700',
                        'Approved' => 'bg-emerald-50 text-emerald-700',
                        'Rejected' => 'bg-rose-50 text-rose-700',
                    ];
                @endphp
                @forelse ($riwayatTahunAnggaran as $tahun)
                    <tr>
                        <td class="py-3 font-medium text-ink-900">
                            <a href="{{ route('bendahara.tahun-anggaran.show', $tahun) }}" class="hover:text-forest-700">
                                {{ $tahun->nama }}
                            </a>
                        </td>
                        <td class="py-3 text-slate-600">
                            {{ $tahun->tanggal_mulai->format('d M Y') }} – {{ $tahun->tanggal_selesai->format('d M Y') }}
                        </td>
                        <td class="py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusMap[$tahun->status_approval] ?? '' }}">
                                {{ $tahun->status_approval }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-4 text-sm text-slate-500">Belum ada tahun anggaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
