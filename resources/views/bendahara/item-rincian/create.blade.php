@extends('layouts.bendahara')

@section('title', 'Tambah Item Rincian')
@section('subtitle', $subKategori->kategori->nama.' / '.$subKategori->nama)

@section('content')
<form method="POST" action="{{ route('bendahara.item-rincian.store') }}" class="space-y-6">
    @csrf
    <input type="hidden" name="sub_kategori_rapb_id" value="{{ $subKategori->id }}" />

    <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-field label="Nama Item" name="nama" required class="sm:col-span-2">
                <x-input name="nama" />
            </x-field>
            <x-field label="Jumlah Rencana (Total)" name="jumlah_rencana" required
                      hint="Jika mengisi rincian per bulan di bawah, nilai ini akan dihitung ulang otomatis dari total rincian bulanan.">
                <x-input type="number" name="jumlah_rencana" step="0.01" min="0" />
            </x-field>
            <x-field label="Keterangan" name="keterangan">
                <x-input name="keterangan" />
            </x-field>
        </div>
    </div>

    <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        <h2 class="font-display text-base font-semibold text-ink-900">Rincian Per Bulan <span class="text-sm font-normal text-slate-500">(opsional)</span></h2>
        <p class="mt-1 text-sm text-slate-500">Kosongkan seluruhnya jika tidak perlu memecah rencana per bulan.</p>
        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            @php
                $bulanLabel = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
            @endphp
            @foreach ($bulanLabel as $angka => $label)
                <div>
                    <label class="block text-xs font-medium text-slate-500">{{ $label }}</label>
                    <input type="number" name="rencana_bulanan[{{ $angka }}]" step="0.01" min="0"
                           class="mt-1 block w-full rounded-lg border border-ink-900/10 px-3 py-2 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20" />
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('bendahara.kategori.show', $subKategori->kategori) }}" class="rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-ink-900/5">Batal</a>
        <button type="submit" class="rounded-xl bg-forest-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">Simpan</button>
    </div>
</form>
@endsection
