@props([
    'jenis' => 'utama',
    'href' => null,
    'type' => 'submit',
])

@php
    $gaya = match ($jenis) {
        'kedua'  => 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus:ring-slate-400',
        'bahaya' => 'bg-bahaya-600 text-white hover:bg-bahaya-700 focus:ring-bahaya-600',
        default  => 'bg-utama-600 text-white hover:bg-utama-700 focus:ring-utama-600',
    };

    $kelas = 'inline-block rounded-md px-4 py-2.5 text-center text-sm font-medium '
        . 'focus:outline-none focus:ring-2 focus:ring-offset-2 ' . $gaya;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $kelas]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $kelas]) }}>{{ $slot }}</button>
@endif
