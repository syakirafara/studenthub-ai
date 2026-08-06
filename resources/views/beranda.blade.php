@extends('layouts.app')

@section('judul', 'Beranda')

@section('isi')

@php
    /*
     | Ikon dan tautan tiap kartu angka. Ditaruh di satu tempat supaya
     | perulangan di bawah tetap terbaca -- kalau ikonnya ditulis langsung
     | di dalam perulangan, isinya tenggelam di antara jalur SVG.
     */
    $ringkasan = [
        [
            'label' => 'Peluang tersedia',
            'angka' => $jumlah['tersedia'],
            'tautan' => route('peluang.index'),
            'teksTautan' => 'Jelajahi',
            'warna' => 'text-utama-300',
            'latar' => 'from-utama-400/22 to-utama-600/8 ring-utama-400/25',
            'ikon' => 'M11 2a9 9 0 1 0 5.6 16.1l4.1 4.2 1.4-1.4-4.2-4.1A9 9 0 0 0 11 2Zm0 2a7 7 0 1 1 0 14 7 7 0 0 1 0-14Z',
        ],
        [
            'label' => 'Sudah kamu simpan',
            'angka' => $jumlah['tersimpan'],
            'tautan' => route('tersimpan.index'),
            'teksTautan' => 'Lihat',
            'warna' => 'text-langit-300',
            'latar' => 'from-langit-400/22 to-langit-600/8 ring-langit-400/25',
            'ikon' => 'M17.6 3.3c1.1.1 1.9 1.1 1.9 2.2V21L12 17.25 4.5 21V5.5c0-1.1.8-2.1 1.9-2.2a48.5 48.5 0 0 1 11.2 0Z',
        ],
        [
            'label' => 'Sudah dinilai AI',
            'angka' => $jumlah['dinilai'],
            'tautan' => null,
            'teksTautan' => null,
            'warna' => 'text-sukses-300',
            'latar' => 'from-sukses-400/22 to-sukses-600/8 ring-sukses-400/25',
            'ikon' => 'M12 3c0 4.97 4.03 9 9 9-4.97 0-9 4.03-9 9 0-4.97-4.03-9-9-9 4.97 0 9-4.03 9-9z',
        ],
    ];
@endphp

{{-- ---------- Sapaan ---------- --}}
<div data-reveal>
    <h1 class="font-judul text-3xl font-bold tracking-tight text-white">
        Halo, {{ auth()->user()->name }}
    </h1>

    @if ($profil)
        <p class="mt-1.5 text-sm text-slate-400">
            {{ $profil->jurusan }} &middot;
            Semester {{ $profil->semester }} &middot;
            {{ $profil->universitas }}
        </p>
    @endif
</div>

{{-- Profil belum lengkap: dorong melengkapi dulu, karena tanpa itu
     skor kecocokan tidak punya bahan apa pun untuk dibandingkan. --}}
@if ($kelengkapan < 100)
    <x-kartu datar data-reveal data-reveal-jeda="80"
             class="mt-6 border-waspada-400/30 bg-waspada-500/10">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-start gap-3">
                <span aria-hidden="true"
                      class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg
                             bg-waspada-500/15 text-waspada-300 ring-1 ring-inset ring-waspada-400/25">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor">
                        <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm1 15h-2v-2h2v2Zm0-4h-2V7h2v6Z"/>
                    </svg>
                </span>

                <div>
                    <p class="text-sm font-semibold text-waspada-200">
                        Profilmu baru {{ $kelengkapan }}% lengkap
                    </p>
                    <p class="mt-0.5 text-sm leading-relaxed text-waspada-300/90">
                        Skor kecocokan hanya seakurat data yang tersedia.
                        Lengkapi minat dan kemampuanmu dulu.
                    </p>
                </div>
            </div>

            <x-tombol :href="route('profil.edit')">Lengkapi profil</x-tombol>
        </div>
    </x-kartu>
@endif

{{-- ---------- Ringkasan angka ---------- --}}
<div data-reveal-grup class="mt-6 grid gap-4 sm:grid-cols-3">
    @foreach ($ringkasan as $kartu)
        <x-kartu datar data-reveal-anak class="relative">
            <div class="flex items-start justify-between gap-3">
                <span aria-hidden="true"
                      class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br
                             ring-1 ring-inset {{ $kartu['latar'] }} {{ $kartu['warna'] }}">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor">
                        <path d="{{ $kartu['ikon'] }}"/>
                    </svg>
                </span>

                {{-- data-hitung membuat angkanya menghitung naik saat terlihat.
                     Isi awalnya tetap ditulis, supaya tanpa JavaScript pun
                     angkanya sudah benar. --}}
                <span data-hitung="{{ $kartu['angka'] }}"
                      class="font-judul text-3xl font-bold leading-none text-white tabular-nums">
                    {{ $kartu['angka'] }}
                </span>
            </div>

            <p class="mt-4 text-[0.68rem] font-semibold uppercase tracking-[0.14em] text-slate-500">
                {{ $kartu['label'] }}
            </p>

            @if ($kartu['tautan'])
                <a href="{{ $kartu['tautan'] }}"
                   class="mt-1.5 inline-flex items-center gap-1 text-sm font-medium
                          text-utama-300 transition-colors duration-300 hover:text-utama-200
                          after:absolute after:inset-0 focus:outline-none">
                    {{ $kartu['teksTautan'] }}
                    <span aria-hidden="true">&rarr;</span>
                </a>
            @endif
        </x-kartu>
    @endforeach
</div>

{{-- ---------- Deadline yang segera berakhir ---------- --}}
@if ($segeraBerakhir->isNotEmpty())
    <div data-reveal class="mt-14">
        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-utama-300">
            Dari simpananmu
        </p>
        <h2 class="mt-1.5 font-judul text-2xl font-bold tracking-tight text-white">Segera berakhir</h2>
        <p class="mt-1 text-sm text-slate-400">Yang paling mendesak lebih dulu.</p>
    </div>

    <div data-reveal-grup class="mt-4 space-y-3">
        @foreach ($segeraBerakhir as $item)
            @php
                $mendesak = in_array($item->statusDeadline(), ['mepet', 'hari_ini', 'lewat'], true);
            @endphp

            <x-kartu data-reveal-anak
                     class="group flex flex-wrap items-center justify-between gap-4 py-4">
                <div class="min-w-0">
                    <a href="{{ route('peluang.show', $item) }}"
                       class="font-medium text-white transition-colors duration-300
                              after:absolute after:inset-0 focus:outline-none group-hover:text-utama-100">
                        {{ $item->judul }}
                    </a>

                    @if ($item->penyelenggara)
                        <p class="mt-0.5 text-sm text-slate-400">{{ $item->penyelenggara }}</p>
                    @endif
                </div>

                <p class="flex shrink-0 items-center gap-1.5 text-sm font-medium
                          {{ $mendesak ? 'text-waspada-300' : 'text-slate-400' }}">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                        <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm1 5v5.3l3.6 2.1-.8 1.4-4.3-2.5V7h1.5Z"/>
                    </svg>
                    {{ $item->teksDeadline() }}
                </p>
            </x-kartu>
        @endforeach
    </div>
@endif

{{-- ---------- Rekomendasi berdasarkan skor ---------- --}}
<div data-reveal class="mt-14">
    <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-utama-300">
        Dihitung AI
    </p>
    <h2 class="mt-1.5 font-judul text-2xl font-bold tracking-tight text-white">Paling cocok untukmu</h2>

    @unless ($rekomendasi->isEmpty())
        <p class="mt-1 text-sm text-slate-400">
            Diurutkan dari skor tertinggi, hanya yang pendaftarannya masih dibuka.
        </p>
    @endunless
</div>

@if ($rekomendasi->isEmpty())

    <x-kartu datar jarak="p-10" data-reveal data-reveal-jeda="80"
             class="mt-4 border-dashed border-white/15 text-center">
        <span aria-hidden="true"
              class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-white/5
                     text-slate-500 ring-1 ring-inset ring-white/10">
            <svg viewBox="0 0 24 24" class="h-6 w-6" fill="currentColor">
                <path d="M12 3c0 4.97 4.03 9 9 9-4.97 0-9 4.03-9 9 0-4.97-4.03-9-9-9 4.97 0 9-4.03 9-9z"/>
            </svg>
        </span>

        <p class="mt-4 font-judul text-lg font-semibold text-white">Belum ada peluang yang dinilai</p>

        <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-slate-400">
            Buka satu peluang di katalog, lalu tekan <strong class="text-slate-200">Hitung kecocokan</strong>.
            AI membandingkan syaratnya dengan profilmu, dan hasilnya muncul di sini
            diurutkan dari yang paling cocok.
        </p>

        <div class="mt-5">
            <x-tombol :href="route('peluang.index')">Jelajahi katalog</x-tombol>
        </div>
    </x-kartu>

@else

    <div data-reveal-grup class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($rekomendasi as $cocok)
            @if ($cocok->opportunity)
                <div data-reveal-anak class="h-full">
                    <x-kartu-peluang :peluang="$cocok->opportunity" :skor="$cocok" />
                </div>
            @endif
        @endforeach
    </div>

    <p data-reveal class="mt-5 text-sm text-slate-400">
        Belum semua peluang dinilai.
        <a href="{{ route('peluang.index') }}"
           class="font-semibold text-utama-300 underline-offset-2 hover:underline">Jelajahi katalog</a>
        untuk menghitung kecocokan peluang lainnya.
    </p>

@endif

@endsection
