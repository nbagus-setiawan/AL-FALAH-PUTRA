@extends('layouts.sekretaris')

@section('title', 'Data Santri')
@section('subtitle', 'Kelola data induk santri.')

@section('header-actions')
    <a href="{{ route('sekretaris.santri.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
        <x-icon name="users" class="h-4 w-4" />
        Tambah Santri
    </a>
@endsection

@section('content')
<div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
    <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau NIS..."
               class="rounded-xl border border-ink-900/10 px-3.5 py-2.5 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20 sm:col-span-2" />

        <select name="kelas_id" class="rounded-xl border border-ink-900/10 px-3.5 py-2.5 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20">
            <option value="">Semua Kelas</option>
            @foreach ($kelasList as $kelas)
                <option value="{{ $kelas->id }}" @selected(request('kelas_id') == $kelas->id)>{{ $kelas->nama }}</option>
            @endforeach
        </select>

        <select name="status" class="rounded-xl border border-ink-900/10 px-3.5 py-2.5 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20">
            <option value="">Semua Status</option>
            @foreach (['Aktif', 'Lulus', 'Boyong'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
            @endforeach
        </select>

        <div class="sm:col-span-4 flex justify-end gap-2">
            <a href="{{ route('sekretaris.santri.index') }}" class="rounded-xl border border-ink-900/10 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-ink-900/5">Reset</a>
            <button type="submit" class="rounded-xl bg-forest-800 px-4 py-2 text-sm font-semibold text-white hover:bg-forest-700">Cari</button>
        </div>
    </form>
</div>

<div class="mt-6 rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">NIS</th>
                    <th class="px-6 py-3 font-medium">Nama Lengkap</th>
                    <th class="px-6 py-3 font-medium">Kelas</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @php
                    $statusColor = ['Aktif' => 'emerald', 'Lulus' => 'gold', 'Boyong' => 'slate'];
                @endphp
                @forelse ($santris as $santri)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3 text-slate-600">{{ $santri->nis }}</td>
                        <td class="px-6 py-3">
                            <a href="{{ route('sekretaris.santri.show', $santri) }}" class="font-medium text-ink-900 hover:text-forest-700">
                                {{ $santri->nama_lengkap }}
                            </a>
                        </td>
                        <td class="px-6 py-3 text-slate-600">{{ $santri->kelas->nama ?? '—' }}</td>
                        <td class="px-6 py-3">
                            <x-badge :color="$statusColor[$santri->status] ?? 'slate'">{{ $santri->status }}</x-badge>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('sekretaris.santri.edit', $santri) }}" class="text-sm font-medium text-forest-700 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('sekretaris.santri.destroy', $santri) }}" class="inline"
                                  onsubmit="return confirm('Hapus data santri {{ $santri->nama_lengkap }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-sm font-medium text-rose-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data santri.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-ink-900/5 px-6 py-4">
        {{ $santris->withQueryString()->links() }}
    </div>
</div>
@endsection
