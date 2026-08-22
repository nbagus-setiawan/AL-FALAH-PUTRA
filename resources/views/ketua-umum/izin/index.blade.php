@extends('layouts.ketua-umum')

@section('title', 'Persetujuan Izin')
@section('subtitle', 'Pengajuan izin santri yang menunggu persetujuan Anda.')

@section('content')
<div class="rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Santri</th>
                    <th class="px-6 py-3 font-medium">Jenis</th>
                    <th class="px-6 py-3 font-medium">Keluar</th>
                    <th class="px-6 py-3 font-medium">Rencana Kembali</th>
                    <th class="px-6 py-3 font-medium">Alasan</th>
                    <th class="px-6 py-3 font-medium">Penjemput</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @forelse ($izinPending as $izin)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3">
                            <p class="font-medium text-ink-900">{{ $izin->santri->nama_lengkap }}</p>
                            <p class="text-xs text-slate-500">NIS {{ $izin->santri->nis }}</p>
                        </td>
                        <td class="px-6 py-3 text-slate-600">{{ $izin->jenis_izin }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $izin->tanggal_keluar->format('d-m-Y') }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $izin->rencana_kembali->format('d-m-Y') }}</td>
                        <td class="px-6 py-3 max-w-xs text-slate-600">
                            <span class="line-clamp-2">{{ $izin->alasan }}</span>
                        </td>
                        <td class="px-6 py-3 text-slate-600">{{ $izin->penjemput }}</td>
                        <td class="px-6 py-3">
                            <div class="flex justify-end gap-2">
                                <form method="POST" action="{{ route('ketua-umum.izin.approve', $izin) }}"
                                      onsubmit="return confirm('Setujui izin {{ $izin->santri->nama_lengkap }}?')">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-forest-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-forest-700">
                                        Setujui
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('ketua-umum.izin.reject', $izin) }}" class="js-reject-form">
                                    @csrf
                                    <input type="hidden" name="catatan_penolakan" class="js-reject-reason" />
                                    <button type="submit" class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-sm text-slate-500">Tidak ada pengajuan izin yang menunggu persetujuan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-ink-900/5 px-6 py-4">{{ $izinPending->links() }}</div>
</div>

<script>
    // Alasan penolakan wajib diisi (validated required|string|max:500 di controller),
    // jadi diminta lewat prompt sebelum form submit, bukan halaman terpisah.
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
