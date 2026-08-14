@extends('layouts.app')

@section('judul', 'Verifikasi Peluang')

@section('isi')

@php
    $hasilAi = $review?->hasil_ai ?? [];
    $kelasIsian = 'mt-1 w-full  border border-dasar-900 px-3 py-2 text-sm focus:border-utama-400 focus:outline-none ';

    // Menandai kolom yang sudah berubah dari bacaan AI, supaya admin melihat
    // apa saja yang sedang dia koreksi.
    $berubah = function (string $kolom, $nilaiSekarang) use ($hasilAi) {
        $ai = $hasilAi[$kolom] ?? null;

        return trim((string) $ai) !== trim((string) $nilaiSekarang);
    };
@endphp

<a href="{{ route('admin.dasbor') }}" class="text-sm text-dasar-600 hover:text-utama-700">
    &larr; Kembali ke antrean
</a>

<h1 class="mt-3 font-judul text-3xl font-bold tracking-tight text-dasar-900">Verifikasi peluang</h1>
<p class="mt-1 text-sm text-dasar-600">
    Bandingkan poster dengan hasil bacaan AI. Betulkan yang keliru, lalu setujui atau tolak.
</p>

{{-- Peringatan kemungkinan duplikat --}}
@if ($duplikat->isNotEmpty())
    <div role="alert" class="mt-6  border border-dasar-900 bg-waspada-200 px-4 py-3">
        <p class="text-sm font-medium text-waspada-700">
            Mirip dengan {{ $duplikat->count() }} peluang yang sudah ada
        </p>
        <ul class="mt-2 space-y-1 text-sm text-waspada-600">
            @foreach ($duplikat as $lain)
                <li>
                    &bull; <a href="{{ route('peluang.show', $lain) }}" target="_blank"
                              class="font-medium underline">{{ $lain->judul }}</a>
                    <span class="text-waspada-600">
                        &mdash; kemiripan {{ $lain->kemiripan }}%, status {{ $lain->status }}
                    </span>
                </li>
            @endforeach
        </ul>
        <p class="mt-2 text-xs text-waspada-600">
            Kalau ini benar-benar peluang yang sama, tolak saja supaya katalog tidak berisi
            informasi ganda.
        </p>
    </div>
@endif

<div class="mt-6 grid gap-6 lg:grid-cols-5">

    {{-- Poster --}}
    <div class="lg:col-span-2">
        <x-kartu datar jarak="p-3" class="sticky top-24">
            @if ($peluang->poster_path)
                <img src="{{ Storage::url($peluang->poster_path) }}"
                     alt="Poster {{ $peluang->judul }}" class="w-full ">
            @else
                <p class="p-6 text-center text-sm text-dasar-600">Tidak ada poster.</p>
            @endif

            <p class="mt-3 px-1 text-xs text-dasar-600">
                Diunggah {{ $peluang->created_at->translatedFormat('d F Y, H:i') }}
                @if ($peluang->pengunggah)
                    oleh {{ $peluang->pengunggah->name }}
                @endif
            </p>
        </x-kartu>
    </div>

    {{-- Form koreksi --}}
    <div class="lg:col-span-3">
        <form method="POST" action="{{ route('admin.setujui', $peluang) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="judul" class="block text-sm font-medium text-dasar-800">
                    Judul
                    @if ($berubah('judul', old('judul', $peluang->judul)))
                        <span class="ml-1 text-xs font-normal text-waspada-600">dikoreksi</span>
                    @endif
                </label>
                <input id="judul" name="judul" type="text" required
                       value="{{ old('judul', $peluang->judul) }}" class="{{ $kelasIsian }}">
                @error('judul') <p class="mt-1 text-sm text-bahaya-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="penyelenggara" class="block text-sm font-medium text-dasar-800">Penyelenggara</label>
                <input id="penyelenggara" name="penyelenggara" type="text"
                       value="{{ old('penyelenggara', $peluang->penyelenggara) }}" class="{{ $kelasIsian }}">
                @error('penyelenggara') <p class="mt-1 text-sm text-bahaya-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="kategori" class="block text-sm font-medium text-dasar-800">Kategori</label>
                    <select id="kategori" name="kategori" class="{{ $kelasIsian }}">
                        @foreach (['lomba' => 'Lomba', 'beasiswa' => 'Beasiswa', 'magang' => 'Magang'] as $n => $t)
                            <option value="{{ $n }}" @selected(old('kategori', $peluang->kategori) === $n)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="tingkat" class="block text-sm font-medium text-dasar-800">Tingkat</label>
                    <select id="tingkat" name="tingkat" class="{{ $kelasIsian }}">
                        @foreach ([
                            'tidak_disebutkan' => 'Tidak disebutkan', 'kampus' => 'Kampus',
                            'regional' => 'Regional', 'nasional' => 'Nasional', 'internasional' => 'Internasional',
                        ] as $n => $t)
                            <option value="{{ $n }}" @selected(old('tingkat', $peluang->tingkat) === $n)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="deadline" class="block text-sm font-medium text-dasar-800">
                        Batas waktu
                        @if (! $peluang->deadline)
                            <span class="ml-1 text-xs font-normal text-waspada-600">tidak terbaca AI</span>
                        @endif
                    </label>
                    <input id="deadline" name="deadline" type="date"
                           value="{{ old('deadline', $peluang->deadline?->toDateString()) }}" class="{{ $kelasIsian }}">
                    @error('deadline') <p class="mt-1 text-sm text-bahaya-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="biaya" class="block text-sm font-medium text-dasar-800">Biaya</label>
                    <select id="biaya" name="biaya" class="{{ $kelasIsian }}">
                        @foreach ([
                            'tidak_disebutkan' => 'Tidak disebutkan', 'gratis' => 'Gratis', 'berbayar' => 'Berbayar',
                        ] as $n => $t)
                            <option value="{{ $n }}" @selected(old('biaya', $peluang->biaya) === $n)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="nominal_biaya" class="block text-sm font-medium text-dasar-800">Nominal (Rp)</label>
                    <input id="nominal_biaya" name="nominal_biaya" type="number" min="0"
                           value="{{ old('nominal_biaya', $peluang->nominal_biaya) }}" class="{{ $kelasIsian }}">
                    @error('nominal_biaya') <p class="mt-1 text-sm text-bahaya-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="link" class="block text-sm font-medium text-dasar-800">Tautan pendaftaran</label>
                    <input id="link" name="link" type="url" placeholder="https://..."
                           value="{{ old('link', $peluang->link) }}" class="{{ $kelasIsian }}">
                    @error('link') <p class="mt-1 text-sm text-bahaya-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-medium text-dasar-800">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" class="{{ $kelasIsian }}">{{ old('deskripsi', $peluang->deskripsi) }}</textarea>
                @error('deskripsi') <p class="mt-1 text-sm text-bahaya-600">{{ $message }}</p> @enderror
            </div>

            <x-kartu datar jarak="p-4">
                <p class="text-sm font-medium text-dasar-800">Syarat hasil bacaan AI</p>
                <pre class="mt-2 overflow-x-auto  bg-dasar-100 p-3 text-xs text-dasar-800">{{ json_encode($peluang->syarat, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                <p class="mt-2 text-xs text-dasar-600">
                    Penyuntingan syarat belum tersedia di tampilan ini. Kalau syaratnya keliru
                    berat, tolak saja dan minta pengunggah mengulang dengan poster yang lebih jelas.
                </p>
            </x-kartu>

            <div class="flex flex-wrap gap-3 border-t border-dasar-900 pt-5">
                <x-tombol>Setujui dan tayangkan</x-tombol>
                <x-tombol :href="route('admin.dasbor')" jenis="kedua">Batal</x-tombol>
            </div>
        </form>

        {{-- Tolak --}}
        <form method="POST" action="{{ route('admin.tolak', $peluang) }}" class="mt-8 space-y-3">
            @csrf
            @method('PUT')

            <label for="catatan_admin" class="block text-sm font-medium text-dasar-800">
                Alasan penolakan
            </label>
            <textarea id="catatan_admin" name="catatan_admin" rows="2"
                      placeholder="Contoh: poster tidak terbaca jelas, deadline tidak tercantum."
                      class="{{ $kelasIsian }}">{{ old('catatan_admin') }}</textarea>
            @error('catatan_admin') <p class="text-sm text-bahaya-600">{{ $message }}</p> @enderror

            <p class="text-xs text-dasar-600">
                Alasannya ditampilkan ke pengunggah, supaya mereka tahu apa yang perlu diperbaiki
                &mdash; bukan sekadar ditolak diam-diam.
            </p>

            <x-tombol jenis="bahaya">Tolak peluang ini</x-tombol>
        </form>
    </div>

</div>

@endsection
