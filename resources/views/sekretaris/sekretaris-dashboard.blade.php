@extends('layouts.sekretaris')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan data santri, izin, dan persuratan.')

@section('content')
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card label="Santri Aktif" :value="$total_santri_aktif" icon="users" accent="forest" />
    <x-stat-card label="Sedang Izin" :value="$sedang_izin" icon="calendar" accent="amber" />
    <x-stat-card label="Surat Bulan Ini" :value="$surat_bulan_ini" icon="mail" accent="gold" />
    <x-stat-card label="Kelas Terisi" :value="$santri_per_kelas->count()" icon="academic-cap" accent="rose"
                 sublabel="Kelas dengan santri aktif" />
</div>

<div class="mt-6 rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
    <h2 class="font-display text-base font-semibold text-ink-900">Santri per Kelas</h2>
    <p class="mt-1 text-sm text-slate-500">Distribusi santri aktif berdasarkan kelas saat ini.</p>

    <div class="mt-5 space-y-3">
        @forelse ($santri_per_kelas as $baris)
            <div class="flex items-center gap-4">
                <p class="w-40 shrink-0 truncate text-sm font-medium text-ink-900">{{ $baris->kelas->nama ?? 'Belum ada kelas' }}</p>
                <div class="h-2 flex-1 rounded-full bg-cream-100">
                    <div class="h-2 rounded-full bg-forest-600"
                         style="width: {{ $total_santri_aktif > 0 ? min(100, round($baris->total / $total_santri_aktif * 100)) : 0 }}%"></div>
                </div>
                <span class="w-10 shrink-0 text-right text-sm font-semibold text-ink-900">{{ $baris->total }}</span>
            </div>
        @empty
            <p class="text-sm text-slate-500">Belum ada data santri.</p>
        @endforelse
    </div>
</div>
@endsection
