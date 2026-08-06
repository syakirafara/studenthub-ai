@props(['datar' => false])

{{--
    Wadah kaca. Bawaannya ikut terangkat saat disentuh; beri :datar="true"
    untuk kartu yang bukan tautan, supaya tidak terasa bisa diklik padahal tidak.
--}}
<div @unless ($datar) data-sorot @endunless {{ $attributes->merge([
    'class' => 'kaca kaca-tepi relative overflow-hidden rounded-2xl p-6 shadow-naik '
        .($datar ? '' : 'kartu-mewah kartu-sorot'),
]) }}>
    <div class="relative z-10">{{ $slot }}</div>
</div>
