@extends('layouts.sekretaris')

@section('title', 'Jenis Surat')
@section('subtitle', 'Master data jenis surat untuk generator surat.')

@section('header-actions')
    <a href="{{ route('sekretaris.jenis-surat.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
        <x-icon name="tag" class="h-4 w-4" />
        Tambah Jenis Surat
    </a>
@endsection

@section('content')
<div class="rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Kode</th>
                    <th class="px-6 py-3 font-medium">Nama Jenis Surat</th>
                    <th class="px-6 py-3 font-medium">Jumlah Surat</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @forelse ($jenisSurats as $jenis)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3"><x-badge color="forest">{{ $jenis->kode }}</x-badge></td>
                        <td class="px-6 py-3 font-medium text-ink-900">{{ $jenis->nama }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $jenis->surats_count }}</td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('sekretaris.jenis-surat.edit', $jenis) }}" class="text-sm font-medium text-forest-700 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('sekretaris.jenis-surat.destroy', $jenis) }}" class="inline"
                                  onsubmit="return confirm('Hapus jenis surat {{ $jenis->nama }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-sm font-medium text-rose-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada jenis surat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
