@props(['warna' => 'abu'])

@php
    $gaya = match ($warna) {
        'utama'   => 'bg-utama-50 text-utama-700 ring-utama-200',
        'sukses'  => 'bg-sukses-50 text-sukses-700 ring-sukses-200',
        'bahaya'  => 'bg-bahaya-50 text-bahaya-700 ring-bahaya-200',
        'waspada' => 'bg-waspada-50 text-waspada-700 ring-waspada-200',
        default   => 'bg-slate-50 text-slate-600 ring-slate-200',
    };
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {$gaya}",
]) }}>
    {{ $slot }}
</span>
