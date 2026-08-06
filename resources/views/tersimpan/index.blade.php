@extends('layouts.app')

@section('judul', 'Peluang Tersimpan')

@section('isi')

<div data-reveal>
    <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-utama-300">
        Simpananmu
    </p>
    <h1 class="mt-1.5 font-judul text-3xl font-bold tracking-tight text-white">Peluang tersimpan</h1>
    <p class="mt-1.5 text-sm text-slate-400">
        Diurutkan dari deadline terdekat, supaya yang paling mendesak muncul lebih dulu.
    </p>
</div>

@if ($peluang->isEmpty())

    <x-kartu datar jarak="p-10" data-reveal data-reveal-jeda="80"
             class="mt-6 border-dashed border-white/15 text-center">
        <span aria-hidden="true"
              class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-white/5
                     text-slate-500 ring-1 ring-inset ring-white/10">
            <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17.6 3.3c1.1.1 1.9 1.1 1.9 2.2V21L12 17.25 4.5 21V5.5c0-1.1.8-2.1 1.9-2.2a48.5 48.5 0 0 1 11.2 0Z"/>
            </svg>
        </span>

        <p class="mt-4 font-judul text-lg font-semibold text-white">Belum ada yang disimpan</p>

        <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-slate-400">
            Tekan ikon pembatas di kartu peluang untuk menyimpannya. Yang tersimpan
            akan muncul di sini beserta hitung mundur deadline-nya.
        </p>

        <div class="mt-5">
            <x-tombol :href="route('peluang.index')">Jelajahi katalog</x-tombol>
        </div>
    </x-kartu>

@else

    <p data-reveal class="mt-6 text-sm text-slate-400">
        <strong class="font-judul text-base font-bold text-white tabular-nums">{{ $peluang->total() }}</strong>
        peluang tersimpan
    </p>

    <div data-reveal-grup class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($peluang as $item)
            <div data-reveal-anak class="h-full">
                <x-kartu-peluang :peluang="$item" :tersimpan="true" />
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $peluang->links() }}
    </div>

@endif

@endsection
