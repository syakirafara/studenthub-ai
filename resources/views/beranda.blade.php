@extends('layouts.app')

@section('judul', 'Beranda')

@section('isi')
<h1 class="text-2xl font-semibold tracking-tight">
    Halo, {{ auth()->user()->name }}
</h1>

@if (auth()->user()->profile)
    <p class="mt-1 text-sm text-slate-500">
        {{ auth()->user()->profile->jurusan }} &middot;
        Semester {{ auth()->user()->profile->semester }} &middot;
        {{ auth()->user()->profile->universitas }}
    </p>
@endif

<x-kartu class="mt-8 border-dashed p-8 text-center">
    <p class="text-sm text-slate-500">
        Katalog peluang dan rekomendasi akan tampil di sini.
    </p>
</x-kartu>
@endsection
