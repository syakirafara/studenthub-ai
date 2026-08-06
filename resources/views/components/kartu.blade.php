@props(['datar' => false])

{{--
    Wadah kaca. Bawaannya ikut terangkat saat disentuh; beri :datar="true"
    untuk kartu yang bukan tautan, supaya tidak terasa bisa diklik padahal tidak.
--}}
<div {{ $attributes->merge([
    'class' => 'kaca kaca-tepi rounded-2xl p-6 shadow-naik '
        . ($datar ? '' : 'kartu-mewah'),
]) }}>
    {{ $slot }}
</div>
