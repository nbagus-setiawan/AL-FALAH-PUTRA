@extends('layouts.bendahara')

@section('title', 'Tahun Anggaran')
@section('subtitle', 'Kelola RAPB per tahun anggaran.')

@section('header-actions')
    <a href="{{ route('bendahara.tahun-anggaran.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
        <x-icon name="banknote" class="h-4 w-4" />
        Buat Tahun Anggaran
    </a>
@endsection

@section('content')
<div class="rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Tahun Anggaran</th>
                    <th class="px-6 py-3 font-medium">Periode</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Aktif</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @php $approvalColor = ['Pending' => 'amber', 'Approved' => 'emerald', 'Rejected' => 'rose']; @endphp
                @forelse ($tahunAnggarans as $tahun)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3">
                            <a href="{{ route('bendahara.tahun-anggaran.show', $tahun) }}" class="font-medium text-ink-900 hover:text-forest-700">{{ $tahun->nama }}</a>
                        </td>
                        <td class="px-6 py-3 text-slate-600">{{ $tahun->tanggal_mulai->format('d M Y') }} – {{ $tahun->tanggal_selesai->format('d M Y') }}</td>
                        <td class="px-6 py-3"><x-badge :color="$approvalColor[$tahun->status_approval] ?? 'slate'">{{ $tahun->status_approval }}</x-badge></td>
                        <td class="px-6 py-3">
                            @if ($tahun->is_active)
                                <x-badge color="forest">Aktif</x-badge>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('bendahara.tahun-anggaran.show', $tahun) }}" class="text-sm font-medium text-forest-700 hover:underline">Kelola</a>
                            <form method="POST" action="{{ route('bendahara.tahun-anggaran.destroy', $tahun) }}" class="inline"
                                  onsubmit="return confirm('Hapus tahun anggaran {{ $tahun->nama }}? Hanya bisa dihapus jika belum memiliki kategori.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-sm font-medium text-rose-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada tahun anggaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-ink-900/5 px-6 py-4">{{ $tahunAnggarans->links() }}</div>
</div>
@endsection
