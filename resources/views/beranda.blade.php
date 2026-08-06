@extends('layouts.app')

@section('judul', 'Beranda')

@section('isi')

@php
    $profil = auth()->user()->profile;
    $kelengkapan = $profil?->kelengkapan() ?? 0;
@endphp

<h1 class="text-2xl font-semibold tracking-tight">
    Halo, {{ auth()->user()->name }}
</h1>

@if ($profil)
    <p class="mt-1 text-sm text-slate-500">
        {{ $profil->jurusan }} &middot;
        Semester {{ $profil->semester }} &middot;
        {{ $profil->universitas }}
    </p>
@endif

@if ($kelengkapan < 100)
    <x-kartu class="mt-6 border-waspada-200 bg-waspada-50">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-waspada-800">
                    Profilmu baru {{ $kelengkapan }}% lengkap
                </p>
                <p class="mt-0.5 text-sm text-waspada-700">
                    Skor kecocokan hanya seakurat data yang tersedia. Lengkapi minat dan kemampuanmu dulu.
                </p>
            </div>
            <x-tombol :href="route('profil.edit')">Lengkapi profil</x-tombol>
        </div>
    </x-kartu>
@else
    <x-kartu class="mt-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-3">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400">Minat</p>
                    <div class="mt-1 flex flex-wrap gap-1.5">
                        @foreach ($profil->namaMinat() as $minat)
                            <x-lencana warna="utama">{{ $minat }}</x-lencana>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400">Kemampuan</p>
                    <div class="mt-1 flex flex-wrap gap-1.5">
                        @foreach ($profil->namaSkill() as $skill)
                            <x-lencana>{{ $skill }}</x-lencana>
                        @endforeach
                    </div>
                </div>
            </div>

            <x-tombol :href="route('profil.edit')" jenis="kedua">Ubah profil</x-tombol>
        </div>
    </x-kartu>
@endif

<x-kartu class="mt-6 border-dashed p-8 text-center">
    <p class="text-sm text-slate-500">
        Rekomendasi peluang yang cocok denganmu akan tampil di sini.
    </p>
    <div class="mt-4">
        <x-tombol :href="route('peluang.index')" jenis="kedua">Jelajahi katalog dulu</x-tombol>
    </div>
</x-kartu>

@endsection
