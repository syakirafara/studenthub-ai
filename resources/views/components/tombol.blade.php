@props([
    'jenis' => 'utama',
    'href' => null,
    'type' => 'submit',
])

@php
    $gaya = match ($jenis) {
        'kedua' => 'tombol-hantu',
        'bahaya' => 'border border-bahaya-500/40 bg-bahaya-600/90 text-white transition-transform '
                    .'duration-300 hover:-translate-y-0.5 hover:bg-bahaya-600',
        default => 'tombol-emas',
    };

    $kelas = 'inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 '
        .'text-sm font-semibold tracking-tight focus:outline-none '
        .'disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0 '.$gaya;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $kelas]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $kelas]) }}>{{ $slot }}</button>
@endif
