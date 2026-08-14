@props([
    'datar' => false,
    // Jarak dalam ditaruh di prop, bukan langsung di kelas, karena kelas Tailwind
    // yang bentrok (p-6 lawan p-0) dimenangkan oleh urutan di berkas CSS -- bukan
    // oleh urutan penulisan di sini. Prop membuat hasilnya pasti.
    'jarak' => 'p-6',
])

{{--
    Kartu bertepi tegas.

    Sudutnya dibiarkan siku, bukan dilengkungkan. Lengkungan melembutkan, dan
    gaya ini justru hidup dari ketegasan -- sudut siku dengan garis tebal
    terbaca sebagai benda yang dicetak, bukan digambar.

    Beri :datar="true" untuk kartu yang bukan tautan, supaya tidak terangkat
    saat disentuh dan tidak terasa bisa diklik padahal tidak.
--}}
<div {{ $attributes->merge([
    'class' => "kaca relative {$jarak} "
        .($datar ? 'shadow-keras' : 'kartu-mewah'),
]) }}>
    {{ $slot }}
</div>
