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
            <a href="{{ route('daftar') }}"
               class="rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-700">
                Mulai sekarang
            </a>
            <a href="{{ route('masuk') }}"
               class="rounded-md border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium hover:bg-slate-50">
                Masuk
            </a>
        </div>
    @endguest

    @auth
        <div class="mt-8">
            <a href="{{ route('beranda') }}"
               class="rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-700">
                Buka beranda
            </a>
        </div>
    @endauth

</div>
@endsection
