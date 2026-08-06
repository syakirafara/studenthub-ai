@props(['warna' => 'abu'])

@php
    // Warna tipis di atas kaca gelap. Latarnya sengaja hampir transparan --
    // lencana yang terlalu pekat merebut perhatian dari judul di sebelahnya.
    $gaya = match ($warna) {
        'utama' => 'bg-utama-500/12 text-utama-200 ring-utama-400/30',
        'sukses' => 'bg-sukses-500/12 text-sukses-200 ring-sukses-400/30',
        'bahaya' => 'bg-bahaya-500/12 text-bahaya-200 ring-bahaya-400/30',
        'waspada' => 'bg-waspada-500/12 text-waspada-200 ring-waspada-400/30',
        default => 'bg-white/6 text-slate-300 ring-white/12',
    };
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium
                tracking-tight ring-1 ring-inset backdrop-blur-sm {$gaya}",
]) }}>
    {{ $slot }}
</span>
