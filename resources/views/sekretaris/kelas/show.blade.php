@extends('layouts.sekretaris')

@section('title', $kelas->nama)
@section('subtitle', ($kelas->tingkat->nama ?? '').' · '.$kelas->tahun_ajaran)

@section('header-actions')
    <a href="{{ route('sekretaris.kelas.edit', $kelas) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-ink-900 hover:bg-ink-900/5">
        Ubah Data
    </a>
@endsection

@section('content')
<div class="rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="border-b border-ink-900/5 px-6 py-4">
        <h2 class="font-display text-base font-semibold text-ink-900">Santri di Kelas Ini</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">NIS</th>
                    <th class="px-6 py-3 font-medium">Nama</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @forelse ($kelas->santris as $santri)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3 text-slate-600">{{ $santri->nis }}</td>
                        <td class="px-6 py-3">
                            <a href="{{ route('sekretaris.santri.show', $santri) }}" class="font-medium text-ink-900 hover:text-forest-700">{{ $santri->nama_lengkap }}</a>
                        </td>
                        <td class="px-6 py-3"><x-badge color="emerald">{{ $santri->status }}</x-badge></td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada santri di kelas ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
