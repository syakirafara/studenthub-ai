@props([
    'jenis' => 'utama',
    'href' => null,
    'type' => 'submit',
])

@php
    $gaya = match ($jenis) {
        'kedua' => 'tombol-hantu',
        'bahaya' => 'border-[2.5px] border-dasar-900 bg-bahaya-500 text-white font-bold shadow-keras '
                    .'transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 '
                    .'hover:bg-bahaya-600 hover:shadow-naik '
                    .'active:translate-x-[3px] active:translate-y-[3px] active:shadow-none',
        default => 'tombol-emas',
    };

    /*
     | Tulisan tombol dibuat kapital dan berjarak huruf sedikit lebar. Di gaya
     | ini tombol adalah benda paling menonjol di halaman, dan huruf kapital
     | memberinya bobot yang sepadan dengan tepi setebal itu.
     |
     | Dua kelas disabled:hover:translate wajib ada. Tanpa keduanya, tombol
     | yang sedang dimatikan masih ikut bergeser saat disentuh -- seolah masih
     | bisa ditekan padahal tidak.
     */
    $kelas = 'inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm uppercase '
        .'tracking-wide focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 '
        .'disabled:hover:translate-x-0 disabled:hover:translate-y-0 '
        .$gaya;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $kelas]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $kelas]) }}>{{ $slot }}</button>
@endif
