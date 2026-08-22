@extends('layouts.ketua-umum')

@section('title', 'Log Aktivitas')
@section('subtitle', 'Audit trail seluruh aksi penting di sistem.')

@section('content')
<div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
    <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:grid-cols-5">
        <select name="user_id" class="rounded-xl border border-ink-900/10 px-3.5 py-2.5 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20">
            <option value="">Semua User</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>

        <select name="action" class="rounded-xl border border-ink-900/10 px-3.5 py-2.5 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20">
            <option value="">Semua Aksi</option>
            @foreach ($availableActions as $action)
                <option value="{{ $action }}" @selected(request('action') === $action)>{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
            @endforeach
        </select>

        <input type="text" name="subject_type" value="{{ request('subject_type') }}" placeholder="Tipe data (mis. Santri)"
               class="rounded-xl border border-ink-900/10 px-3.5 py-2.5 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20" />

        <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
               class="rounded-xl border border-ink-900/10 px-3.5 py-2.5 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20" />

        <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
               class="rounded-xl border border-ink-900/10 px-3.5 py-2.5 text-sm shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20" />

        <div class="sm:col-span-3 lg:col-span-5 flex justify-end gap-2">
            <a href="{{ route('ketua-umum.activity-log.index') }}" class="rounded-xl border border-ink-900/10 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-ink-900/5">Reset</a>
            <button type="submit" class="rounded-xl bg-forest-800 px-4 py-2 text-sm font-semibold text-white hover:bg-forest-700">Filter</button>
        </div>
    </form>
</div>

<div class="mt-6 rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-ink-900/5 text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Waktu</th>
                    <th class="px-6 py-3 font-medium">User</th>
                    <th class="px-6 py-3 font-medium">Aksi</th>
                    <th class="px-6 py-3 font-medium">Data</th>
                    <th class="px-6 py-3 font-medium">Deskripsi</th>
                    <th class="px-6 py-3 font-medium text-right">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @php
                    $actionColor = [
                        'create' => 'emerald', 'update' => 'amber', 'delete' => 'rose',
                        'approve' => 'forest', 'reject' => 'rose',
                    ];
                @endphp
                @forelse ($logs as $log)
                    <tr class="hover:bg-cream-50/60">
                        <td class="px-6 py-3 whitespace-nowrap text-slate-600">{{ $log->created_at->format('d-m-Y H:i') }}</td>
                        <td class="px-6 py-3 text-ink-900">{{ $log->user->name ?? 'Sistem' }}</td>
                        <td class="px-6 py-3">
                            <x-badge :color="$actionColor[$log->action] ?? 'slate'">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</x-badge>
                        </td>
                        <td class="px-6 py-3 text-slate-600">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</td>
                        <td class="px-6 py-3 max-w-sm text-slate-600"><span class="line-clamp-1">{{ $log->description }}</span></td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('ketua-umum.activity-log.show', $log) }}" class="text-sm font-medium text-forest-700 hover:underline">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500">Tidak ada log yang cocok dengan filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-ink-900/5 px-6 py-4">{{ $logs->withQueryString()->links() }}</div>
</div>
@endsection
