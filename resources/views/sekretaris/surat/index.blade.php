@extends('layouts.sekretaris')

@section('title', 'Surat Keluar')
@section('subtitle', 'Arsip dan generator surat keluar.')

@section('header-actions')
    <a href="{{ route('sekretaris.surat.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
        <x-icon name="mail" class="h-4 w-4" />
        Buat Surat
    </a>
@endsection

@section('content')
<div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
    <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari perihal atau nomor surat..."
               class="rounded-xl border border-ink-900/10 px-3.5 py-2.5 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20 sm:col-span-2" />

        <select name="jenis_surat_id" class="rounded-xl border border-ink-900/10 px-3.5 py-2.5 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20">
            <option value="">Semua Jenis</option>
            @foreach ($jenisSurats as $jenis)
                <option value="{{ $jenis->id }}" @selected(request('jenis_surat_id') == $jenis->id)>{{ $jenis->nama }}</option>
            @endforeach
        </select>

        <button type="submit" class="rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest-700">Cari</button>
    </form>
</div>

<div class="mt-6 rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Nomor Surat</th>
                    <th class="px-6 py-3 font-medium">Perihal</th>
                    <th class="px-6 py-3 font-medium">Jenis</th>
                    <th class="px-6 py-3 font-medium">Tanggal</th>
                    <th class="px-6 py-3 font-medium">Approval</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @php
                    $approvalColor = ['Pending' => 'amber', 'Approved' => 'emerald', 'Rejected' => 'rose'];
                @endphp
                @forelse ($surats as $surat)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3">
                            <a href="{{ route('sekretaris.surat.show', $surat) }}" class="font-medium text-ink-900 hover:text-forest-700">
                                {{ $surat->nomor_surat }}
                            </a>
                        </td>
                        <td class="px-6 py-3 text-slate-600">{{ $surat->perihal }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $surat->jenisSurat->nama ?? '—' }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $surat->tanggal->format('d-m-Y') }}</td>
                        <td class="px-6 py-3">
                            <x-badge :color="$approvalColor[$surat->status_approval] ?? 'slate'">{{ $surat->status_approval }}</x-badge>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('sekretaris.surat.cetak', $surat) }}" class="text-sm font-medium text-forest-700 hover:underline" target="_blank">Cetak PDF</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada arsip surat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-ink-900/5 px-6 py-4">{{ $surats->withQueryString()->links() }}</div>
</div>
@endsection
