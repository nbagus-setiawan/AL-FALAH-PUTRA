@php
    $roleLabel = 'Bendahara';

    $navGroups = [
        [
            'label' => null,
            'items' => [
                ['label' => 'Dashboard', 'route' => 'bendahara.dashboard', 'icon' => 'home'],
            ],
        ],
        [
            'label' => 'RAPB',
            'items' => [
                ['label' => 'Tahun Anggaran', 'route' => 'bendahara.tahun-anggaran.index', 'pattern' => 'bendahara.tahun-anggaran.*', 'icon' => 'banknote'],
            ],
        ],
    ];
@endphp
@extends('layouts.app')
