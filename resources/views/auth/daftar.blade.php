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

        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Nama lengkap</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}"
                   required autofocus autocomplete="name"
                   @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                   class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600">
            @error('name')
                <p id="name-error" class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}"
                   required autocomplete="email"
                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                   class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600">
            @error('email')
                <p id="email-error" class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="universitas" class="block text-sm font-medium text-slate-700">Universitas</label>
            <input id="universitas" name="universitas" type="text" value="{{ old('universitas') }}"
                   required autocomplete="organization"
                   @error('universitas') aria-invalid="true" aria-describedby="universitas-error" @enderror
                   class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600">
            @error('universitas')
                <p id="universitas-error" class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="jurusan" class="block text-sm font-medium text-slate-700">Jurusan</label>
            <input id="jurusan" name="jurusan" type="text" value="{{ old('jurusan') }}"
                   required list="daftar-jurusan"
                   @error('jurusan') aria-invalid="true" aria-describedby="jurusan-error" @enderror
                   class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600">
            <datalist id="daftar-jurusan">
                <option value="Teknik Informatika"></option>
                <option value="Sistem Informasi"></option>
                <option value="Teknik Elektro"></option>
                <option value="Desain Komunikasi Visual"></option>
                <option value="Ilmu Komunikasi"></option>
                <option value="Manajemen"></option>
                <option value="Akuntansi"></option>
                <option value="Psikologi"></option>
            </datalist>
            @error('jurusan')
                <p id="jurusan-error" class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="semester" class="block text-sm font-medium text-slate-700">Semester</label>
            <select id="semester" name="semester" required
                    @error('semester') aria-invalid="true" aria-describedby="semester-error" @enderror
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600">
                <option value="">Pilih semester</option>
                @for ($i = 1; $i <= 14; $i++)
                    <option value="{{ $i }}" @selected(old('semester') == $i)>Semester {{ $i }}</option>
                @endfor
            </select>
            @error('semester')
                <p id="semester-error" class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700">Kata sandi</label>
            <input id="password" name="password" type="password"
                   required autocomplete="new-password"
                   @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                   class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600">
            <p class="mt-1 text-xs text-slate-500">Minimal 8 karakter.</p>
            @error('password')
                <p id="password-error" class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Ulangi kata sandi</label>
            <input id="password_confirmation" name="password_confirmation" type="password"
                   required autocomplete="new-password"
                   class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600">
        </div>

        <button type="submit"
                class="w-full rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
            Daftar
        </button>

        <p class="text-center text-sm text-slate-500">
            Sudah punya akun?
            <a href="{{ route('masuk') }}" class="font-medium text-indigo-600 hover:underline">Masuk di sini</a>
        </p>

    </form>
</div>
@endsection
