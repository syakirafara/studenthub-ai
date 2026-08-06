@props([
    'datar' => false,
    // Jarak dalam ditaruh di prop, bukan langsung di kelas, karena kelas Tailwind
    // yang bentrok (p-6 lawan p-0) dimenangkan oleh urutan di berkas CSS -- bukan
    // oleh urutan penulisan di sini. Prop membuat hasilnya pasti.
    'jarak' => 'p-6',
])

{{--
    Wadah kaca. Bawaannya ikut terangkat saat disentuh; beri :datar="true"
    untuk kartu yang bukan tautan, supaya tidak terasa bisa diklik padahal tidak.
--}}
<div @unless ($datar) data-sorot @endunless {{ $attributes->merge([
    'class' => "kaca kaca-tepi relative overflow-hidden rounded-2xl shadow-naik {$jarak} "
        .($datar ? '' : 'kartu-mewah kartu-sorot'),
]) }}>
    <div class="relative z-10">{{ $slot }}</div>
</div>
