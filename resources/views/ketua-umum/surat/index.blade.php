@extends('layouts.ketua-umum')

@section('title', 'Persetujuan Surat')
@section('subtitle', 'Surat keluar yang menunggu persetujuan Anda.')

@section('content')
<div class="space-y-4">
    @forelse ($suratPending as $surat)
        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="font-display text-base font-semibold text-ink-900">{{ $surat->perihal }}</p>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $surat->nomor_surat }} · {{ $surat->jenisSurat->nama ?? '—' }} · {{ $surat->tanggal->format('d-m-Y') }}
                    </p>
                    <p class="mt-1 text-sm text-slate-600">Tujuan: {{ $surat->tujuan_pengirim }}</p>
                    @if ($surat->santri)
                        <p class="mt-1 text-sm text-slate-600">Santri terkait: {{ $surat->santri->nama_lengkap }} ({{ $surat->santri->nis }})</p>
                    @endif
                </div>
                <div class="flex shrink-0 gap-2">
                    <form method="POST" action="{{ route('ketua-umum.surat.approve', $surat) }}"
                          onsubmit="return confirm('Setujui surat {{ $surat->nomor_surat }}?')">
                        @csrf
                        <button type="submit" class="rounded-lg bg-forest-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-forest-700">
                            Setujui
                        </button>
                    </form>
                    <form method="POST" action="{{ route('ketua-umum.surat.reject', $surat) }}" class="js-reject-form">
                        @csrf
                        <input type="hidden" name="catatan_penolakan" class="js-reject-reason" />
                        <button type="submit" class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                            Tolak
                        </button>
                    </form>
                </div>
            </div>

            @if ($surat->isi_lengkap || $surat->isi_ringkas)
                <details class="mt-4 rounded-xl border border-ink-900/5 bg-cream-50/60 px-4 py-3">
                    <summary class="cursor-pointer text-sm font-medium text-forest-700">Lihat isi surat</summary>
                    <p class="mt-2 whitespace-pre-line text-sm text-ink-900">{{ $surat->isi_lengkap ?? $surat->isi_ringkas }}</p>
                </details>
            @endif

            @if ($surat->lampiran_path)
                <a href="{{ route('ketua-umum.surat.lampiran', $surat) }}" target="_blank"
                   class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-forest-700 hover:underline">
                    Buka Lampiran
                </a>
            @endif
        </div>
    @empty
        <div class="rounded-2xl border border-ink-900/5 bg-white p-8 text-center shadow-sm">
            <p class="text-sm text-slate-500">Tidak ada surat yang menunggu persetujuan.</p>
        </div>
    @endforelse
</div>

<div class="mt-6">{{ $suratPending->links() }}</div>

<script>
    document.querySelectorAll('.js-reject-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var alasan = window.prompt('Alasan penolakan (wajib diisi):');
            if (! alasan || ! alasan.trim()) {
                e.preventDefault();
                return;
            }
            form.querySelector('.js-reject-reason').value = alasan.trim();
        });
    });
</script>
@endsection
