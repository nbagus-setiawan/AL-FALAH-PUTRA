@extends('layouts.sekretaris')

@section('title', 'Poin Kedisiplinan')
@section('subtitle', 'Catatan pelanggaran dan akumulasi poin santri.')

@section('header-actions')
    <a href="{{ route('sekretaris.pelanggaran.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
        <x-icon name="alert-triangle" class="h-4 w-4" />
        Catat Pelanggaran
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
            <form method="GET" class="flex flex-wrap gap-3">
                <select name="kategori" class="rounded-xl border border-ink-900/10 px-3.5 py-2.5 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20">
                    <option value="">Semua Kategori</option>
                    @foreach (['Ringan', 'Sedang', 'Berat'] as $k)
                        <option value="{{ $k }}" @selected(request('kategori') === $k)>{{ $k }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest-700">Filter</button>
            </form>
        </div>

        <div class="rounded-2xl border border-ink-900/5 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                            <th class="px-6 py-3 font-medium">Santri</th>
                            <th class="px-6 py-3 font-medium">Pelanggaran</th>
                            <th class="px-6 py-3 font-medium">Kategori</th>
                            <th class="px-6 py-3 font-medium">Tanggal</th>
                            <th class="px-6 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-900/5">
                        @php $kategoriColor = ['Ringan' => 'emerald', 'Sedang' => 'amber', 'Berat' => 'rose']; @endphp
                        @forelse ($pelanggarans as $p)
                            <tr class="hover:bg-cream-50/60">
                                <td class="px-6 py-3">
                                    <a href="{{ route('sekretaris.santri.show', $p->santri) }}" class="font-medium text-ink-900 hover:text-forest-700">{{ $p->santri->nama_lengkap }}</a>
                                </td>
                                <td class="px-6 py-3 text-slate-600">{{ $p->jenis_pelanggaran }}</td>
                                <td class="px-6 py-3"><x-badge :color="$kategoriColor[$p->kategori] ?? 'slate'">{{ $p->kategori }} · {{ $p->poin }} poin</x-badge></td>
                                <td class="px-6 py-3 text-slate-600">{{ $p->tanggal->format('d-m-Y') }}</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('sekretaris.pelanggaran.edit', $p) }}" class="text-sm font-medium text-forest-700 hover:underline">Ubah</a>
                                    <form method="POST" action="{{ route('sekretaris.pelanggaran.destroy', $p) }}" class="inline"
                                          onsubmit="return confirm('Hapus catatan pelanggaran ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="ml-3 text-sm font-medium text-rose-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada catatan pelanggaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-ink-900/5 px-6 py-4">{{ $pelanggarans->withQueryString()->links() }}</div>
        </div>
    </div>

    <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        <h2 class="font-display text-base font-semibold text-ink-900">Poin Tertinggi</h2>
        <p class="mt-1 text-sm text-slate-500">10 santri dengan akumulasi poin tertinggi.</p>
        @php $warnaMap = ['Hijau' => 'emerald', 'Kuning' => 'amber', 'Merah' => 'rose']; @endphp
        <div class="mt-4 divide-y divide-ink-900/5">
            @forelse ($santriPoinTertinggi as $s)
                <div class="flex items-center justify-between py-3">
                    <a href="{{ route('sekretaris.santri.show', $s) }}" class="min-w-0 truncate text-sm font-medium text-ink-900 hover:text-forest-700">{{ $s->nama_lengkap }}</a>
                    <div class="flex shrink-0 items-center gap-2">
                        <span class="text-sm font-semibold text-ink-900">{{ $s->total_poin }}</span>
                        <x-badge :color="$warnaMap[$s->warna_poin] ?? 'slate'">{{ $s->warna_poin }}</x-badge>
                    </div>
                </div>
            @empty
                <p class="py-3 text-sm text-slate-500">Belum ada data.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
