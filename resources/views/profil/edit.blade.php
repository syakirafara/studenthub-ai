@extends('layouts.app')

@section('judul', 'Profil Saya')

@section('isi')

@php
    $kelengkapan = $profil?->kelengkapan() ?? 0;
    $minatTerpilih = old('minat', $profil->minat ?? []);
    $skillTerpilih = old('skill', $profil->skill ?? []);
    $preferensi = old('preferensi', $profil->preferensi ?? []);

    $penuh = $kelengkapan === 100;

    // Kelas kotak pilihan dipakai di dua tempat -- ditaruh di peubah supaya
    // kalau gayanya berubah, tidak ada satu pun yang tertinggal.
    $kotakPilihan = 'flex cursor-pointer items-center gap-3  border border-dasar-900
                     bg-dasar-100 px-3.5 py-2.5 text-sm text-dasar-800 transition-all duration-300
                     hover:border-dasar-900 hover:bg-dasar-200
                     has-[:checked]:border-dasar-900 has-[:checked]:bg-utama-200
                     has-[:checked]:text-utama-800';
@endphp

<div class="mx-auto max-w-2xl">

    <div data-reveal>
        <h1 class="font-judul text-3xl font-bold tracking-tight text-dasar-900">Profil saya</h1>
        <p class="mt-1.5 text-sm text-dasar-600">
            Makin lengkap profilmu, makin akurat skor kecocokan yang dihitung AI.
        </p>
    </div>

    {{-- ---------- Penanda kelengkapan ---------- --}}
    <x-kartu datar data-reveal data-reveal-jeda="80" class="mt-6">
        <div class="flex items-center justify-between gap-3 text-sm">
            <span class="font-medium text-dasar-800">Kelengkapan profil</span>
            <span class="font-judul text-lg font-bold {{ $penuh ? 'text-sukses-600' : 'text-utama-600' }}">
                {{ $kelengkapan }}%
            </span>
        </div>

        <div class="mt-2.5 h-2 w-full overflow-hidden  bg-dasar-200"
             role="progressbar" aria-valuenow="{{ $kelengkapan }}" aria-valuemin="0" aria-valuemax="100"
             aria-label="Kelengkapan profil">
            <div data-batang="{{ $kelengkapan }}"
                 class="h-full  bg-gradient-to-r
                        {{ $penuh ? 'from-sukses-600 to-sukses-400' : 'from-utama-600 to-utama-400' }}"></div>
        </div>

        <p class="mt-2.5 text-xs leading-relaxed {{ $penuh ? 'text-sukses-600' : 'text-dasar-600' }}">
            @if ($penuh)
                Profilmu sudah lengkap. Skor kecocokan akan dihitung dengan data paling utuh.
            @else
                Lengkapi minat, kemampuan, dan preferensimu supaya rekomendasi lebih tepat sasaran.
            @endif
        </p>
    </x-kartu>

    <form method="POST" action="{{ route('profil.update') }}" class="mt-4 space-y-4">
        @csrf
        @method('PUT')

        {{-- ---------- Data akademik ---------- --}}
        <x-kartu datar data-reveal data-reveal-jeda="140" class="space-y-5">
            <h2 class="font-judul text-lg font-semibold text-dasar-900">Data akademik</h2>

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
        </x-kartu>

        {{-- ---------- Minat ---------- --}}
        <x-kartu datar data-reveal data-reveal-jeda="200">
            <div class="flex items-baseline justify-between gap-3">
                <h2 class="font-judul text-lg font-semibold text-dasar-900">Minat</h2>
                <span class="shrink-0  bg-dasar-100 px-2 py-0.5 text-[0.68rem] text-dasar-600">
                    maksimal 3
                </span>
            </div>

            <p class="mt-1.5 text-sm leading-relaxed text-dasar-600">
                Bidang yang paling kamu incar. Ini yang dipakai untuk menyaring rekomendasi.
            </p>

            <div class="mt-4 grid gap-2 sm:grid-cols-2">
                @foreach (App\Models\Profile::MINAT as $nilai => $teks)
                    <label class="{{ $kotakPilihan }}">
                        <input type="checkbox" name="minat[]" value="{{ $nilai }}"
                               @checked(in_array($nilai, $minatTerpilih))
                               class="h-4 w-4 shrink-0 cursor-pointer  border-dasar-900">
                        {{ $teks }}
                    </label>
                @endforeach
            </div>

            @error('minat')
                <p class="mt-3 text-sm text-bahaya-600">{{ $message }}</p>
            @enderror
        </x-kartu>

        {{-- ---------- Kemampuan ---------- --}}
        <x-kartu datar data-reveal data-reveal-jeda="260">
            <h2 class="font-judul text-lg font-semibold text-dasar-900">Kemampuan</h2>

            <p class="mt-1.5 text-sm leading-relaxed text-dasar-600">
                Yang sudah kamu kuasai, walau baru dasar. Dipakai untuk menilai apakah kamu memenuhi syarat.
            </p>

            <div class="mt-4 grid gap-2 sm:grid-cols-2">
                @foreach (App\Models\Profile::SKILL as $nilai => $teks)
                    <label class="{{ $kotakPilihan }}">
                        <input type="checkbox" name="skill[]" value="{{ $nilai }}"
                               @checked(in_array($nilai, $skillTerpilih))
                               class="h-4 w-4 shrink-0 cursor-pointer  border-dasar-900">
                        {{ $teks }}
                    </label>
                @endforeach
            </div>

            @error('skill')
                <p class="mt-3 text-sm text-bahaya-600">{{ $message }}</p>
            @enderror
        </x-kartu>

        {{-- ---------- Preferensi ---------- --}}
        <x-kartu datar data-reveal data-reveal-jeda="320" class="space-y-5">
            <div>
                <h2 class="font-judul text-lg font-semibold text-dasar-900">Preferensi</h2>
                <p class="mt-1.5 text-sm leading-relaxed text-dasar-600">
                    Supaya AI tidak merekomendasikan yang tidak mungkin kamu ikuti.
                </p>
            </div>

            <x-pilihan name="preferensi[format]" label="Format kegiatan" :kosong="null">
                @foreach ([
                    'keduanya' => 'Online maupun offline',
                    'online' => 'Hanya online',
                    'offline' => 'Hanya offline',
                ] as $nilai => $teks)
                    <option value="{{ $nilai }}" @selected(($preferensi['format'] ?? 'keduanya') === $nilai)>{{ $teks }}</option>
                @endforeach
            </x-pilihan>

            <x-pilihan name="preferensi[biaya]" label="Biaya pendaftaran" :kosong="null">
                @foreach ([
                    'keduanya' => 'Gratis maupun berbayar',
                    'gratis' => 'Hanya yang gratis',
                ] as $nilai => $teks)
                    <option value="{{ $nilai }}" @selected(($preferensi['biaya'] ?? 'keduanya') === $nilai)>{{ $teks }}</option>
                @endforeach
            </x-pilihan>
        </x-kartu>

        <div data-reveal data-reveal-jeda="380" class="flex gap-3 pt-2">
            <x-tombol>Simpan perubahan</x-tombol>
            <x-tombol :href="route('beranda')" jenis="kedua">Batal</x-tombol>
        </div>

    </form>
</div>

@endsection
