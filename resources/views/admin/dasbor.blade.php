@extends('layouts.app')

@section('judul', 'Dasbor Admin')

@section('isi')
<h1 class="text-2xl font-semibold tracking-tight">Dasbor Admin</h1>
<p class="mt-1 text-sm text-slate-500">
    Masuk sebagai {{ auth()->user()->name }}
</p>

<div class="mt-8 rounded-lg border border-dashed border-slate-300 bg-white p-8 text-center">
    <p class="text-sm text-slate-500">
        Antrean verifikasi peluang akan tampil di sini.
    </p>
</div>
@endsection
