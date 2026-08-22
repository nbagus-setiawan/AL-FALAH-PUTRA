@extends('layouts.bendahara')

@section('title', $kategori->nama)
@section('subtitle', $kategori->tahunAnggaran->nama)

@section('header-actions')
    <a href="{{ route('bendahara.tahun-anggaran.show', $kategori->tahunAnggaran) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-ink-900 hover:bg-ink-900/5">
        ← Kembali
    </a>
    @unless ($kategori->tahunAnggaran->isApprovalLocked())
        <a href="{{ route('bendahara.kategori.edit', $kategori) }}"
           class="inline-flex items-center gap-2 rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-ink-900 hover:bg-ink-900/5">
            Ubah Kategori
        </a>
    @endunless
@endsection

@section('content')
@php $locked = $kategori->tahunAnggaran->isApprovalLocked(); @endphp

@if ($locked)
    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        RAPB tahun anggaran ini sudah disetujui — struktur (sub-kategori/item rincian) terkunci. Pencatatan realisasi tetap bisa dilakukan.
    </div>
@endif

<div class="flex items-center gap-3">
    <x-badge :color="$kategori->jenis === 'Pemasukan' ? 'emerald' : 'rose'">{{ $kategori->jenis }}</x-badge>
</div>

<div class="mt-6 space-y-4">
    @forelse ($kategori->subKategoris as $sub)
        <div class="rounded-2xl border border-ink-900/5 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-ink-900/5 px-6 py-4">
                <h2 class="font-display text-base font-semibold text-ink-900">{{ $sub->nama }}</h2>
                @unless ($locked)
                    <div class="flex items-center gap-3">
                        <details class="relative">
                            <summary class="cursor-pointer text-sm font-medium text-forest-700 hover:underline">Ubah</summary>
                            <form method="POST" action="{{ route('bendahara.sub-kategori.update', $sub) }}"
                                  class="absolute right-0 z-10 mt-2 w-64 rounded-xl border border-ink-900/10 bg-white p-4 shadow-lg">
                                @csrf @method('PUT')
                                <input type="text" name="nama" value="{{ $sub->nama }}" required
                                       class="block w-full rounded-lg border border-ink-900/10 px-3 py-2 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20" />
                                <button type="submit" class="mt-2 w-full rounded-lg bg-forest-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-forest-700">Simpan</button>
                            </form>
                        </details>
                        <form method="POST" action="{{ route('bendahara.sub-kategori.destroy', $sub) }}"
                              onsubmit="return confirm('Hapus sub-kategori {{ $sub->nama }}? Hanya bisa dihapus jika belum memiliki item rincian.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm font-medium text-rose-600 hover:underline">Hapus</button>
                        </form>
                    </div>
                @endunless
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                            <th class="px-6 py-2.5 font-medium">Item Rincian</th>
                            <th class="px-6 py-2.5 font-medium">Rencana</th>
                            <th class="px-6 py-2.5 font-medium">Realisasi</th>
                            <th class="px-6 py-2.5 font-medium">Serapan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-900/5">
                        @forelse ($sub->itemRincians as $item)
                            <tr class="hover:bg-cream-50/60">
                                <td class="px-6 py-2.5">
                                    <a href="{{ route('bendahara.item-rincian.show', $item) }}" class="font-medium text-ink-900 hover:text-forest-700">{{ $item->nama }}</a>
                                </td>
                                <td class="px-6 py-2.5 text-slate-600">Rp{{ number_format($item->jumlah_rencana, 0, ',', '.') }}</td>
                                <td class="px-6 py-2.5 text-slate-600">Rp{{ number_format($item->total_realisasi, 0, ',', '.') }}</td>
                                <td class="px-6 py-2.5 text-slate-600">{{ $item->persentase_serapan }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-4 text-center text-xs text-slate-500">Belum ada item rincian.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-ink-900/5 px-6 py-3">
                <a href="{{ route('bendahara.item-rincian.create', ['sub_kategori_rapb_id' => $sub->id]) }}"
                   class="text-sm font-medium text-forest-700 hover:underline">+ Tambah Item Rincian</a>
            </div>
        </div>
    @empty
        <div class="rounded-2xl border border-ink-900/5 bg-white p-8 text-center shadow-sm">
            <p class="text-sm text-slate-500">Belum ada sub-kategori. Tambahkan di bawah untuk mulai menyusun rincian.</p>
        </div>
    @endforelse
</div>

@unless ($locked)
    <div class="mt-6 rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        <h2 class="font-display text-base font-semibold text-ink-900">Tambah Sub-Kategori</h2>
        <form method="POST" action="{{ route('bendahara.sub-kategori.store', $kategori) }}" class="mt-3 flex gap-3">
            @csrf
            <input type="text" name="nama" placeholder="Nama sub-kategori" required
                   class="flex-1 rounded-xl border border-ink-900/10 px-3.5 py-2.5 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20" />
            <button type="submit" class="rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest-700">Tambah</button>
        </form>
    </div>
@endunless
@endsection
