@extends('layouts.app')

@section('judul', 'Masuk')

@section('isi')
<div class="mx-auto flex max-w-md flex-col justify-center py-6 sm:py-14">

    <div data-reveal class="text-center">
        <span aria-hidden="true"
              class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br
                     from-utama-200 to-utama-600 shadow-emas">
            <svg viewBox="0 0 24 24" class="h-6 w-6 text-dasar-950" fill="currentColor">
                <path d="M12 3c0 4.97 4.03 9 9 9-4.97 0-9 4.03-9 9 0-4.97-4.03-9-9-9 4.97 0 9-4.03 9-9z"/>
            </svg>
        </span>

        <h1 class="mt-5 font-judul text-3xl font-bold tracking-tight text-white">Selamat datang kembali</h1>
        <p class="mt-2 text-sm text-slate-400">Lanjutkan mencari peluang yang cocok untukmu.</p>
    </div>

    <x-kartu datar data-reveal data-reveal-jeda="120" class="mt-8">
        <form method="POST" action="{{ route('masuk.proses') }}" class="space-y-5">
            @csrf

            <x-isian name="email" label="Email" type="email" required autofocus autocomplete="email" />

            <x-isian name="password" label="Kata sandi" type="password" required autocomplete="current-password" />

            <label class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-300">
                {{-- accent-color di CSS dasar yang mewarnai centangnya. Kelas
                     seperti text-utama-300 tidak berpengaruh pada kotak
                     centang bawaan. --}}
                <input type="checkbox" name="ingat" value="1" @checked(old('ingat'))
                       class="h-4 w-4 cursor-pointer rounded border-white/20">
                Ingat saya di perangkat ini
            </label>

            <x-tombol class="w-full">Masuk</x-tombol>
        </form>
    </x-kartu>

    <p data-reveal data-reveal-jeda="200" class="mt-6 text-center text-sm text-slate-400">
        Belum punya akun?
        <a href="{{ route('daftar') }}"
           class="font-semibold text-utama-300 underline-offset-2 hover:underline">Daftar di sini</a>
    </p>

</div>
@endsection
