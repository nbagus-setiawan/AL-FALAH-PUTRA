@extends('layouts.sekretaris')

@section('title', 'Tingkat')
@section('subtitle', 'Jenjang tingkat pendidikan pondok.')

@section('header-actions')
    <a href="{{ route('sekretaris.tingkat.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
        <x-icon name="layers" class="h-4 w-4" />
        Tambah Tingkat
    </a>
@endsection

@section('content')
<div class="rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Urutan</th>
                    <th class="px-6 py-3 font-medium">Nama Tingkat</th>
                    <th class="px-6 py-3 font-medium">Jumlah Kelas</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @forelse ($tingkats as $t)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3 text-slate-600">{{ $t->urutan }}</td>
                        <td class="px-6 py-3 font-medium text-ink-900">{{ $t->nama }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $t->kelas_count }}</td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('sekretaris.tingkat.edit', $t) }}" class="text-sm font-medium text-forest-700 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('sekretaris.tingkat.destroy', $t) }}" class="inline"
                                  onsubmit="return confirm('Hapus tingkat {{ $t->nama }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-sm font-medium text-rose-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data tingkat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
