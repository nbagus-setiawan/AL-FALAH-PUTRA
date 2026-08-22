@extends('layouts.sekretaris')

@section('title', 'Asrama')
@section('subtitle', 'Kelola data asrama dan kapasitasnya.')

@section('header-actions')
    <a href="{{ route('sekretaris.asrama.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
        <x-icon name="building" class="h-4 w-4" />
        Tambah Asrama
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($asramas as $asrama)
        @php $penuh = $asrama->penghuni_aktif_count >= $asrama->kapasitas; @endphp
        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <a href="{{ route('sekretaris.asrama.show', $asrama) }}" class="font-display text-base font-semibold text-ink-900 hover:text-forest-700">
                        {{ $asrama->nama }}
                    </a>
                    <p class="mt-1 text-sm text-slate-500">PJ: {{ $asrama->penanggungJawab->nama ?? '—' }}</p>
                </div>
                <x-badge :color="$penuh ? 'rose' : 'emerald'">{{ $asrama->penghuni_aktif_count }}/{{ $asrama->kapasitas }}</x-badge>
            </div>
            <div class="mt-4 h-2 rounded-full bg-cream-100">
                <div class="h-2 rounded-full {{ $penuh ? 'bg-rose-500' : 'bg-forest-600' }}"
                     style="width: {{ $asrama->kapasitas > 0 ? min(100, round($asrama->penghuni_aktif_count / $asrama->kapasitas * 100)) : 0 }}%"></div>
            </div>
            <div class="mt-4 flex justify-end gap-3 text-sm">
                <a href="{{ route('sekretaris.asrama.edit', $asrama) }}" class="font-medium text-forest-700 hover:underline">Ubah</a>
                <form method="POST" action="{{ route('sekretaris.asrama.destroy', $asrama) }}"
                      onsubmit="return confirm('Hapus asrama {{ $asrama->nama }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="font-medium text-rose-600 hover:underline">Hapus</button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-sm text-slate-500">Belum ada data asrama.</p>
    @endforelse
</div>
@endsection
