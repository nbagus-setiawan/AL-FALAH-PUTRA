@extends('layouts.ketua-umum')

@section('title', $tahunAnggaran->nama)
@section('subtitle', 'Rincian RAPB — hanya baca. Persetujuan mengunci perubahan struktur anggaran.')

@section('header-actions')
    @if ($tahunAnggaran->status_approval === 'Pending')
        <form method="POST" action="{{ route('ketua-umum.rapb.approve', $tahunAnggaran) }}"
              onsubmit="return confirm('Setujui RAPB {{ $tahunAnggaran->nama }}? Struktur anggaran akan terkunci dari perubahan.')">
            @csrf
            <button type="submit" class="rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest-700">
                Setujui RAPB
            </button>
        </form>
        <form method="POST" action="{{ route('ketua-umum.rapb.reject', $tahunAnggaran) }}" class="js-reject-form">
            @csrf
            <input type="hidden" name="catatan_penolakan" class="js-reject-reason" />
            <button type="submit" class="rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50">
                Tolak
            </button>
        </form>
    @endif
@endsection

@section('content')
@php $approvalColor = ['Pending' => 'amber', 'Approved' => 'emerald', 'Rejected' => 'rose']; @endphp

<div class="flex items-center gap-3">
    <x-badge :color="$approvalColor[$tahunAnggaran->status_approval] ?? 'slate'">{{ $tahunAnggaran->status_approval }}</x-badge>
    @if ($tahunAnggaran->catatan_penolakan)
        <span class="text-sm text-rose-600">Catatan: {{ $tahunAnggaran->catatan_penolakan }}</span>
    @endif
</div>

<div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <x-stat-card label="Periode" :value="$tahunAnggaran->tanggal_mulai->format('d M Y').' – '.$tahunAnggaran->tanggal_selesai->format('d M Y')" icon="calendar" accent="forest" />
    <x-stat-card label="Total Rencana" :value="'Rp'.number_format($totalRencana, 0, ',', '.')" icon="banknote" accent="gold" />
    <x-stat-card label="Total Realisasi" :value="'Rp'.number_format($totalRealisasi, 0, ',', '.')" icon="trending-up" accent="rose" />
</div>

<div class="mt-6 space-y-4">
    @forelse ($tahunAnggaran->kategoris as $kategori)
        <details class="rounded-2xl border border-ink-900/5 bg-white shadow-sm" open>
            <summary class="cursor-pointer px-6 py-4 font-display text-base font-semibold text-ink-900">
                {{ $kategori->nama }}
                <x-badge :color="$kategori->jenis === 'Pemasukan' ? 'emerald' : 'rose'">{{ $kategori->jenis }}</x-badge>
            </summary>

            <div class="border-t border-ink-900/5 px-6 py-4 space-y-5">
                @forelse ($kategori->subKategoris as $sub)
                    <div>
                        <p class="text-sm font-semibold text-ink-900">{{ $sub->nama }}</p>
                        <div class="mt-2 overflow-x-auto rounded-xl border border-ink-900/5">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="border-b border-ink-900/5 bg-cream-50/60 text-xs uppercase tracking-wide text-slate-500">
                                        <th class="px-4 py-2 font-medium">Item</th>
                                        <th class="px-4 py-2 font-medium">Rencana</th>
                                        <th class="px-4 py-2 font-medium">Realisasi</th>
                                        <th class="px-4 py-2 font-medium">Serapan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-ink-900/5">
                                    @forelse ($sub->itemRincians as $item)
                                        <tr>
                                            <td class="px-4 py-2 text-ink-900">{{ $item->nama }}</td>
                                            <td class="px-4 py-2 text-slate-600">Rp{{ number_format($item->jumlah_rencana, 0, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-slate-600">Rp{{ number_format($item->total_realisasi, 0, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-slate-600">{{ $item->persentase_serapan }}%</td>
                                        </tr>
                                        @if ($item->realisasis->isNotEmpty())
                                            <tr>
                                                <td colspan="4" class="bg-cream-50/40 px-4 py-2">
                                                    <div class="space-y-1">
                                                        @foreach ($item->realisasis as $realisasi)
                                                            <div class="flex items-center justify-between text-xs text-slate-500">
                                                                <span>{{ $realisasi->tanggal->format('d-m-Y') }} — Rp{{ number_format($realisasi->jumlah, 0, ',', '.') }} ({{ $realisasi->keterangan ?? 'tanpa keterangan' }})</span>
                                                                @if ($realisasi->bukti_path)
                                                                    <a href="{{ route('ketua-umum.realisasi.bukti', $realisasi) }}" target="_blank" class="font-medium text-forest-700 hover:underline">Lihat Bukti</a>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr><td colspan="4" class="px-4 py-3 text-center text-xs text-slate-500">Belum ada item rincian.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada sub-kategori.</p>
                @endforelse
            </div>
        </details>
    @empty
        <div class="rounded-2xl border border-ink-900/5 bg-white p-8 text-center shadow-sm">
            <p class="text-sm text-slate-500">Belum ada kategori RAPB.</p>
        </div>
    @endforelse
</div>

<script>
    document.querySelectorAll('.js-reject-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var alasan = window.prompt('Alasan penolakan (wajib diisi):');
            if (! alasan || ! alasan.trim()) {
                e.preventDefault();
                return;
            }
            form.querySelector('.js-reject-reason').value = alasan.trim();
        });
    });
</script>
@endsection
