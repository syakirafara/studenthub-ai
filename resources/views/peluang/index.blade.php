@extends('layouts.app')

@section('judul', 'Katalog Peluang')

@section('isi')

@php
    $kelasIsian = 'w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-utama-600 focus:outline-none focus:ring-1 focus:ring-utama-600';
@endphp

<div>
    <h1 class="text-2xl font-semibold tracking-tight">Katalog peluang</h1>
    <p class="mt-1 text-sm text-slate-500">
        Lomba, beasiswa, dan magang yang sudah diverifikasi admin.
    </p>
</div>

<form method="GET" action="{{ route('peluang.index') }}"
      class="mt-6 rounded-lg border border-slate-200 bg-white p-4">

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
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="biaya" value="gratis" @checked(request('biaya') === 'gratis')
                       class="rounded border-slate-300 text-utama-600 focus:ring-utama-600">
                Hanya yang gratis
            </label>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="tampilkan_lewat" value="1" @checked(request('tampilkan_lewat'))
                       class="rounded border-slate-300 text-utama-600 focus:ring-utama-600">
                Tampilkan yang sudah berakhir
            </label>
        </div>

        <div class="flex gap-2">
            <x-tombol type="submit" class="px-4 py-2">Terapkan</x-tombol>
            <x-tombol :href="route('peluang.index')" jenis="kedua" class="px-4 py-2">Reset</x-tombol>
        </div>

    </div>
</form>

<p class="mt-6 text-sm text-slate-500">
    <strong class="text-slate-700">{{ $peluang->total() }}</strong> peluang ditemukan
</p>

@if ($peluang->isEmpty())

    <x-kartu class="mt-4 border-dashed p-10 text-center">
        <p class="text-sm font-medium text-slate-700">Tidak ada peluang yang cocok</p>
        <p class="mt-1 text-sm text-slate-500">
            Coba longgarkan penyaringnya, atau centang "Tampilkan yang sudah berakhir".
        </p>
    </x-kartu>

@else

    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($peluang as $item)
            <x-kartu-peluang :peluang="$item" :tersimpan="$idTersimpan->contains($item->id)" />
        @endforeach
    </div>

    <div class="mt-8">
        {{ $peluang->links() }}
    </div>

@endif

@endsection
