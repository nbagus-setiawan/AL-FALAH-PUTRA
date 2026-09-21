@extends('layouts.bendahara')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan RAPB tahun anggaran aktif.')

@section('content')
@php $approvalColor = ['Pending' => 'amber', 'Approved' => 'emerald', 'Rejected' => 'rose']; @endphp

@if ($tahunAktif)
    <div class="flex items-center gap-3">
        <p class="font-display text-base font-semibold text-ink-900">{{ $tahunAktif->nama }}</p>
        <x-badge :color="$approvalColor[$tahunAktif->status_approval] ?? 'slate'">{{ $tahunAktif->status_approval }}</x-badge>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-stat-card label="Total Rencana" :value="'Rp'.number_format($totalRencana, 0, ',', '.')" icon="banknote" accent="forest" />
        <x-stat-card label="Total Realisasi" :value="'Rp'.number_format($totalRealisasi, 0, ',', '.')" icon="trending-up" accent="gold" />
        <x-stat-card label="Sisa Anggaran" :value="'Rp'.number_format(max(0, $totalRencana - $totalRealisasi), 0, ',', '.')" icon="chart" accent="rose" />
    </div>
@else
    <div class="rounded-2xl border border-ink-900/5 bg-white p-8 text-center shadow-sm">
        <p class="text-sm text-slate-500">Belum ada tahun anggaran aktif.</p>
        <a href="{{ route('bendahara.tahun-anggaran.create') }}" class="mt-3 inline-block text-sm font-medium text-forest-700 hover:underline">Buat Tahun Anggaran</a>
    </div>
@endif

<div class="mt-6 rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="border-b border-ink-900/5 px-6 py-4">
        <h2 class="font-display text-base font-semibold text-ink-900">Riwayat Tahun Anggaran</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Tahun Anggaran</th>
                    <th class="px-6 py-3 font-medium">Periode</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @forelse ($riwayatTahunAnggaran as $tahun)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3">
                            <a href="{{ route('bendahara.tahun-anggaran.show', $tahun) }}" class="font-medium text-ink-900 hover:text-forest-700">{{ $tahun->nama }}</a>
                        </td>
                        <td class="px-6 py-3 text-slate-600">{{ $tahun->tanggal_mulai->format('d M Y') }} – {{ $tahun->tanggal_selesai->format('d M Y') }}</td>
                        <td class="px-6 py-3"><x-badge :color="$approvalColor[$tahun->status_approval] ?? 'slate'">{{ $tahun->status_approval }}</x-badge></td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada tahun anggaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection