@extends('layouts.bendahara')

@section('title', 'Kategori RAPB')
@section('subtitle', $tahunAnggaran->nama)

@section('header-actions')
    <a href="{{ route('bendahara.tahun-anggaran.show', $tahunAnggaran) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-ink-900 hover:bg-ink-900/5">
        ← Kembali ke Tahun Anggaran
    </a>
    @unless ($tahunAnggaran->isApprovalLocked())
        <a href="{{ route('bendahara.tahun-anggaran.kategori.create', $tahunAnggaran) }}"
           class="inline-flex items-center gap-2 rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest-700">
            + Tambah Kategori
        </a>
    @endunless
@endsection

@section('content')
<div class="rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Nama Kategori</th>
                    <th class="px-6 py-3 font-medium">Jenis</th>
                    <th class="px-6 py-3 font-medium">Sub-Kategori</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @forelse ($kategoris as $kategori)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3">
                            <a href="{{ route('bendahara.kategori.show', $kategori) }}" class="font-medium text-ink-900 hover:text-forest-700">{{ $kategori->nama }}</a>
                        </td>
                        <td class="px-6 py-3"><x-badge :color="$kategori->jenis === 'Pemasukan' ? 'emerald' : 'rose'">{{ $kategori->jenis }}</x-badge></td>
                        <td class="px-6 py-3 text-slate-600">{{ $kategori->sub_kategoris_count }}</td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('bendahara.kategori.show', $kategori) }}" class="text-sm font-medium text-forest-700 hover:underline">Kelola</a>
                            @unless ($tahunAnggaran->isApprovalLocked())
                                <a href="{{ route('bendahara.kategori.edit', $kategori) }}" class="ml-3 text-sm font-medium text-slate-500 hover:underline">Ubah</a>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
