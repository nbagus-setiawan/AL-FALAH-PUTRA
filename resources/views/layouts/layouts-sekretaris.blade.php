@php
    $roleLabel = 'Sekretaris';

    $navGroups = [
        [
            'label' => null,
            'items' => [
                ['label' => 'Dashboard', 'route' => 'sekretaris.dashboard', 'icon' => 'home'],
            ],
        ],
        [
            'label' => 'Data Master',
            'items' => [
                ['label' => 'Data Santri', 'route' => 'sekretaris.santri.index', 'pattern' => 'sekretaris.santri.*', 'icon' => 'users'],
                ['label' => 'Data Pengurus', 'route' => 'sekretaris.pengurus.index', 'pattern' => 'sekretaris.pengurus.*', 'icon' => 'id-badge'],
                ['label' => 'Kelas', 'route' => 'sekretaris.kelas.index', 'pattern' => 'sekretaris.kelas.*', 'icon' => 'academic-cap'],
                ['label' => 'Tingkat', 'route' => 'sekretaris.tingkat.index', 'pattern' => 'sekretaris.tingkat.*', 'icon' => 'layers'],
                ['label' => 'Asrama', 'route' => 'sekretaris.asrama.index', 'pattern' => 'sekretaris.asrama.*', 'icon' => 'building'],
                ['label' => 'Kenaikan Kelas', 'route' => 'sekretaris.kenaikan-kelas.preview', 'pattern' => 'sekretaris.kenaikan-kelas.*', 'icon' => 'trending-up'],
            ],
        ],
        [
            'label' => 'Persuratan',
            'items' => [
                ['label' => 'Jenis Surat', 'route' => 'sekretaris.jenis-surat.index', 'pattern' => 'sekretaris.jenis-surat.*', 'icon' => 'tag'],
                ['label' => 'Surat Keluar', 'route' => 'sekretaris.surat.index', 'pattern' => 'sekretaris.surat.*', 'icon' => 'mail'],
            ],
        ],
        [
            'label' => 'Kedisiplinan & Izin',
            'items' => [
                ['label' => 'Perizinan', 'route' => 'sekretaris.izin.index', 'pattern' => 'sekretaris.izin.*', 'icon' => 'calendar'],
                ['label' => 'Poin Kedisiplinan', 'route' => 'sekretaris.pelanggaran.index', 'pattern' => 'sekretaris.pelanggaran.*', 'icon' => 'alert-triangle'],
            ],
        ],
    ];
@endphp
@extends('layouts.app')
