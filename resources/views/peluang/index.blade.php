@extends('layouts.app')

@section('judul', 'Katalog Peluang')

@section('isi')

@php
    $kelasIsian = 'w-full  border border-dasar-900 bg-dasar-100 px-3 py-2 text-sm text-dasar-900
                   transition-colors duration-300 placeholder:text-dasar-500 hover:border-dasar-900
                   focus:border-utama-400 focus:bg-dasar-50 focus:outline-none ';
@endphp

<div data-reveal>
    <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-utama-600">
        Sudah diverifikasi admin
    </p>
    <h1 class="mt-1.5 font-judul text-3xl font-bold tracking-tight text-dasar-900">Katalog peluang</h1>
    <p class="mt-1.5 text-sm text-dasar-600">
        Lomba, beasiswa, dan magang yang dikumpulkan mahasiswa, dibaca AI, lalu diperiksa manusia.
    </p>
</div>

{{-- Memakai kelas kaca langsung, bukan komponen x-kartu, karena komponen itu
     selalu menghasilkan <div> sedangkan yang dibutuhkan di sini <form>. --}}
<form method="GET" action="{{ route('peluang.index') }}" data-reveal
      class="kaca kaca-tepi relative mt-6 overflow-hidden  p-5 shadow-naik">

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">

        <div class="lg:col-span-2">
            <label for="cari" class="sr-only">Cari peluang</label>
            <input id="cari" name="cari" type="search" value="{{ request('cari') }}"
                   placeholder="Cari judul atau penyelenggara..."
                   class="{{ $kelasIsian }}">
        </div>

        <div>
            <label for="kategori" class="sr-only">Kategori</label>
            <select id="kategori" name="kategori" class="{{ $kelasIsian }}">
                <option value="">Semua kategori</option>
                @foreach (['lomba' => 'Lomba', 'beasiswa' => 'Beasiswa', 'magang' => 'Magang'] as $nilai => $teks)
                    <option value="{{ $nilai }}" @selected(request('kategori') === $nilai)>{{ $teks }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="tingkat" class="sr-only">Tingkat</label>
            <select id="tingkat" name="tingkat" class="{{ $kelasIsian }}">
                <option value="">Semua tingkat</option>
                @foreach ([
                    'kampus' => 'Kampus', 'regional' => 'Regional',
                    'nasional' => 'Nasional', 'internasional' => 'Internasional',
                ] as $nilai => $teks)
                    <option value="{{ $nilai }}" @selected(request('tingkat') === $nilai)>{{ $teks }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="urut" class="sr-only">Urutkan</label>
            <select id="urut" name="urut" class="{{ $kelasIsian }}">
                <option value="deadline" @selected(request('urut', 'deadline') === 'deadline')>Deadline terdekat</option>
                <option value="terbaru" @selected(request('urut') === 'terbaru')>Baru ditambahkan</option>
            </select>
        </div>

    </div>

    <div class="mt-3 flex flex-wrap items-center justify-between gap-3">

        <div class="flex flex-wrap items-center gap-4">
            <label class="flex items-center gap-2 text-sm text-dasar-700">
                <input type="checkbox" name="biaya" value="gratis" @checked(request('biaya') === 'gratis')
                       class="h-4 w-4 cursor-pointer  border-dasar-900">
                Hanya yang gratis
            </label>

            <label class="flex items-center gap-2 text-sm text-dasar-700">
                <input type="checkbox" name="tampilkan_lewat" value="1" @checked(request('tampilkan_lewat'))
                       class="h-4 w-4 cursor-pointer  border-dasar-900">
                Tampilkan yang sudah berakhir
            </label>
        </div>

        <div class="flex gap-2">
            <x-tombol type="submit" class="px-4 py-2">Terapkan</x-tombol>
            <x-tombol :href="route('peluang.index')" jenis="kedua" class="px-4 py-2">Reset</x-tombol>
        </div>

    </div>
</form>

<p data-reveal class="mt-6 text-sm text-dasar-600">
    <strong class="font-judul text-base font-bold text-dasar-900 tabular-nums">{{ $peluang->total() }}</strong>
    peluang ditemukan
</p>

@if ($peluang->isEmpty())

    <x-kartu datar jarak="p-10" data-reveal class="mt-4 border-dashed border-dasar-900 text-center">
        <span aria-hidden="true"
              class="mx-auto grid h-12 w-12 place-items-center  bg-dasar-100
                     text-dasar-500 ring-1 ring-inset ring-dasar-900">
            <svg viewBox="0 0 24 24" class="h-6 w-6" fill="currentColor">
                <path d="M11 2a9 9 0 1 0 5.6 16.1l4.1 4.2 1.4-1.4-4.2-4.1A9 9 0 0 0 11 2Zm0 2a7 7 0 1 1 0 14 7 7 0 0 1 0-14Z"/>
            </svg>
        </span>

        <p class="mt-4 font-judul text-lg font-semibold text-dasar-900">Tidak ada peluang yang cocok</p>
        <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-dasar-600">
            Coba longgarkan penyaringnya, atau centang
            <strong class="text-dasar-800">Tampilkan yang sudah berakhir</strong>.
        </p>

        <div class="mt-5">
            <x-tombol :href="route('peluang.index')" jenis="kedua">Reset penyaring</x-tombol>
        </div>
    </x-kartu>

@else

    <div data-reveal-grup class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($peluang as $item)
            <div data-reveal-anak class="h-full">
                <x-kartu-peluang :peluang="$item" :tersimpan="$idTersimpan->contains($item->id)" />
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $peluang->links() }}
    </div>

@endif

@endsection
