@extends('layouts.sekretaris')

@section('title', 'Data Pengurus')
@section('subtitle', 'Kelola data pengurus pondok.')

@section('header-actions')
    <a href="{{ route('sekretaris.pengurus.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
        <x-icon name="id-badge" class="h-4 w-4" />
        Tambah Pengurus
    </a>
@endsection

@section('content')
<div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
    <form method="GET" class="flex gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama pengurus..."
               class="flex-1 rounded-xl border border-ink-900/10 px-3.5 py-2.5 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20" />
        <button type="submit" class="rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest-700">Cari</button>
    </form>
</div>

<div class="mt-6 rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Nama</th>
                    <th class="px-6 py-3 font-medium">Jabatan</th>
                    <th class="px-6 py-3 font-medium">Periode</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @forelse ($pengurus as $p)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3">
                            <a href="{{ route('sekretaris.pengurus.show', $p) }}" class="font-medium text-ink-900 hover:text-forest-700">{{ $p->nama }}</a>
                        </td>
                        <td class="px-6 py-3 text-slate-600">{{ $p->jabatan }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $p->periode }}</td>
                        <td class="px-6 py-3">
                            <x-badge :color="$p->is_active ? 'emerald' : 'slate'">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</x-badge>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('sekretaris.pengurus.edit', $p) }}" class="text-sm font-medium text-forest-700 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('sekretaris.pengurus.destroy', $p) }}" class="inline"
                                  onsubmit="return confirm('Hapus data pengurus {{ $p->nama }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-sm font-medium text-rose-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data pengurus.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-ink-900/5 px-6 py-4">{{ $pengurus->withQueryString()->links() }}</div>
</div>
@endsection
