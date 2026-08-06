@extends('layouts.app')

@section('judul', 'Dasbor Admin')

@section('isi')

@php
    $status = [
        [
            'label' => 'Menunggu verifikasi',
            'angka' => $jumlah['menunggu'],
            'warna' => 'text-waspada-300',
            'latar' => 'from-waspada-400/22 to-waspada-600/8 ring-waspada-400/25',
            'ikon' => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm1 5v5.3l3.6 2.1-.8 1.4-4.3-2.5V7h1.5Z',
        ],
        [
            'label' => 'Sudah disetujui',
            'angka' => $jumlah['disetujui'],
            'warna' => 'text-sukses-300',
            'latar' => 'from-sukses-400/22 to-sukses-600/8 ring-sukses-400/25',
            'ikon' => 'M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0z',
        ],
        [
            'label' => 'Ditolak',
            'angka' => $jumlah['ditolak'],
            'warna' => 'text-slate-400',
            'latar' => 'from-white/12 to-white/4 ring-white/12',
            'ikon' => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm4 12.6-1.4 1.4L12 13.4 9.4 16 8 14.6 10.6 12 8 9.4 9.4 8l2.6 2.6L14.6 8 16 9.4 13.4 12l2.6 2.6Z',
        ],
    ];
@endphp

{{-- ---------- Kepala ---------- --}}
<div data-reveal>
    <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-utama-300">
        Area admin
    </p>
    <h1 class="mt-1.5 font-judul text-3xl font-bold tracking-tight text-white">Dasbor</h1>
    <p class="mt-1.5 text-sm text-slate-400">Masuk sebagai {{ auth()->user()->name }}</p>
</div>

{{-- ---------- Ringkasan status ---------- --}}
<div data-reveal-grup class="mt-6 grid gap-4 sm:grid-cols-3">
    @foreach ($status as $kartu)
        <x-kartu datar data-reveal-anak>
            <div class="flex items-start justify-between gap-3">
                <span aria-hidden="true"
                      class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br
                             ring-1 ring-inset {{ $kartu['latar'] }} {{ $kartu['warna'] }}">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor">
                        <path d="{{ $kartu['ikon'] }}"/>
                    </svg>
                </span>

                <span data-hitung="{{ $kartu['angka'] }}"
                      class="font-judul text-3xl font-bold leading-none tabular-nums {{ $kartu['warna'] }}">
                    {{ $kartu['angka'] }}
                </span>
            </div>

            <p class="mt-4 text-[0.68rem] font-semibold uppercase tracking-[0.14em] text-slate-500">
                {{ $kartu['label'] }}
            </p>
        </x-kartu>
    @endforeach
</div>

{{-- ---------- Angka pemakaian AI ---------- --}}
<div data-reveal class="mt-14">
    <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-utama-300">
        Dari catatan sistem
    </p>
    <h2 class="mt-1.5 font-judul text-2xl font-bold tracking-tight text-white">Pemakaian AI</h2>
    <p class="mt-1.5 max-w-2xl text-sm leading-relaxed text-slate-400">
        Angka ini dihitung dari tabel <code class="rounded bg-white/6 px-1.5 py-0.5 text-xs text-slate-300">ai_logs</code>,
        bukan perkiraan. Dipakai untuk menjelaskan efisiensi dan akurasi saat presentasi.
    </p>
</div>

<div data-reveal-grup class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ([
        ['Total panggilan', number_format($ai['total'], 0, ',', '.'), null],
        ['Dilayani dari simpanan', $ai['hemat_persen'].'%', $ai['dari_cache'].' dari '.$ai['total'].' panggilan terhindar'],
        ['Tingkat keberhasilan', $ai['berhasil_persen'].'%', $ai['gagal'].' gagal'],
        ['Rata-rata waktu', number_format($ai['durasi_rata'], 0, ',', '.').' ms', null],
    ] as [$label, $nilai, $catatan])
        <x-kartu datar data-reveal-anak>
            <p class="text-[0.68rem] font-semibold uppercase tracking-[0.14em] text-slate-500">
                {{ $label }}
            </p>
            <p class="mt-2 font-judul text-2xl font-bold text-white tabular-nums">{{ $nilai }}</p>
            @if ($catatan)
                <p class="mt-1 text-xs leading-relaxed text-slate-400">{{ $catatan }}</p>
            @endif
        </x-kartu>
    @endforeach
</div>

<x-kartu datar data-reveal class="mt-4">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-[0.68rem] font-semibold uppercase tracking-[0.14em] text-slate-500">
                Akurasi pembacaan poster
            </p>

            @if ($ai['akurasi_persen'] !== null)
                <p class="mt-2 font-judul text-3xl font-bold text-utama-300 tabular-nums">
                    {{ $ai['akurasi_persen'] }}%
                </p>

                <div class="mt-3 h-1.5 max-w-sm overflow-hidden rounded-full bg-white/8">
                    <div data-batang="{{ $ai['akurasi_persen'] }}"
                         class="h-full rounded-full bg-gradient-to-r from-utama-600 to-utama-300"></div>
                </div>

                <p class="mt-2.5 max-w-lg text-xs leading-relaxed text-slate-400">
                    Dihitung dari {{ $ai['poster_ditinjau'] }} poster yang sudah diverifikasi admin:
                    berapa persen kolom yang tidak perlu dikoreksi sama sekali.
                </p>
            @else
                <p class="mt-2 max-w-lg text-sm leading-relaxed text-slate-400">
                    Belum ada poster yang diverifikasi. Angka akurasi muncul setelah kamu
                    menyetujui poster pertama.
                </p>
            @endif
        </div>

        <p class="shrink-0 rounded-lg bg-white/5 px-3 py-1.5 text-xs text-slate-400 tabular-nums">
            {{ number_format($ai['token_total'], 0, ',', '.') }} token terpakai
        </p>
    </div>
</x-kartu>

{{-- ---------- Antrean verifikasi ---------- --}}
<div data-reveal class="mt-14">
    <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-utama-300">
        Butuh diperiksa manusia
    </p>
    <h2 class="mt-1.5 font-judul text-2xl font-bold tracking-tight text-white">Antrean verifikasi</h2>

    @unless ($antrean->isEmpty())
        <p class="mt-1.5 text-sm text-slate-400">Diurutkan dari yang paling lama menunggu.</p>
    @endunless
</div>

@if ($antrean->isEmpty())

    <x-kartu datar jarak="p-10" data-reveal data-reveal-jeda="80"
             class="mt-4 border-dashed border-white/15 text-center">
        <span aria-hidden="true"
              class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-sukses-500/10
                     text-sukses-300 ring-1 ring-inset ring-sukses-400/25">
            <svg viewBox="0 0 24 24" class="h-6 w-6" fill="currentColor">
                <path d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0z"/>
            </svg>
        </span>

        <p class="mt-4 font-judul text-lg font-semibold text-white">Antrean kosong</p>
        <p class="mt-2 text-sm text-slate-400">Semua unggahan sudah diverifikasi.</p>
    </x-kartu>

@else

    <div data-reveal-grup class="mt-4 space-y-3">
        @foreach ($antrean as $item)
            <x-kartu data-reveal-anak
                     class="group flex flex-wrap items-center justify-between gap-4">
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

                    <p class="mt-2 font-judul font-semibold text-white">{{ $item->judul }}</p>

                    @if ($item->penyelenggara)
                        <p class="mt-0.5 text-sm text-slate-400">{{ $item->penyelenggara }}</p>
                    @endif
                </div>

                <x-tombol :href="route('admin.periksa', $item)" class="shrink-0">Periksa</x-tombol>
            </x-kartu>
        @endforeach
    </div>

    <div class="mt-6">{{ $antrean->links() }}</div>

@endif

@endsection
