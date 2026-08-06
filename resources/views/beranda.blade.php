@extends('layouts.app')

@section('judul', 'Beranda')

@section('isi')

<h1 class="text-2xl font-semibold tracking-tight">
    Halo, {{ auth()->user()->name }}
</h1>

@if ($profil)
    <p class="mt-1 text-sm text-slate-500">
        {{ $profil->jurusan }} &middot;
        Semester {{ $profil->semester }} &middot;
        {{ $profil->universitas }}
    </p>
@endif

{{-- Profil belum lengkap: dorong melengkapi dulu, karena tanpa itu
     skor kecocokan tidak punya bahan apa pun untuk dibandingkan. --}}
@if ($kelengkapan < 100)
    <x-kartu class="mt-6 border-waspada-200 bg-waspada-50">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-waspada-800">
                    Profilmu baru {{ $kelengkapan }}% lengkap
                </p>
                <p class="mt-0.5 text-sm text-waspada-700">
                    Skor kecocokan hanya seakurat data yang tersedia. Lengkapi minat dan kemampuanmu dulu.
                </p>
            </div>
            <x-tombol :href="route('profil.edit')">Lengkapi profil</x-tombol>
        </div>
    </x-kartu>
@endif

{{-- Ringkasan angka --}}
<div class="mt-6 grid gap-4 sm:grid-cols-3">
    @foreach ([
        ['Peluang tersedia', $jumlah['tersedia'], route('peluang.index'), 'Jelajahi'],
        ['Sudah kamu simpan', $jumlah['tersimpan'], route('tersimpan.index'), 'Lihat'],
        ['Sudah dinilai AI', $jumlah['dinilai'], null, null],
    ] as [$label, $angka, $tautan, $teksTautan])
        <x-kartu>
            <p class="text-xs uppercase tracking-wide text-slate-400">{{ $label }}</p>
            <p class="mt-1 text-3xl font-semibold text-slate-800">{{ $angka }}</p>
            @if ($tautan)
                <a href="{{ $tautan }}" class="mt-1 inline-block text-sm font-medium text-utama-600 hover:underline">
                    {{ $teksTautan }} &rarr;
                </a>
            @endif
        </x-kartu>
    @endforeach
</div>

{{-- Deadline yang segera berakhir --}}
@if ($segeraBerakhir->isNotEmpty())
    <h2 class="mt-10 text-lg font-semibold">Segera berakhir</h2>
    <p class="mt-1 text-sm text-slate-500">
        Dari peluang yang sudah kamu simpan. Yang paling mendesak lebih dulu.
    </p>

    <div class="mt-4 space-y-3">
        @foreach ($segeraBerakhir as $item)
            <x-kartu class="flex flex-wrap items-center justify-between gap-4">
                <div class="min-w-0">
                    <a href="{{ route('peluang.show', $item) }}"
                       class="font-medium text-slate-900 hover:text-utama-700">
                        {{ $item->judul }}
                    </a>
                    @if ($item->penyelenggara)
                        <p class="text-sm text-slate-500">{{ $item->penyelenggara }}</p>
                    @endif
                </div>

                <x-lencana :warna="$item->statusDeadline() === 'mepet' || $item->statusDeadline() === 'hari_ini' ? 'waspada' : 'abu'">
                    {{ $item->teksDeadline() }}
                </x-lencana>
            </x-kartu>
        @endforeach
    </div>
@endif

{{-- Rekomendasi berdasarkan skor --}}
<h2 class="mt-10 text-lg font-semibold">Paling cocok untukmu</h2>

@if ($rekomendasi->isEmpty())

    <x-kartu class="mt-4 border-dashed p-10 text-center">
        <p class="text-sm font-medium text-slate-700">Belum ada peluang yang dinilai</p>
        <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">
            Buka satu peluang di katalog, lalu tekan <strong>Hitung kecocokan</strong>.
            AI akan membandingkan syaratnya dengan profilmu, dan hasilnya muncul di sini
            diurutkan dari yang paling cocok.
        </p>
        <div class="mt-4">
            <x-tombol :href="route('peluang.index')">Jelajahi katalog</x-tombol>
        </div>
    </x-kartu>

@else

    <p class="mt-1 text-sm text-slate-500">
        Diurutkan dari skor tertinggi, hanya yang pendaftarannya masih dibuka.
    </p>

    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($rekomendasi as $cocok)
            @if ($cocok->opportunity)
                <x-kartu-peluang :peluang="$cocok->opportunity" :skor="$cocok" />
            @endif
        @endforeach
    </div>

    <p class="mt-4 text-sm text-slate-500">
        Belum semua peluang dinilai.
        <a href="{{ route('peluang.index') }}" class="font-medium text-utama-600 hover:underline">
            Jelajahi katalog
        </a>
        untuk menghitung kecocokan peluang lainnya.
    </p>

@endif

@endsection
