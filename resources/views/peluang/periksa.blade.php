@extends('layouts.app')

@section('judul', 'Periksa Hasil Bacaan AI')

@section('isi')

@php
    $syarat = $peluang->syarat ?? [];
    $kosong = fn ($nilai) => $nilai === null || $nilai === '' || $nilai === [];
@endphp

<div class="mx-auto max-w-4xl">

    <h1 class="font-judul text-3xl font-bold tracking-tight text-dasar-900">Periksa hasil bacaan AI</h1>
    <p class="mt-1 text-sm text-dasar-600">
        Ini yang berhasil dibaca AI dari posternya. Bandingkan dengan gambar di samping.
    </p>

    <div class="mt-6 grid gap-6 lg:grid-cols-5">

        {{-- Poster --}}
        <div class="lg:col-span-2">
            <x-kartu datar jarak="p-3">
                @if ($peluang->poster_path)
                    <img src="{{ Storage::url($peluang->poster_path) }}"
                         alt="Poster {{ $peluang->judul }}"
                         class="w-full ">
                @else
                    <p class="p-6 text-center text-sm text-dasar-600">Poster tidak tersimpan.</p>
                @endif
            </x-kartu>
        </div>

        {{-- Hasil bacaan --}}
        <div class="lg:col-span-3">
            <x-kartu datar jarak="">
                <dl class="divide-y divide-dasar-900">

                    @foreach ([
                        'Judul' => $peluang->judul,
                        'Penyelenggara' => $peluang->penyelenggara,
                        'Kategori' => ucfirst($peluang->kategori),
                        'Tingkat' => $peluang->tingkat === 'tidak_disebutkan' ? null : ucfirst($peluang->tingkat),
                        'Deadline' => $peluang->deadline?->translatedFormat('d F Y'),
                        'Biaya' => $peluang->biaya === 'berbayar'
                            ? 'Rp '.number_format($peluang->nominal_biaya ?? 0, 0, ',', '.')
                            : ($peluang->biaya === 'gratis' ? 'Gratis' : null),
                        'Tautan' => $peluang->link,
                        'Deskripsi' => $peluang->deskripsi,
                    ] as $label => $nilai)
                        <div class="flex gap-4 px-4 py-3">
                            <dt class="w-32 shrink-0 text-sm text-dasar-600">{{ $label }}</dt>
                            <dd class="min-w-0 flex-1 text-sm {{ $kosong($nilai) ? 'text-dasar-500 italic' : 'text-dasar-900' }}">
                                {{ $kosong($nilai) ? 'tidak terbaca dari poster' : $nilai }}
                            </dd>
                        </div>
                    @endforeach

                    <div class="flex gap-4 px-4 py-3">
                        <dt class="w-32 shrink-0 text-sm text-dasar-600">Jurusan</dt>
                        <dd class="min-w-0 flex-1 text-sm text-dasar-900">
                            {{ empty($syarat['jurusan']) ? 'Semua jurusan' : implode(', ', $syarat['jurusan']) }}
                        </dd>
                    </div>

                    <div class="flex gap-4 px-4 py-3">
                        <dt class="w-32 shrink-0 text-sm text-dasar-600">Peserta</dt>
                        <dd class="min-w-0 flex-1 text-sm text-dasar-900">
                            {{ ($syarat['ukuran_tim'] ?? 1) > 1
                                ? 'Tim maksimal '.$syarat['ukuran_tim'].' orang'
                                : 'Perorangan' }}
                        </dd>
                    </div>

                    @if (! empty($syarat['lainnya']))
                        <div class="flex gap-4 px-4 py-3">
                            <dt class="w-32 shrink-0 text-sm text-dasar-600">Syarat lain</dt>
                            <dd class="min-w-0 flex-1 text-sm text-dasar-900">
                                <ul class="list-inside list-disc space-y-1">
                                    @foreach ($syarat['lainnya'] as $butir)
                                        <li>{{ $butir }}</li>
                                    @endforeach
                                </ul>
                            </dd>
                        </div>
                    @endif

                </dl>
            </x-kartu>

            <x-kartu datar class="mt-4 border-dasar-900 bg-waspada-200">
                <p class="text-sm font-medium text-waspada-700">Ada yang keliru atau kosong?</p>
                <p class="mt-1 text-sm text-waspada-600">
                    Wajar &mdash; poster sering tidak mencantumkan semuanya, dan tulisan kecil kadang
                    terbaca meleset. Kolom yang bertanda <em>tidak terbaca dari poster</em> sengaja
                    dikosongkan, bukan ditebak. Kemampuan membetulkan sendiri sedang disiapkan;
                    untuk sekarang admin akan melengkapinya saat verifikasi.
                </p>
            </x-kartu>

            <div class="mt-4 flex flex-wrap gap-3">
                <x-tombol :href="route('unggah.buat')">Unggah poster lain</x-tombol>
                <x-tombol :href="route('peluang.index')" jenis="kedua">Kembali ke katalog</x-tombol>
            </div>

            <p class="mt-4 text-xs text-dasar-600">
                Status saat ini: <strong>menunggu verifikasi admin</strong>.
                Peluang ini belum tampil di katalog sampai admin menyetujuinya.
            </p>
        </div>

    </div>
</div>

@endsection
