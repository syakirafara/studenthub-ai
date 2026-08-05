@extends('layouts.app')

@section('judul', 'Masuk')

@section('isi')
<div class="mx-auto max-w-md">

    <h1 class="text-2xl font-semibold tracking-tight">Masuk</h1>
    <p class="mt-1 text-sm text-slate-500">Lanjutkan mencari peluang yang cocok untukmu.</p>

    <form method="POST" action="{{ route('masuk.proses') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}"
                   required autofocus autocomplete="email"
                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                   class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600">
            @error('email')
                <p id="email-error" class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700">Kata sandi</label>
            <input id="password" name="password" type="password"
                   required autocomplete="current-password"
                   class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600">
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="ingat" value="1" @checked(old('ingat'))
                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-600">
            Ingat saya di perangkat ini
        </label>

        <button type="submit"
                class="w-full rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
            Masuk
        </button>

        <p class="text-center text-sm text-slate-500">
            Belum punya akun?
            <a href="{{ route('daftar') }}" class="font-medium text-indigo-600 hover:underline">Daftar di sini</a>
        </p>

    </form>
</div>
@endsection
