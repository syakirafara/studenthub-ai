@extends('layouts.app')

@section('judul', 'Daftar')

@section('isi')
<div class="mx-auto max-w-md">

    <h1 class="text-2xl font-semibold tracking-tight">Buat akun</h1>
    <p class="mt-1 text-sm text-slate-500">
        Isi profil akademikmu supaya kami bisa menghitung kecocokanmu dengan setiap peluang.
    </p>

    <form method="POST" action="{{ route('daftar.simpan') }}" class="mt-6 space-y-5">
        @csrf

        <x-isian name="name" label="Nama lengkap" required autofocus autocomplete="name" />

        <x-isian name="email" label="Email" type="email" required autocomplete="email" />

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

        <x-isian name="password" label="Kata sandi" type="password" required
                 autocomplete="new-password" petunjuk="Minimal 8 karakter." />

        <x-isian name="password_confirmation" label="Ulangi kata sandi" type="password"
                 required autocomplete="new-password" />

        <x-tombol class="w-full">Daftar</x-tombol>

        <p class="text-center text-sm text-slate-500">
            Sudah punya akun?
            <a href="{{ route('masuk') }}" class="font-medium text-utama-600 hover:underline">Masuk di sini</a>
        </p>

    </form>
</div>
@endsection
