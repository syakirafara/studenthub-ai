@props([
    'peluang',
    'tersimpan' => false,
    'skor' => null,
])

@php
    $warnaKategori = match ($peluang->kategori) {
        'lomba'    => 'utama',
        'beasiswa' => 'sukses',
        'magang'   => 'waspada',
        default    => 'abu',
    };

    $warnaDeadline = match ($peluang->statusDeadline()) {
        'lewat', 'hari_ini' => 'bahaya',
        'mepet'             => 'waspada',
        default             => 'abu',
    };
@endphp

{{--
    Kartu ini TIDAK dibungkus tautan, melainkan memakai pola "tautan melar":
    judulnya yang menjadi tautan, lalu direntangkan menutupi seluruh kartu
    lewat after:absolute after:inset-0. Dengan begitu seluruh kartu tetap
    bisa diklik, tetapi tombol simpan di dalamnya tidak menjadi tombol di
    dalam tautan — susunan yang tidak sah di HTML dan membuat kliknya bentrok.
--}}
<x-kartu class="relative flex h-full flex-col gap-3 p-5 transition hover:border-utama-300 hover:shadow-sm focus-within:ring-2 focus-within:ring-utama-600 focus-within:ring-offset-2">

    @if ($skor)
        {{-- Skor ditaruh paling atas: inilah alasan kartu ini muncul di sini. --}}
        <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
            <span class="text-2xl font-semibold
                         {{ $skor->warna() === 'utama' ? 'text-utama-700'
                            : ($skor->warna() === 'waspada' ? 'text-waspada-700' : 'text-slate-500') }}">
                {{ $skor->skor }}
            </span>
            <span class="text-xs leading-tight text-slate-500">
                dari 100<br>{{ $skor->keterangan() }}
            </span>
        </div>
    @endif

    <div class="flex flex-wrap items-center gap-2">
        <x-lencana :warna="$warnaKategori">{{ ucfirst($peluang->kategori) }}</x-lencana>

        @if ($peluang->tingkat !== 'tidak_disebutkan')
            <x-lencana>{{ ucfirst($peluang->tingkat) }}</x-lencana>
        @endif

        @if ($peluang->biaya === 'gratis')
            <x-lencana warna="sukses">Gratis</x-lencana>
        @endif
    </div>

    <div>
        <h3 class="text-base font-semibold leading-snug text-slate-900">
            <a href="{{ route('peluang.show', $peluang) }}"
               class="after:absolute after:inset-0 focus:outline-none">
                {{ $peluang->judul }}
            </a>
        </h3>

        @if ($peluang->penyelenggara)
            <p class="mt-0.5 text-sm text-slate-500">{{ $peluang->penyelenggara }}</p>
        @endif
    </div>

    @if ($peluang->deskripsi)
        <p class="line-clamp-2 text-sm text-slate-600">{{ $peluang->deskripsi }}</p>
    @endif

    <div class="mt-auto flex items-center justify-between gap-2 pt-1">
        <x-lencana :warna="$warnaDeadline">{{ $peluang->teksDeadline() }}</x-lencana>

        @auth
            {{-- relative z-10 menaikkan tombol di atas tautan yang melar tadi --}}
            <x-tombol-simpan :peluang="$peluang" :tersimpan="$tersimpan" class="relative z-10" />
        @else
            @if ($peluang->deadline)
                <span class="text-xs text-slate-400">
                    {{ $peluang->deadline->translatedFormat('d M Y') }}
                </span>
            @endif
        @endauth
    </div>

</x-kartu>
