@extends('layouts.sekretaris')

@section('title', 'Tambah Asrama')

@section('content')
<form method="POST" action="{{ route('sekretaris.asrama.store') }}" class="space-y-6">
    @csrf
    <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        @include('sekretaris.asrama._form', ['asrama' => null])
    </div>
    <div class="flex justify-end gap-3">
        <a href="{{ route('sekretaris.asrama.index') }}" class="rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-ink-900/5">Batal</a>
        <button type="submit" class="rounded-xl bg-forest-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">Simpan</button>
    </div>
</form>
@endsection
