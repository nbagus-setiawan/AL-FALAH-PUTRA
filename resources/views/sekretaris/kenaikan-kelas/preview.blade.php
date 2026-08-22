@extends('layouts.sekretaris')

@section('title', 'Kenaikan Kelas')
@section('subtitle', 'Proses kenaikan kelas massal santri aktif.')

@section('content')
<div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-sm font-medium text-ink-900">Tahun Ajaran Baru</label>
            <input type="text" name="tahun_ajaran_baru" value="{{ $tahunAjaranBaru }}" placeholder="Contoh: 2027/2028"
                   class="mt-1.5 rounded-xl border border-ink-900/10 px-3.5 py-2.5 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20" />
        </div>
        <button type="submit" class="rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest-700">
            Tampilkan Preview
        </button>
    </form>
    <p class="mt-3 text-xs text-slate-500">
        Sistem mencoba mencocokkan kelas tujuan otomatis (nama kelas sama di tingkat berikutnya, tahun ajaran baru).
        Jika tidak cocok, pilih kelas tujuan manual di bawah.
    </p>
</div>

<form method="POST" action="{{ route('sekretaris.kenaikan-kelas.proses') }}" class="mt-6">
    @csrf

    <div class="rounded-2xl border border-ink-900/5 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3 font-medium">Santri</th>
                        <th class="px-6 py-3 font-medium">Kelas Sekarang</th>
                        <th class="px-6 py-3 font-medium">Aksi</th>
                        <th class="px-6 py-3 font-medium">Kelas / Keterangan Tujuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-900/5">
                    @forelse ($preview as $row)
                        <tr class="hover:bg-cream-50/60">
                            <td class="px-6 py-3">
                                <p class="font-medium text-ink-900">{{ $row['santri']->nama_lengkap }}</p>
                                <p class="text-xs text-slate-500">NIS {{ $row['santri']->nis }}</p>
                            </td>
                            <td class="px-6 py-3 text-slate-600">{{ $row['kelas_sekarang']->nama ?? '—' }}</td>

                            @if ($row['is_kelas_tertinggi'])
                                <td class="px-6 py-3">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="lulus[]" value="{{ $row['santri']->id }}"
                                               class="rounded border-ink-900/20 text-forest-700 focus:ring-forest-600/30" />
                                        Tandai Lulus
                                    </label>
                                </td>
                                <td class="px-6 py-3 text-slate-500">Kelas tertinggi — tidak ada tingkat berikutnya.</td>
                            @else
                                <td class="px-6 py-3">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="tinggal_kelas[]" value="{{ $row['santri']->id }}"
                                               class="rounded border-ink-900/20 text-forest-700 focus:ring-forest-600/30" />
                                        Tinggal Kelas
                                    </label>
                                </td>
                                <td class="px-6 py-3">
                                    <select name="promosi[{{ $row['santri']->id }}]"
                                            class="rounded-xl border border-ink-900/10 px-3 py-2 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20">
                                        <option value="">— Tidak naik kelas —</option>
                                        @foreach ($kelasList as $kelasOpsi)
                                            <option value="{{ $kelasOpsi->id }}" @selected($row['kelas_tujuan']?->id === $kelasOpsi->id)>
                                                {{ $kelasOpsi->nama }} ({{ $kelasOpsi->tingkat->nama ?? '—' }}, {{ $kelasOpsi->tahun_ajaran }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">Tidak ada santri aktif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 flex justify-end">
        <button type="submit"
                onclick="return confirm('Proses kenaikan kelas untuk seluruh santri yang dipilih? Tindakan ini akan mengubah data secara massal.')"
                class="rounded-xl bg-forest-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
            Proses Kenaikan Kelas
        </button>
    </div>
</form>

<script>
    // Selects left at "— Tidak naik kelas —" harus TIDAK ikut terkirim,
    // karena controller memvalidasi setiap promosi.* dengan exists:kelas,id
    // (nilai string kosong akan membuat seluruh request ditolak 422).
    document.querySelector('form[action="{{ route('sekretaris.kenaikan-kelas.proses') }}"]')
        ?.addEventListener('submit', function () {
            this.querySelectorAll('select[name^="promosi["]').forEach(function (select) {
                if (select.value === '') {
                    select.disabled = true;
                }
            });
        });
</script>
@endsection
