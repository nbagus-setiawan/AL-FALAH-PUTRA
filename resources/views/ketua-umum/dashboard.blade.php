@extends('layouts.sekretaris')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan data santri, perizinan, dan persuratan.')

@section('content')
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <x-stat-card label="Santri Aktif" :value="$total_santri_aktif" icon="users" accent="forest" />
    <x-stat-card label="Sedang Izin" :value="$sedang_izin" icon="calendar" accent="gold" />
    <x-stat-card label="Surat Bulan Ini" :value="$surat_bulan_ini" icon="mail" accent="rose" />
</div>

<div class="mt-6 rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="border-b border-ink-900/5 px-6 py-4">
        <h2 class="font-display text-base font-semibold text-ink-900">Santri Aktif per Kelas</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Kelas</th>
                    <th class="px-6 py-3 font-medium text-right">Jumlah Santri</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @forelse ($santri_per_kelas as $row)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3 text-ink-900">{{ $row->kelas->nama ?? 'Belum ada kelas' }}</td>
                        <td class="px-6 py-3 text-right font-medium text-ink-900">{{ $row->total }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data santri aktif.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection