@extends('layouts.app')

@section('judul', 'Masuk')

@section('isi')
<div class="mx-auto max-w-md">

    <h1 class="text-2xl font-semibold tracking-tight">Masuk</h1>
    <p class="mt-1 text-sm text-slate-500">Lanjutkan mencari peluang yang cocok untukmu.</p>

    <form method="POST" action="{{ route('masuk.proses') }}" class="mt-6 space-y-5">
        @csrf

        <x-isian name="email" label="Email" type="email" required autofocus autocomplete="email" />

        <x-isian name="password" label="Kata sandi" type="password" required autocomplete="current-password" />

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="ingat" value="1" @checked(old('ingat'))
                   class="rounded border-slate-300 text-utama-600 focus:ring-utama-600">
            Ingat saya di perangkat ini
        </label>

        <x-tombol class="w-full">Masuk</x-tombol>

        <p class="text-center text-sm text-slate-500">
            Belum punya akun?
            <a href="{{ route('daftar') }}" class="font-medium text-utama-600 hover:underline">Daftar di sini</a>
        </p>

    </form>
</div>
@endsection
