@extends('layouts.ketua-umum')

@section('title', 'Manajemen User')
@section('subtitle', 'Kelola akun pengguna sistem.')

@section('header-actions')
    <a href="{{ route('ketua-umum.user.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-forest-700">
        <x-icon name="user-group" class="h-4 w-4" />
        Tambah User
    </a>
@endsection

@section('content')
@php
    $roleLabelMap = [
        'ketua_umum' => 'Ketua Umum',
        'sekretaris' => 'Sekretaris',
        'bendahara' => 'Bendahara',
    ];
    $roleColor = ['ketua_umum' => 'forest', 'sekretaris' => 'gold', 'bendahara' => 'amber'];
@endphp

<div class="rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Nama</th>
                    <th class="px-6 py-3 font-medium">Username</th>
                    <th class="px-6 py-3 font-medium">Role</th>
                    <th class="px-6 py-3 font-medium">Pengurus</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @forelse ($users as $user)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3 font-medium text-ink-900">{{ $user->name }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ '@'.$user->username }}</td>
                        <td class="px-6 py-3"><x-badge :color="$roleColor[$user->role] ?? 'slate'">{{ $roleLabelMap[$user->role] ?? $user->role }}</x-badge></td>
                        <td class="px-6 py-3 text-slate-600">{{ $user->pengurus->nama ?? '—' }}</td>
                        <td class="px-6 py-3">
                            <x-badge :color="$user->is_active ? 'emerald' : 'slate'">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</x-badge>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('ketua-umum.user.edit', $user) }}" class="text-sm font-medium text-forest-700 hover:underline">Ubah</a>
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('ketua-umum.user.destroy', $user) }}" class="inline"
                                      onsubmit="return confirm('Hapus akun {{ $user->username }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ml-3 text-sm font-medium text-rose-600 hover:underline">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-ink-900/5 px-6 py-4">{{ $users->links() }}</div>
</div>
@endsection
