@extends('layouts.app')

@section('judul', 'Daftar')

@section('isi')
<div class="mx-auto max-w-md py-6 sm:py-12">

    <div data-reveal class="text-center">
        <span aria-hidden="true"
              class="mx-auto grid h-12 w-12 place-items-center  bg-gradient-to-br
                     from-utama-200 to-utama-600 shadow-emas">
            <svg viewBox="0 0 24 24" class="h-6 w-6 text-dasar-950" fill="currentColor">
                <path d="M12 3c0 4.97 4.03 9 9 9-4.97 0-9 4.03-9 9 0-4.97-4.03-9-9-9 4.97 0 9-4.03 9-9z"/>
            </svg>
        </span>

        <h1 class="mt-5 font-judul text-3xl font-bold tracking-tight text-dasar-900">Buat akun</h1>
        <p class="mx-auto mt-2 max-w-sm text-sm leading-relaxed text-dasar-600">
            Profil akademikmu dipakai AI untuk menghitung seberapa cocok tiap peluang denganmu.
        </p>
    </div>

    <form method="POST" action="{{ route('daftar.simpan') }}" class="mt-8 space-y-4">
        @csrf

        {{-- Isian dipisah jadi dua kartu supaya pendaftaran tidak terasa
             seperti satu daftar panjang tanpa ujung. Mata butuh tahu ada
             berapa bagian yang harus dilewati. --}}

        <x-kartu datar data-reveal data-reveal-jeda="100" class="space-y-5">
            <p class="flex items-center gap-2 text-[0.68rem] font-semibold uppercase
                      tracking-[0.14em] text-utama-600">
                <span class="grid h-5 w-5 place-items-center  bg-utama-500/15
                             text-[0.65rem] ring-1 ring-inset ring-dasar-900">1</span>
                Akun
            </p>

            <x-isian name="name" label="Nama lengkap" required autofocus autocomplete="name" />

            <x-isian name="email" label="Email" type="email" required autocomplete="email" />

            <x-isian name="password" label="Kata sandi" type="password" required
                     autocomplete="new-password" petunjuk="Minimal 8 karakter." />

            <x-isian name="password_confirmation" label="Ulangi kata sandi" type="password"
                     required autocomplete="new-password" />
        </x-kartu>

        <x-kartu datar data-reveal data-reveal-jeda="180" class="space-y-5">
            <p class="flex items-center gap-2 text-[0.68rem] font-semibold uppercase
                      tracking-[0.14em] text-utama-600">
                <span class="grid h-5 w-5 place-items-center  bg-utama-500/15
                             text-[0.65rem] ring-1 ring-inset ring-dasar-900">2</span>
                Profil akademik
            </p>

            <x-isian name="universitas" label="Universitas" required autocomplete="organization" />

            <x-isian name="jurusan" label="Jurusan" required list="daftar-jurusan" />
            <datalist id="daftar-jurusan">
                @foreach ([
                    'Teknik Informatika', 'Sistem Informasi', 'Teknik Elektro',
                    'Desain Komunikasi Visual', 'Ilmu Komunikasi', 'Manajemen',
                    'Akuntansi', 'Psikologi',
                ] as $pilihanJurusan)
                    <option value="{{ $pilihanJurusan }}"></option>
                @endforeach
            </datalist>

            <x-pilihan name="semester" label="Semester" kosong="Pilih semester" required>
                @for ($i = 1; $i <= 14; $i++)
                    <option value="{{ $i }}" @selected(old('semester') == $i)>Semester {{ $i }}</option>
                @endfor
            </x-pilihan>
        </x-kartu>

        <div data-reveal data-reveal-jeda="240" class="pt-1">
            <x-tombol class="w-full">Buat akun</x-tombol>
        </div>
    </form>

    <p data-reveal data-reveal-jeda="300" class="mt-6 text-center text-sm text-dasar-600">
        Sudah punya akun?
        <a href="{{ route('masuk') }}"
           class="font-semibold text-utama-600 underline-offset-2 hover:underline">Masuk di sini</a>
    </p>

</div>
@endsection
