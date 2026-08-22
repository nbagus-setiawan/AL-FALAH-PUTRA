@extends('layouts.sekretaris')

@section('title', $asrama->nama)
@section('subtitle', 'PJ: '.($asrama->penanggungJawab->nama ?? '—'))

@section('header-actions')
    <a href="{{ route('sekretaris.asrama.edit', $asrama) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-ink-900 hover:bg-ink-900/5">
        Ubah Data
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <x-stat-card label="Kapasitas" :value="$asrama->kapasitas" icon="building" accent="forest" />
    <x-stat-card label="Penghuni Aktif" :value="$asrama->jumlahPenghuniAktif()" icon="users" accent="gold" />
    <x-stat-card label="Sisa Kapasitas" :value="max(0, $asrama->kapasitas - $asrama->jumlahPenghuniAktif())" icon="trending-up" accent="rose" />
</div>

<div class="mt-6 rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="border-b border-ink-900/5 px-6 py-4">
        <h2 class="font-display text-base font-semibold text-ink-900">Penghuni Saat Ini</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Nama Santri</th>
                    <th class="px-6 py-3 font-medium">Tanggal Masuk</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @forelse ($asrama->penghuniAktif as $penghuni)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3">
                            <a href="{{ route('sekretaris.santri.show', $penghuni->santri) }}" class="font-medium text-ink-900 hover:text-forest-700">
                                {{ $penghuni->santri->nama_lengkap }}
                            </a>
                        </td>
                        <td class="px-6 py-3 text-slate-600">{{ $penghuni->tanggal_masuk->format('d-m-Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada penghuni.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="border-b border-ink-900/5 px-6 py-4">
        <h2 class="font-display text-base font-semibold text-ink-900">Riwayat Penghuni</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Nama Santri</th>
                    <th class="px-6 py-3 font-medium">Masuk</th>
                    <th class="px-6 py-3 font-medium">Keluar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @forelse ($asrama->riwayatPenghuni as $riwayat)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3 text-ink-900">{{ $riwayat->santri->nama_lengkap ?? '—' }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $riwayat->tanggal_masuk->format('d-m-Y') }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $riwayat->tanggal_keluar?->format('d-m-Y') ?? 'sekarang' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada riwayat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
