@extends('layouts.bendahara')

@section('title', $itemRincian->nama)
@section('subtitle', $itemRincian->subKategori->kategori->nama.' / '.$itemRincian->subKategori->nama)

@section('header-actions')
    <a href="{{ route('bendahara.kategori.show', $itemRincian->subKategori->kategori) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-ink-900 hover:bg-ink-900/5">
        ← Kembali
    </a>
    @unless ($itemRincian->subKategori->kategori->tahunAnggaran->isApprovalLocked())
        <a href="{{ route('bendahara.item-rincian.edit', $itemRincian) }}"
           class="inline-flex items-center gap-2 rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-ink-900 hover:bg-ink-900/5">
            Ubah
        </a>
    @endunless
@endsection

@section('content')
@php $locked = $itemRincian->subKategori->kategori->tahunAnggaran->isApprovalLocked(); @endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <x-stat-card label="Rencana" :value="'Rp'.number_format($itemRincian->jumlah_rencana, 0, ',', '.')" icon="banknote" accent="forest" />
    <x-stat-card label="Realisasi" :value="'Rp'.number_format($itemRincian->total_realisasi, 0, ',', '.')" icon="trending-up" accent="gold" />
    <x-stat-card label="Serapan" :value="$itemRincian->persentase_serapan.'%'" icon="chart" accent="rose"
                 :sublabel="'Sisa Rp'.number_format(max(0, $itemRincian->selisih), 0, ',', '.')" />
</div>

@if ($itemRincian->keterangan)
    <div class="mt-6 rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        <h2 class="font-display text-base font-semibold text-ink-900">Keterangan</h2>
        <p class="mt-2 text-sm text-ink-900">{{ $itemRincian->keterangan }}</p>
    </div>
@endif

@if ($itemRincian->rencanaBulanans->isNotEmpty())
    @php $bulanLabel = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des']; @endphp
    <div class="mt-6 rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        <h2 class="font-display text-base font-semibold text-ink-900">Rencana Per Bulan</h2>
        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($itemRincian->rencanaBulanans->sortBy('bulan') as $rencana)
                <div class="rounded-xl border border-ink-900/5 bg-cream-50/60 px-3 py-2">
                    <p class="text-xs text-slate-500">{{ $bulanLabel[$rencana->bulan] }}</p>
                    <p class="text-sm font-medium text-ink-900">Rp{{ number_format($rencana->jumlah, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="mt-6 rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="border-b border-ink-900/5 px-6 py-4">
        <h2 class="font-display text-base font-semibold text-ink-900">Realisasi</h2>
        <p class="mt-1 text-sm text-slate-500">Pencatatan realisasi tetap terbuka meskipun RAPB sudah disetujui.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Tanggal</th>
                    <th class="px-6 py-3 font-medium">Jumlah</th>
                    <th class="px-6 py-3 font-medium">Keterangan</th>
                    <th class="px-6 py-3 font-medium">Dicatat Oleh</th>
                    <th class="px-6 py-3 font-medium">Bukti</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @forelse ($itemRincian->realisasis as $realisasi)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3 text-slate-600">{{ $realisasi->tanggal->format('d-m-Y') }}</td>
                        <td class="px-6 py-3 font-medium text-ink-900">Rp{{ number_format($realisasi->jumlah, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $realisasi->keterangan ?? '—' }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $realisasi->pencatat->name ?? '—' }}</td>
                        <td class="px-6 py-3">
                            @if ($realisasi->bukti_path)
                                <a href="{{ route('bendahara.realisasi.bukti', $realisasi) }}" target="_blank" class="text-forest-700 hover:underline">Lihat</a>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <form method="POST" action="{{ route('bendahara.realisasi.destroy', $realisasi) }}"
                                  onsubmit="return confirm('Hapus realisasi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-rose-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada realisasi tercatat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-ink-900/5 p-6">
        <h3 class="text-sm font-semibold text-ink-900">Catat Realisasi Baru</h3>
        <form method="POST" action="{{ route('bendahara.realisasi.store', $itemRincian) }}" enctype="multipart/form-data"
              class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @csrf
            <x-field label="Jumlah" name="jumlah" required>
                <x-input type="number" name="jumlah" step="0.01" min="0.01" />
            </x-field>
            <x-field label="Tanggal" name="tanggal" required>
                <x-input type="date" name="tanggal" :value="now()->format('Y-m-d')" />
            </x-field>
            <x-field label="Keterangan" name="keterangan">
                <x-input name="keterangan" />
            </x-field>
            <x-field label="Bukti" name="bukti" required hint="PDF/JPG/PNG, maks 5MB.">
                <input type="file" name="bukti" accept=".pdf,.jpg,.jpeg,.png" required
                       class="block w-full rounded-xl border border-ink-900/10 bg-white px-3 py-2 text-sm text-ink-900 shadow-sm file:mr-2 file:rounded-lg file:border-0 file:bg-forest-50 file:px-2.5 file:py-1 file:text-xs file:font-medium file:text-forest-700" />
            </x-field>
            <div class="sm:col-span-2 lg:col-span-4 flex justify-end">
                <button type="submit" class="rounded-xl bg-forest-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
                    Simpan Realisasi
                </button>
            </div>
        </form>
    </div>
</div>

@if (! $locked && $itemRincian->realisasis->isEmpty())
    <div class="mt-6 flex justify-end">
        <form method="POST" action="{{ route('bendahara.item-rincian.destroy', $itemRincian) }}"
              onsubmit="return confirm('Hapus item rincian {{ $itemRincian->nama }}?')">
            @csrf @method('DELETE')
            <button type="submit" class="rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-medium text-rose-600 hover:bg-rose-50">
                Hapus Item Rincian
            </button>
        </form>
    </div>
@endif
@endsection
