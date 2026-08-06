@extends('layouts.app')

@section('judul', 'Peluang Tersimpan')

@section('isi')

<h1 class="font-judul text-3xl font-bold tracking-tight text-white">Peluang tersimpan</h1>
<p class="mt-1 text-sm text-slate-400">
    Diurutkan dari deadline terdekat, supaya yang paling mendesak muncul lebih dulu.
</p>

@if ($peluang->isEmpty())

    <x-kartu class="mt-6 border-dashed p-10 text-center">
        <p class="text-sm font-medium text-slate-200">Belum ada yang disimpan</p>
        <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">
            Tekan ikon pembatas di kartu peluang untuk menyimpannya. Yang tersimpan
            akan muncul di sini beserta hitung mundur deadline-nya.
        </p>
        <div class="mt-4">
            <x-tombol :href="route('peluang.index')">Jelajahi katalog</x-tombol>
        </div>
    </x-kartu>

@else

    <p class="mt-6 text-sm text-slate-400">
        <strong class="text-slate-200">{{ $peluang->total() }}</strong> peluang tersimpan
    </p>

    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($peluang as $item)
            <x-kartu-peluang :peluang="$item" :tersimpan="true" />
        @endforeach
    </div>

    <div class="mt-8">
        {{ $peluang->links() }}
    </div>

@endif

@endsection
