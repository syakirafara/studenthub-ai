@extends('layouts.app')

@section('judul', 'Profil Saya')

@section('isi')

@php
    $kelengkapan = $profil?->kelengkapan() ?? 0;
    $minatTerpilih = old('minat', $profil->minat ?? []);
    $skillTerpilih = old('skill', $profil->skill ?? []);
    $preferensi = old('preferensi', $profil->preferensi ?? []);
@endphp

<div class="mx-auto max-w-2xl">

    <h1 class="text-2xl font-semibold tracking-tight">Profil saya</h1>
    <p class="mt-1 text-sm text-slate-500">
        Makin lengkap profilmu, makin akurat skor kecocokan yang kami hitung.
    </p>

    {{-- Penanda kelengkapan --}}
    <div class="mt-6 rounded-lg border border-slate-200 bg-white p-4">
        <div class="flex items-center justify-between text-sm">
            <span class="font-medium text-slate-700">Kelengkapan profil</span>
            <span class="font-semibold {{ $kelengkapan === 100 ? 'text-sukses-700' : 'text-slate-700' }}">
                {{ $kelengkapan }}%
            </span>
        </div>

        <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-100"
             role="progressbar" aria-valuenow="{{ $kelengkapan }}" aria-valuemin="0" aria-valuemax="100"
             aria-label="Kelengkapan profil">
            <div class="h-full rounded-full transition-all {{ $kelengkapan === 100 ? 'bg-sukses-600' : 'bg-utama-600' }}"
                 style="width: {{ $kelengkapan }}%"></div>
        </div>

        @if ($kelengkapan < 100)
            <p class="mt-2 text-xs text-slate-500">
                Lengkapi minat, kemampuan, dan preferensimu supaya rekomendasi lebih tepat sasaran.
            </p>
        @endif
    </div>

    <form method="POST" action="{{ route('profil.update') }}" class="mt-6 space-y-8">
        @csrf
        @method('PUT')

        {{-- Data akademik --}}
        <section class="space-y-5">
            <h2 class="text-lg font-semibold">Data akademik</h2>

            <x-isian name="universitas" label="Universitas"
                     :value="$profil->universitas ?? ''" required autocomplete="organization" />

            <x-isian name="jurusan" label="Jurusan"
                     :value="$profil->jurusan ?? ''" required list="daftar-jurusan" />
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
                    <option value="{{ $i }}" @selected(old('semester', $profil->semester ?? null) == $i)>
                        Semester {{ $i }}
                    </option>
                @endfor
            </x-pilihan>
        </section>

        {{-- Minat --}}
        <section>
            <div class="flex items-baseline justify-between">
                <h2 class="text-lg font-semibold">Minat</h2>
                <span class="text-xs text-slate-500">pilih maksimal 3</span>
            </div>
            <p class="mt-1 text-sm text-slate-500">
                Bidang yang paling kamu incar. Ini yang dipakai untuk menyaring rekomendasi.
            </p>

            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                @foreach (App\Models\Profile::MINAT as $nilai => $teks)
                    <label class="flex cursor-pointer items-center gap-3 rounded-md border border-slate-200 bg-white px-3 py-2.5 text-sm transition hover:border-utama-300 has-[:checked]:border-utama-600 has-[:checked]:bg-utama-50">
                        <input type="checkbox" name="minat[]" value="{{ $nilai }}"
                               @checked(in_array($nilai, $minatTerpilih))
                               class="rounded border-slate-300 text-utama-600 focus:ring-utama-600">
                        {{ $teks }}
                    </label>
                @endforeach
            </div>

            @error('minat')
                <p class="mt-2 text-sm text-bahaya-600">{{ $message }}</p>
            @enderror
        </section>

        {{-- Kemampuan --}}
        <section>
            <h2 class="text-lg font-semibold">Kemampuan</h2>
            <p class="mt-1 text-sm text-slate-500">
                Yang sudah kamu kuasai, walau baru dasar. Dipakai untuk menilai apakah kamu memenuhi syarat.
            </p>

            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                @foreach (App\Models\Profile::SKILL as $nilai => $teks)
                    <label class="flex cursor-pointer items-center gap-3 rounded-md border border-slate-200 bg-white px-3 py-2.5 text-sm transition hover:border-utama-300 has-[:checked]:border-utama-600 has-[:checked]:bg-utama-50">
                        <input type="checkbox" name="skill[]" value="{{ $nilai }}"
                               @checked(in_array($nilai, $skillTerpilih))
                               class="rounded border-slate-300 text-utama-600 focus:ring-utama-600">
                        {{ $teks }}
                    </label>
                @endforeach
            </div>

            @error('skill')
                <p class="mt-2 text-sm text-bahaya-600">{{ $message }}</p>
            @enderror
        </section>

        {{-- Preferensi --}}
        <section class="space-y-5">
            <div>
                <h2 class="text-lg font-semibold">Preferensi</h2>
                <p class="mt-1 text-sm text-slate-500">Supaya kami tidak merekomendasikan yang tidak mungkin kamu ikuti.</p>
            </div>

            <x-pilihan name="preferensi[format]" label="Format kegiatan" :kosong="null">
                @foreach (['keduanya' => 'Online maupun offline', 'online' => 'Hanya online', 'offline' => 'Hanya offline'] as $nilai => $teks)
                    <option value="{{ $nilai }}" @selected(($preferensi['format'] ?? 'keduanya') === $nilai)>{{ $teks }}</option>
                @endforeach
            </x-pilihan>

            <x-pilihan name="preferensi[biaya]" label="Biaya pendaftaran" :kosong="null">
                @foreach (['keduanya' => 'Gratis maupun berbayar', 'gratis' => 'Hanya yang gratis'] as $nilai => $teks)
                    <option value="{{ $nilai }}" @selected(($preferensi['biaya'] ?? 'keduanya') === $nilai)>{{ $teks }}</option>
                @endforeach
            </x-pilihan>
        </section>

        <div class="flex gap-3 border-t border-slate-200 pt-6">
            <x-tombol>Simpan perubahan</x-tombol>
            <x-tombol :href="route('beranda')" jenis="kedua">Batal</x-tombol>
        </div>

    </form>
</div>

@endsection
