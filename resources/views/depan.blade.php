@extends('layouts.app')

@section('judul', 'Semua peluang mahasiswa dalam satu tempat')

@section('isi')
<div class="mx-auto max-w-2xl py-12 text-center">

    <h1 class="text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">
        Semua peluang mahasiswa, dalam satu tempat
    </h1>

    <p class="mt-4 text-slate-600">
        Lomba, beasiswa, dan magang dikumpulkan bersama, lalu dibaca AI supaya kamu tahu
        mana yang benar-benar cocok denganmu sebelum mendaftar.
    </p>

    @guest
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <x-tombol :href="route('daftar')">Mulai sekarang</x-tombol>
            <x-tombol :href="route('masuk')" jenis="kedua">Masuk</x-tombol>
        </div>
    @endguest

    @auth
        <div class="mt-8">
            <x-tombol :href="route('beranda')">Buka beranda</x-tombol>
        </div>
    @endauth

</div>
@endsection
