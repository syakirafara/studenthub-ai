@props(['warna' => 'abu'])

@php
    /*
     | Di tema kaca, lencana dibuat nyaris tembus pandang supaya tidak merebut
     | perhatian dari judul. Di gaya ini kebalikannya: bidang warna PENUH
     | dengan tepi pekat.
     |
     | Yang menjaganya tidak berteriak bukan lagi ketipisan warnanya,
     | melainkan ukurannya yang kecil dan warnanya yang sudah diredam sejak
     | di palet.
     */
    $gaya = match ($warna) {
        'utama' => 'bg-utama-200 text-utama-800',
        'sukses' => 'bg-sukses-200 text-sukses-800',
        'bahaya' => 'bg-bahaya-200 text-bahaya-800',
        'waspada' => 'bg-waspada-200 text-waspada-800',
        default => 'bg-dasar-200 text-dasar-800',
    };
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center border-2 border-dasar-900 px-2.5 py-0.5
                text-xs font-bold uppercase tracking-wide {$gaya}",
]) }}>
    {{ $slot }}
</span>
