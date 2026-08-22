@extends('layouts.sekretaris')

@section('title', $pengurus->nama)
@section('subtitle', $pengurus->jabatan)

@section('header-actions')
    <a href="{{ route('sekretaris.pengurus.edit', $pengurus) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-ink-900 hover:bg-ink-900/5">
        Ubah Data
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        <h2 class="font-display text-base font-semibold text-ink-900">Detail Pengurus</h2>
        <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
            <div><dt class="text-slate-500">Nama</dt><dd class="font-medium text-ink-900">{{ $pengurus->nama }}</dd></div>
            <div><dt class="text-slate-500">NIP Internal</dt><dd class="text-ink-900">{{ $pengurus->nip_internal ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Jabatan</dt><dd class="text-ink-900">{{ $pengurus->jabatan }}</dd></div>
            <div><dt class="text-slate-500">Periode</dt><dd class="text-ink-900">{{ $pengurus->periode }}</dd></div>
            <div><dt class="text-slate-500">Kontak</dt><dd class="text-ink-900">{{ $pengurus->kontak ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Status</dt><dd><x-badge :color="$pengurus->is_active ? 'emerald' : 'slate'">{{ $pengurus->is_active ? 'Aktif' : 'Nonaktif' }}</x-badge></dd></div>
            <div><dt class="text-slate-500">Akun Login</dt><dd class="text-ink-900">{{ $pengurus->user->username ?? 'Belum ada akun' }}</dd></div>
        </dl>
    </div>

    <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        <h2 class="font-display text-base font-semibold text-ink-900">Asrama yang Diawasi</h2>
        <div class="mt-4 divide-y divide-ink-900/5">
            @forelse ($pengurus->asramaDiawasi as $asrama)
                <div class="py-2.5 text-sm text-ink-900">{{ $asrama->nama }}</div>
            @empty
                <p class="py-2.5 text-sm text-slate-500">Tidak mengawasi asrama manapun.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
