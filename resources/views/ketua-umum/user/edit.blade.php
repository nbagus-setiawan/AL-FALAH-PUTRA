@extends('layouts.ketua-umum')

@section('title', 'Ubah User')
@section('subtitle', $user->name)

@section('content')
@if ($user->id === auth()->id())
    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
        Anda sedang mengubah akun Anda sendiri yang aktif login — role tidak dapat diubah untuk mencegah lockout.
    </div>
@endif

<form method="POST" action="{{ route('ketua-umum.user.update', $user) }}" class="space-y-6">
    @csrf @method('PUT')
    <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
        @include('ketua-umum.user._form', ['user' => $user])
    </div>
    <div class="flex justify-end gap-3">
        <a href="{{ route('ketua-umum.user.index') }}" class="rounded-xl border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-ink-900/5">Batal</a>
        <button type="submit" class="rounded-xl bg-forest-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">Simpan Perubahan</button>
    </div>
</form>
@endsection
