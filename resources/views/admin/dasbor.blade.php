@extends('layouts.app')

@section('judul', 'Dasbor Admin')

@section('isi')

<h1 class="font-judul text-3xl font-bold tracking-tight text-white">Dasbor Admin</h1>
<p class="mt-1 text-sm text-slate-400">Masuk sebagai {{ auth()->user()->name }}</p>

{{-- Ringkasan status peluang --}}
<div class="mt-6 grid gap-4 sm:grid-cols-3">
    @foreach ([
        ['Menunggu verifikasi', $jumlah['menunggu'], 'waspada'],
        ['Sudah disetujui', $jumlah['disetujui'], 'utama'],
        ['Ditolak', $jumlah['ditolak'], 'abu'],
    ] as [$label, $angka, $warna])
        <x-kartu>
            <p class="text-xs uppercase tracking-wide text-slate-500">{{ $label }}</p>
            <p class="mt-1 text-3xl font-semibold
                      {{ $warna === 'waspada' ? 'text-waspada-300' : ($warna === 'utama' ? 'text-utama-200' : 'text-slate-400') }}">
                {{ $angka }}
            </p>
        </x-kartu>
    @endforeach
</div>

{{-- Angka pemakaian AI --}}
<h2 class="mt-12 font-judul text-xl font-semibold text-white">Pemakaian AI</h2>
<p class="mt-1 text-sm text-slate-400">
    Angka ini dihitung dari catatan sistem, bukan perkiraan. Dipakai untuk menjelaskan
    efisiensi dan akurasi saat presentasi.
</p>

<div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ([
        ['Total panggilan', number_format($ai['total'], 0, ',', '.'), null],
        ['Dilayani dari simpanan', $ai['hemat_persen'].'%', $ai['dari_cache'].' dari '.$ai['total'].' panggilan terhindar'],
        ['Tingkat keberhasilan', $ai['berhasil_persen'].'%', $ai['gagal'].' gagal'],
        ['Rata-rata waktu', number_format($ai['durasi_rata'], 0, ',', '.').' ms', null],
    ] as [$label, $nilai, $catatan])
        <x-kartu>
            <p class="text-xs uppercase tracking-wide text-slate-500">{{ $label }}</p>
            <p class="mt-1 text-2xl font-semibold text-white">{{ $nilai }}</p>
            @if ($catatan)
                <p class="mt-0.5 text-xs text-slate-400">{{ $catatan }}</p>
            @endif
        </x-kartu>
    @endforeach
</div>

<x-kartu class="mt-4">
    <div class="flex flex-wrap items-baseline justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-500">Akurasi pembacaan poster</p>
            @if ($ai['akurasi_persen'] !== null)
                <p class="mt-1 text-2xl font-semibold text-white">{{ $ai['akurasi_persen'] }}%</p>
                <p class="mt-0.5 text-xs text-slate-400">
                    Dihitung dari {{ $ai['poster_ditinjau'] }} poster yang sudah diverifikasi admin:
                    berapa persen kolom yang tidak perlu dikoreksi sama sekali.
                </p>
            @else
                <p class="mt-1 text-sm text-slate-400">
                    Belum ada poster yang diverifikasi. Angka akurasi muncul setelah kamu
                    menyetujui poster pertama.
                </p>
            @endif
        </div>
        <p class="text-xs text-slate-500">{{ number_format($ai['token_total'], 0, ',', '.') }} token terpakai</p>
    </div>
</x-kartu>

{{-- Antrean verifikasi --}}
<h2 class="mt-12 font-judul text-xl font-semibold text-white">Antrean verifikasi</h2>

@if ($antrean->isEmpty())

    <x-kartu class="mt-4 border-dashed p-10 text-center">
        <p class="text-sm font-medium text-slate-200">Antrean kosong</p>
        <p class="mt-1 text-sm text-slate-400">Semua unggahan sudah diverifikasi.</p>
    </x-kartu>

@else

    <p class="mt-1 text-sm text-slate-400">Diurutkan dari yang paling lama menunggu.</p>

    <div class="mt-4 space-y-3">
        @foreach ($antrean as $item)
            <x-kartu class="flex flex-wrap items-center justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-lencana warna="waspada">{{ ucfirst($item->kategori) }}</x-lencana>
                        <span class="text-xs text-slate-500">
                            diunggah {{ $item->created_at->diffForHumans() }}
                            @if ($item->pengunggah)
                                oleh {{ $item->pengunggah->name }}
                            @endif
                        </span>
                    </div>
                    <p class="mt-1 font-medium text-white">{{ $item->judul }}</p>
                    @if ($item->penyelenggara)
                        <p class="text-sm text-slate-400">{{ $item->penyelenggara }}</p>
                    @endif
                </div>

                <x-tombol :href="route('admin.periksa', $item)">Periksa</x-tombol>
            </x-kartu>
        @endforeach
    </div>

    <div class="mt-6">{{ $antrean->links() }}</div>

@endif

@endsection
