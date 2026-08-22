@extends('layouts.bendahara')

@section('title', $tahunAnggaran->nama)
@section('subtitle', 'Kelola kategori dan rincian RAPB.')

@section('header-actions')
    <a href="{{ route('bendahara.tahun-anggaran.lpj', $tahunAnggaran) }}" target="_blank"
       class="inline-flex items-center gap-2 rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-ink-900 hover:bg-ink-900/5">
        Cetak LPJ
    </a>
    @unless ($tahunAnggaran->isApprovalLocked())
        <a href="{{ route('bendahara.tahun-anggaran.edit', $tahunAnggaran) }}"
           class="inline-flex items-center gap-2 rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-ink-900 hover:bg-ink-900/5">
            Ubah
        </a>
    @endunless
    @if ($tahunAnggaran->status_approval !== 'Pending')
        <form method="POST" action="{{ route('bendahara.tahun-anggaran.ajukan', $tahunAnggaran) }}"
              onsubmit="return confirm('Ajukan RAPB {{ $tahunAnggaran->nama }} untuk persetujuan Ketua Umum?')">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest-700">
                Ajukan Persetujuan
            </button>
        </form>
    @endif
@endsection

@section('content')
@php $approvalColor = ['Pending' => 'amber', 'Approved' => 'emerald', 'Rejected' => 'rose']; @endphp

@if ($tahunAnggaran->isApprovalLocked())
    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        RAPB ini sudah <strong>disetujui</strong> Ketua Umum. Struktur anggaran (kategori/sub-kategori/item) terkunci dari perubahan —
        pencatatan realisasi tetap bisa dilakukan seperti biasa.
    </div>
@elseif ($tahunAnggaran->status_approval === 'Rejected')
    <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        RAPB ini <strong>ditolak</strong> Ketua Umum{{ $tahunAnggaran->catatan_penolakan ? ': '.$tahunAnggaran->catatan_penolakan : '.' }}
        Lakukan revisi lalu ajukan ulang.
    </div>
@elseif ($tahunAnggaran->status_approval === 'Pending')
    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
        RAPB ini sedang <strong>menunggu persetujuan</strong> Ketua Umum.
    </div>
@endif

<div class="flex items-center gap-3">
    <x-badge :color="$approvalColor[$tahunAnggaran->status_approval] ?? 'slate'">{{ $tahunAnggaran->status_approval }}</x-badge>
    @if ($tahunAnggaran->is_active)
        <x-badge color="forest">Tahun Anggaran Aktif</x-badge>
    @endif
</div>

<div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <x-stat-card label="Periode" :value="$tahunAnggaran->tanggal_mulai->format('d M Y').' – '.$tahunAnggaran->tanggal_selesai->format('d M Y')" icon="calendar" accent="forest" />
    <x-stat-card label="Total Rencana" :value="'Rp'.number_format($totalRencana, 0, ',', '.')" icon="banknote" accent="gold" />
    <x-stat-card label="Total Realisasi" :value="'Rp'.number_format($totalRealisasi, 0, ',', '.')" icon="trending-up" accent="rose" />
</div>

<div class="mt-6 rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-ink-900/5 px-6 py-4">
        <h2 class="font-display text-base font-semibold text-ink-900">Kategori Anggaran</h2>
        @unless ($tahunAnggaran->isApprovalLocked())
            <a href="{{ route('bendahara.tahun-anggaran.kategori.create', $tahunAnggaran) }}"
               class="rounded-xl bg-forest-800 px-3.5 py-2 text-xs font-semibold text-white hover:bg-forest-700">
                + Tambah Kategori
            </a>
        @endunless
    </div>
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
                @forelse ($tahunAnggaran->kategoris as $kategori)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3">
                            <a href="{{ route('bendahara.kategori.show', $kategori) }}" class="font-medium text-ink-900 hover:text-forest-700">{{ $kategori->nama }}</a>
                        </td>
                        <td class="px-6 py-3"><x-badge :color="$kategori->jenis === 'Pemasukan' ? 'emerald' : 'rose'">{{ $kategori->jenis }}</x-badge></td>
                        <td class="px-6 py-3 text-slate-600">{{ $kategori->subKategoris->count() }}</td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('bendahara.kategori.show', $kategori) }}" class="text-sm font-medium text-forest-700 hover:underline">Kelola</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada kategori. Tambahkan kategori Pemasukan/Pengeluaran untuk mulai menyusun RAPB.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
