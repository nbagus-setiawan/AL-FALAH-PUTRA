@extends('layouts.sekretaris')

@section('title', $santri->nama_lengkap)
@section('subtitle', 'NIS '.$santri->nis)

@section('header-actions')
    <a href="{{ route('sekretaris.santri.edit', $santri) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-ink-900 hover:bg-ink-900/5">
        Ubah Data
    </a>
@endsection

@section('content')
@php
    $statusColor = ['Aktif' => 'emerald', 'Lulus' => 'gold', 'Boyong' => 'slate'];
    $warnaMap = ['Hijau' => 'emerald', 'Kuning' => 'amber', 'Merah' => 'rose'];
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-base font-semibold text-ink-900">Identitas</h2>
                <x-badge :color="$statusColor[$santri->status] ?? 'slate'">{{ $santri->status }}</x-badge>
            </div>
            <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                <div><dt class="text-slate-500">Nama Lengkap</dt><dd class="font-medium text-ink-900">{{ $santri->nama_lengkap }}</dd></div>
                <div><dt class="text-slate-500">Nama Panggilan</dt><dd class="text-ink-900">{{ $santri->nama_panggilan ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">NIS / NISN</dt><dd class="text-ink-900">{{ $santri->nis }} / {{ $santri->nisn ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Jenis Kelamin</dt><dd class="text-ink-900">{{ $santri->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd></div>
                <div><dt class="text-slate-500">Tempat, Tanggal Lahir</dt><dd class="text-ink-900">{{ $santri->tempat_lahir ?? '—' }}, {{ $santri->tanggal_lahir?->format('d-m-Y') ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Kelas</dt><dd class="text-ink-900">{{ $santri->kelas->nama ?? '—' }} @if($santri->kelas?->tingkat) ({{ $santri->kelas->tingkat->nama }}) @endif</dd></div>
            </dl>
        </div>

        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
            <h2 class="font-display text-base font-semibold text-ink-900">Alamat & Keluarga</h2>
            <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                <div class="sm:col-span-2"><dt class="text-slate-500">Alamat</dt><dd class="text-ink-900">{{ $santri->alamat ?? '—' }}, {{ $santri->kabupaten_kota ?? '' }} {{ $santri->provinsi ?? '' }}</dd></div>
                <div><dt class="text-slate-500">Ayah</dt><dd class="text-ink-900">{{ $santri->nama_ayah ?? '—' }} ({{ $santri->pekerjaan_ayah ?? '—' }})</dd></div>
                <div><dt class="text-slate-500">Ibu</dt><dd class="text-ink-900">{{ $santri->nama_ibu ?? '—' }} ({{ $santri->pekerjaan_ibu ?? '—' }})</dd></div>
                <div><dt class="text-slate-500">Wali</dt><dd class="text-ink-900">{{ $santri->nama_wali ?? '—' }} — {{ $santri->kontak_wali ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Kontak Darurat</dt><dd class="text-ink-900">{{ $santri->kontak_darurat_nama ?? '—' }} ({{ $santri->kontak_darurat_hubungan ?? '—' }}) — {{ $santri->kontak_darurat_telepon ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-2xl border border-rose-100 bg-rose-50/40 p-6 shadow-sm">
            <div class="flex items-center gap-2">
                <h2 class="font-display text-base font-semibold text-ink-900">Data Sensitif</h2>
                <x-badge color="rose">Hanya Sekretaris</x-badge>
            </div>
            <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                <div><dt class="text-slate-500">NIK</dt><dd class="text-ink-900">{{ $santri->nik ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Golongan Darah</dt><dd class="text-ink-900">{{ $santri->golongan_darah ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Riwayat Kesehatan</dt><dd class="text-ink-900">{{ $santri->riwayat_kesehatan ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Alergi</dt><dd class="text-ink-900">{{ $santri->alergi ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
            <h2 class="font-display text-base font-semibold text-ink-900">Prestasi</h2>
            <div class="mt-4 divide-y divide-ink-900/5">
                @forelse ($santri->prestasis as $prestasi)
                    <div class="py-3 text-sm">
                        <p class="font-medium text-ink-900">{{ $prestasi->nama_prestasi }}</p>
                        <p class="text-slate-500">{{ $prestasi->tingkat ?? '—' }} · {{ $prestasi->tanggal->format('d-m-Y') }}</p>
                    </div>
                @empty
                    <p class="py-3 text-sm text-slate-500">Belum ada catatan prestasi.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
            <h2 class="font-display text-base font-semibold text-ink-900">Riwayat Perizinan</h2>
            <div class="mt-4 divide-y divide-ink-900/5">
                @forelse ($santri->izins()->latest('tanggal_keluar')->take(10)->get() as $izin)
                    <div class="flex items-center justify-between py-3 text-sm">
                        <div>
                            <p class="font-medium text-ink-900">{{ $izin->jenis_izin }}</p>
                            <p class="text-slate-500">{{ $izin->tanggal_keluar->format('d-m-Y') }} – {{ $izin->rencana_kembali->format('d-m-Y') }}</p>
                        </div>
                        <a href="{{ route('sekretaris.izin.show', $izin) }}" class="text-forest-700 hover:underline">Detail</a>
                    </div>
                @empty
                    <p class="py-3 text-sm text-slate-500">Belum ada riwayat izin.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-forest-50 font-display text-2xl font-semibold text-forest-700">
                {{ strtoupper(mb_substr($santri->nama_lengkap, 0, 1)) }}
            </div>
            <p class="mt-3 font-display font-semibold text-ink-900">{{ $santri->nama_lengkap }}</p>
            <p class="text-sm text-slate-500">{{ $santri->nis }}</p>
        </div>

        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
            <h2 class="font-display text-base font-semibold text-ink-900">Poin Kedisiplinan</h2>
            <div class="mt-3 flex items-center justify-between">
                <span class="font-display text-2xl font-semibold text-ink-900">{{ $santri->total_poin }} poin</span>
                <x-badge :color="$warnaMap[$santri->warna_poin] ?? 'slate'">{{ $santri->warna_poin }}</x-badge>
            </div>
            <div class="mt-4 divide-y divide-ink-900/5">
                @forelse ($santri->pelanggarans()->latest('tanggal')->take(5)->get() as $p)
                    <div class="py-2.5 text-sm">
                        <p class="text-ink-900">{{ $p->jenis_pelanggaran }}</p>
                        <p class="text-xs text-slate-500">{{ $p->kategori }} · {{ $p->poin }} poin · {{ $p->tanggal->format('d-m-Y') }}</p>
                    </div>
                @empty
                    <p class="py-2.5 text-sm text-slate-500">Belum ada pelanggaran.</p>
                @endforelse
            </div>
            <form method="POST" action="{{ route('sekretaris.pelanggaran.reset-poin', $santri) }}" class="mt-3"
                  onsubmit="return confirm('Reset poin kedisiplinan {{ $santri->nama_lengkap }}?')">
                @csrf
                <input type="text" name="alasan" placeholder="Alasan reset (opsional)"
                       class="mb-2 block w-full rounded-xl border border-ink-900/10 px-3 py-2 text-xs shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20" />
                <button type="submit" class="w-full rounded-xl border border-ink-900/10 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-ink-900/5">
                    Reset Poin
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
            <h2 class="font-display text-base font-semibold text-ink-900">Asrama</h2>
            <p class="mt-2 text-sm text-ink-900">
                Saat ini: <span class="font-medium">{{ $santri->asramaSaatIni?->asrama->nama ?? 'Belum ditempatkan' }}</span>
            </p>

            <form method="POST" action="{{ route('sekretaris.santri.pindah-asrama', $santri) }}" class="mt-4 space-y-2">
                @csrf
                <select name="asrama_id" required
                        class="block w-full rounded-xl border border-ink-900/10 px-3 py-2 text-xs shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20">
                    <option value="">Pindahkan ke asrama...</option>
                    @foreach (\App\Models\Asrama::orderBy('nama')->get() as $asrama)
                        <option value="{{ $asrama->id }}">{{ $asrama->nama }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full rounded-xl bg-forest-800 px-3 py-2 text-xs font-semibold text-white hover:bg-forest-700">
                    Proses Pindah Asrama
                </button>
            </form>

            <div class="mt-4 divide-y divide-ink-900/5 border-t border-ink-900/5 pt-3">
                @foreach ($santri->riwayatAsrama as $riwayat)
                    <div class="py-2 text-xs text-slate-500">
                        {{ $riwayat->asrama->nama ?? '—' }} · {{ $riwayat->tanggal_masuk->format('d-m-Y') }}
                        – {{ $riwayat->tanggal_keluar?->format('d-m-Y') ?? 'sekarang' }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
