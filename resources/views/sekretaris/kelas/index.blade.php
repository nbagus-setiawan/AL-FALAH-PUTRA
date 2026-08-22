@extends('layouts.sekretaris')

@section('title', 'Kelas')
@section('subtitle', 'Daftar kelas per tingkat dan tahun ajaran.')

@section('header-actions')
    <a href="{{ route('sekretaris.kelas.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
        <x-icon name="academic-cap" class="h-4 w-4" />
        Tambah Kelas
    </a>
@endsection

@section('content')
<div class="rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Nama Kelas</th>
                    <th class="px-6 py-3 font-medium">Tingkat</th>
                    <th class="px-6 py-3 font-medium">Tahun Ajaran</th>
                    <th class="px-6 py-3 font-medium">Jumlah Santri</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @forelse ($kelas as $k)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3">
                            <a href="{{ route('sekretaris.kelas.show', $k) }}" class="font-medium text-ink-900 hover:text-forest-700">{{ $k->nama }}</a>
                        </td>
                        <td class="px-6 py-3 text-slate-600">{{ $k->tingkat->nama ?? '—' }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $k->tahun_ajaran }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $k->santris_count }}</td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('sekretaris.kelas.edit', $k) }}" class="text-sm font-medium text-forest-700 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('sekretaris.kelas.destroy', $k) }}" class="inline"
                                  onsubmit="return confirm('Hapus kelas {{ $k->nama }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-sm font-medium text-rose-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data kelas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-ink-900/5 px-6 py-4">{{ $kelas->links() }}</div>
</div>
@endsection
