@extends('layouts.sekretaris')

@section('title', 'Perizinan')
@section('subtitle', 'Pengajuan dan status izin santri.')

@section('header-actions')
    <a href="{{ route('sekretaris.izin.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
        <x-icon name="calendar" class="h-4 w-4" />
        Ajukan Izin
    </a>
@endsection

@section('content')
<div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
    <form method="GET" class="flex flex-wrap gap-3">
        <select name="status" class="rounded-xl border border-ink-900/10 px-3.5 py-2.5 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20">
            <option value="">Semua Status</option>
            @foreach (['Pending', 'Approved', 'Rejected', 'Sedang Izin', 'Sudah Kembali', 'Terlambat'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest-700">Filter</button>
        @if (request('status'))
            <a href="{{ route('sekretaris.izin.index') }}" class="rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-ink-900/5">Reset</a>
        @endif
    </form>
</div>

<div class="mt-6 rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Santri</th>
                    <th class="px-6 py-3 font-medium">Jenis</th>
                    <th class="px-6 py-3 font-medium">Keluar</th>
                    <th class="px-6 py-3 font-medium">Rencana Kembali</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @php
                    $statusColor = [
                        'Pending' => 'amber', 'Approved' => 'emerald', 'Rejected' => 'rose',
                        'Sedang Izin' => 'forest', 'Sudah Kembali' => 'slate', 'Terlambat' => 'rose',
                    ];
                @endphp
                @forelse ($izins as $izin)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3">
                            <a href="{{ route('sekretaris.izin.show', $izin) }}" class="font-medium text-ink-900 hover:text-forest-700">
                                {{ $izin->santri->nama_lengkap }}
                            </a>
                        </td>
                        <td class="px-6 py-3 text-slate-600">{{ $izin->jenis_izin }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $izin->tanggal_keluar->format('d-m-Y') }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $izin->rencana_kembali->format('d-m-Y') }}</td>
                        <td class="px-6 py-3"><x-badge :color="$statusColor[$izin->status] ?? 'slate'">{{ $izin->status }}</x-badge></td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('sekretaris.izin.show', $izin) }}" class="text-sm font-medium text-forest-700 hover:underline">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada pengajuan izin.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-ink-900/5 px-6 py-4">{{ $izins->withQueryString()->links() }}</div>
</div>
@endsection
