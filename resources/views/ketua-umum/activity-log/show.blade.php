@extends('layouts.ketua-umum')

@php($log = $activityLog)

@section('title', 'Detail Log Aktivitas')
@section('subtitle', $log->description ?? '')

@section('content')
@php
    $actionColor = ['create' => 'emerald', 'update' => 'amber', 'delete' => 'rose', 'approve' => 'forest', 'reject' => 'rose'];
@endphp

<div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <h2 class="font-display text-base font-semibold text-ink-900">Ringkasan</h2>
        <x-badge :color="$actionColor[$log->action] ?? 'slate'">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</x-badge>
    </div>
    <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
        <div><dt class="text-slate-500">Waktu</dt><dd class="text-ink-900">{{ $log->created_at->format('d-m-Y H:i:s') }}</dd></div>
        <div><dt class="text-slate-500">User</dt><dd class="text-ink-900">{{ $log->user->name ?? 'Sistem' }}</dd></div>
        <div><dt class="text-slate-500">Tipe Data</dt><dd class="text-ink-900">{{ class_basename($log->subject_type) }}</dd></div>
        <div><dt class="text-slate-500">ID Data</dt><dd class="text-ink-900">{{ $log->subject_id ?? '—' }}</dd></div>
        <div><dt class="text-slate-500">Alamat IP</dt><dd class="text-ink-900">{{ $log->ip_address ?? '—' }}</dd></div>
        <div class="sm:col-span-2"><dt class="text-slate-500">Deskripsi</dt><dd class="text-ink-900">{{ $log->description }}</dd></div>
    </dl>
</div>

@if ($log->data_sebelum || $log->data_sesudah)
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
            <h2 class="font-display text-base font-semibold text-ink-900">Data Sebelum</h2>
            <pre class="mt-3 overflow-x-auto rounded-xl bg-cream-50/80 p-4 text-xs text-ink-900">{{ $log->data_sebelum ? json_encode($log->data_sebelum, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '—' }}</pre>
        </div>
        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
            <h2 class="font-display text-base font-semibold text-ink-900">Data Sesudah</h2>
            <pre class="mt-3 overflow-x-auto rounded-xl bg-cream-50/80 p-4 text-xs text-ink-900">{{ $log->data_sesudah ? json_encode($log->data_sesudah, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '—' }}</pre>
        </div>
    </div>
@endif

<div class="mt-6">
    <a href="{{ route('ketua-umum.activity-log.index') }}" class="text-sm font-medium text-forest-700 hover:underline">← Kembali ke daftar log</a>
</div>
@endsection
