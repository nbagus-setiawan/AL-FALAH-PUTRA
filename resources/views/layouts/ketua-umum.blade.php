@php
    $roleLabel = 'Ketua Umum';

    $navGroups = [
        [
            'label' => null,
            'items' => [
                ['label' => 'Dashboard', 'route' => 'ketua-umum.dashboard', 'icon' => 'home'],
            ],
        ],
        [
            'label' => 'Persetujuan',
            'items' => [
                ['label' => 'Persetujuan Izin', 'route' => 'ketua-umum.izin.index', 'pattern' => 'ketua-umum.izin.*', 'icon' => 'calendar'],
                ['label' => 'Persetujuan Surat', 'route' => 'ketua-umum.surat.index', 'pattern' => 'ketua-umum.surat.*', 'icon' => 'mail'],
                ['label' => 'Persetujuan RAPB', 'route' => 'ketua-umum.rapb.index', 'pattern' => 'ketua-umum.rapb.*', 'icon' => 'banknote'],
            ],
        ],
        [
            'label' => 'Administrasi',
            'items' => [
                ['label' => 'Manajemen User', 'route' => 'ketua-umum.user.index', 'pattern' => 'ketua-umum.user.*', 'icon' => 'user-group'],
                ['label' => 'Log Aktivitas', 'route' => 'ketua-umum.activity-log.index', 'pattern' => 'ketua-umum.activity-log.*', 'icon' => 'clock'],
            ],
        ],
    ];
@endphp
@extends('layouts.app')
