@extends('layouts.app')

@section('judul', 'Transparansi AI')

@section('isi')

@php
    $adaData = $angka['poster_ditinjau'] > 0;

    // Kolom yang pernah dikoreksi, dan yang tidak pernah sekali pun.
    $salah = array_filter($perKolom);
    $bersih = array_keys(array_diff_key($perKolom, $salah));
@endphp

{{-- ---------- Kepala ---------- --}}
<div data-reveal class="mx-auto max-w-3xl text-center">
    <span class="stiker kaca inline-flex items-center gap-2.5 px-4 py-1.5 text-xs
                 font-bold uppercase tracking-wide text-dasar-800 shadow-keras">
        <span class="h-2 w-2 bg-utama-500"></span>
        Terbuka untuk siapa saja
    </span>

    <h1 class="mt-6 font-judul text-4xl font-bold uppercase leading-[0.95]
               tracking-[-0.04em] text-dasar-900 sm:text-6xl">
        Bagaimana AI<br class="hidden sm:block">
        <span class="teks-emas">dipakai di sini</span>
    </h1>

    <p class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-dasar-700">
        Instruksi aslinya, bentuk data yang dipaksakan, dan angka akurasinya &mdash;
        semuanya ditampilkan apa adanya. Termasuk bagian yang masih salah.
    </p>

    <p class="mt-4 text-xs text-dasar-500">
        Seluruh angka di halaman ini dihitung saat halaman dibuka, langsung dari
        <code class="bg-dasar-200 px-1.5 py-0.5">ai_logs</code> dan
        <code class="bg-dasar-200 px-1.5 py-0.5">extraction_reviews</code>.
        Tidak ada yang ditulis tetap.
    </p>
</div>

{{-- ---------- Angka utama ---------- --}}
@if ($adaData)
    <div data-reveal-grup class="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['Akurasi pembacaan', $angka['akurasi'].'%', $angka['kolom_benar'].' dari '.$angka['kolom_dinilai'].' kolom benar'],
            ['Dibaca sempurna', $angka['sempurna'].' / '.$angka['poster_ditinjau'], 'poster tanpa koreksi sama sekali'],
            ['Rata-rata waktu', $angka['detik_rata'].' detik', $angka['detik_tercepat'].'-'.$angka['detik_terlama'].' detik'],
            ['Token terpakai', number_format($angka['token'], 0, ',', '.'), $angka['total'].' panggilan tercatat'],
        ] as [$label, $nilai, $catatan])
            <x-kartu datar data-reveal-anak>
                <p class="text-[0.68rem] font-bold uppercase tracking-[0.14em] text-dasar-500">{{ $label }}</p>
                <p class="mt-2 font-judul text-3xl font-bold tabular-nums text-dasar-900">{{ $nilai }}</p>
                <p class="mt-1 text-xs leading-relaxed text-dasar-600">{{ $catatan }}</p>
            </x-kartu>
        @endforeach
    </div>
@endif

{{-- ---------- Dua panggilan AI ---------- --}}
<div data-reveal class="mt-16">
    <p class="text-[0.68rem] font-bold uppercase tracking-[0.16em] text-utama-700">Dua titik</p>
    <h2 class="mt-1.5 font-judul text-2xl font-bold uppercase tracking-tight text-dasar-900">
        Di mana AI dipanggil
    </h2>
    <p class="mt-1.5 max-w-2xl text-sm leading-relaxed text-dasar-700">
        Hanya dua. Keduanya menghasilkan JSON terstruktur yang disimpan ke basis data,
        bukan paragraf bebas yang ditempel ke layar.
    </p>
</div>

<div data-reveal-grup class="mt-6 grid gap-4 lg:grid-cols-2">
    @foreach ([
        ['AI-1', 'Membaca poster', $model['berat'], 'Gambar poster masuk, data terstruktur keluar. Tugas penglihatan, jadi memakai model yang lebih berat.', 'bg-utama-300', 'text-utama-800'],
        ['AI-2', 'Menghitung kecocokan', $model['ringan'], 'Membandingkan syarat peluang dengan profil mahasiswa. Hanya teks, jadi cukup model ringan.', 'bg-nila-300', 'text-nila-700'],
    ] as [$kode, $judul, $namaModel, $isi, $kotak, $teks])
        <x-kartu datar data-reveal-anak>
            <div class="flex items-start gap-3">
                <span class="grid h-11 w-11 shrink-0 place-items-center border-[2.5px] border-dasar-900
                             font-judul text-sm font-bold shadow-keras {{ $kotak }} {{ $teks }}">
                    {{ $kode }}
                </span>
                <div class="min-w-0">
                    <h3 class="font-judul text-lg font-bold uppercase text-dasar-900">{{ $judul }}</h3>
                    <p class="mt-0.5 break-all font-mono text-xs text-dasar-600">{{ $namaModel }}</p>
                </div>
            </div>
            <p class="mt-4 text-sm leading-relaxed text-dasar-700">{{ $isi }}</p>
        </x-kartu>
    @endforeach
</div>

{{-- ---------- Di mana AI masih salah ---------- --}}
@if ($adaData)
    <div data-reveal class="mt-16">
        <p class="text-[0.68rem] font-bold uppercase tracking-[0.16em] text-bahaya-600">Bagian yang jujur</p>
        <h2 class="mt-1.5 font-judul text-2xl font-bold uppercase tracking-tight text-dasar-900">
            Di mana AI masih salah
        </h2>
        <p class="mt-1.5 max-w-2xl text-sm leading-relaxed text-dasar-700">
            Setiap bacaan AI disimpan apa adanya. Saat admin memverifikasi, sistem
            membandingkan sepuluh kolom dan mencatat mana saja yang perlu dibetulkan.
            Dihitung dari <strong>{{ $angka['poster_ditinjau'] }} poster</strong> sungguhan.
        </p>
    </div>

    <x-kartu datar data-reveal class="mt-6">
        @if ($salah)
            <div class="space-y-4">
                @foreach ($salah as $kolom => $jumlah)
                    <div>
                        <div class="flex items-baseline justify-between gap-3">
                            <p class="font-mono text-sm font-bold text-dasar-900">{{ $kolom }}</p>
                            <p class="text-xs font-semibold tabular-nums text-bahaya-600">
                                dikoreksi {{ $jumlah }} dari {{ $angka['poster_ditinjau'] }}
                            </p>
                        </div>
                        <div class="mt-1.5 h-3 border-2 border-dasar-900 bg-dasar-100">
                            <div class="h-full bg-bahaya-400"
                                 data-batang="{{ round($jumlah / max(1, $angka['poster_ditinjau']) * 100) }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($bersih)
            <div class="{{ $salah ? 'mt-6 border-t-2 border-dashed border-dasar-300 pt-5' : '' }}">
                <p class="text-[0.68rem] font-bold uppercase tracking-[0.14em] text-sukses-700">
                    Tidak pernah sekali pun salah
                </p>
                <div class="mt-2.5 flex flex-wrap gap-2">
                    @foreach ($bersih as $kolom)
                        <span class="border-2 border-dasar-900 bg-sukses-200 px-2.5 py-0.5
                                     font-mono text-xs font-bold text-sukses-800">{{ $kolom }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </x-kartu>
@endif

{{-- ---------- Instruksi asli ---------- --}}
<div data-reveal class="mt-16">
    <p class="text-[0.68rem] font-bold uppercase tracking-[0.16em] text-utama-700">Apa adanya</p>
    <h2 class="mt-1.5 font-judul text-2xl font-bold uppercase tracking-tight text-dasar-900">
        Instruksi yang dikirim ke AI
    </h2>
    <p class="mt-1.5 max-w-2xl text-sm leading-relaxed text-dasar-700">
        Ditampilkan langsung dari kode yang memprosesnya, bukan dari salinan.
        Kalau instruksinya diubah, tulisan di bawah ikut berubah pada saat itu juga.
    </p>
</div>

<div data-reveal class="mt-6 space-y-4">
    @foreach ([
        ['AI-1 - membaca poster', $petunjukPoster],
        ['AI-2 - menghitung kecocokan', $petunjukKecocokan],
    ] as [$judul, $isi])
        <x-kartu datar jarak="">
            <p class="border-b-[2.5px] border-dasar-900 bg-dasar-200 px-5 py-2.5
                      text-[0.68rem] font-bold uppercase tracking-[0.14em] text-dasar-700">
                {{ $judul }}
            </p>
            <pre class="overflow-x-auto p-5 font-mono text-xs leading-relaxed text-dasar-800">{{ trim($isi) }}</pre>
        </x-kartu>
    @endforeach
</div>

{{-- ---------- Bentuk data dipaksa ---------- --}}
<div data-reveal class="mt-16">
    <p class="text-[0.68rem] font-bold uppercase tracking-[0.16em] text-utama-700">Bukan diminta</p>
    <h2 class="mt-1.5 font-judul text-2xl font-bold uppercase tracking-tight text-dasar-900">
        Bentuk balasannya dipaksa
    </h2>
    <p class="mt-1.5 max-w-2xl text-sm leading-relaxed text-dasar-700">
        Skema JSON dikirim bersama permintaan, sehingga model
        <strong>dijamin</strong> membalas dalam bentuk ini. Tidak ada kode pengurai,
        tidak ada pengulangan karena format kacau.
    </p>
</div>

<x-kartu datar jarak="" data-reveal class="mt-6">
    <pre class="overflow-x-auto p-5 font-mono text-xs leading-relaxed text-dasar-800">{{ json_encode($skemaPeluang, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
</x-kartu>

{{-- ---------- Batas ---------- --}}
<div data-reveal class="mt-16">
    <p class="text-[0.68rem] font-bold uppercase tracking-[0.16em] text-utama-700">Apa yang jebol duluan</p>
    <h2 class="mt-1.5 font-judul text-2xl font-bold uppercase tracking-tight text-dasar-900">
        Batas yang sudah dialami
    </h2>
</div>

<x-kartu datar data-reveal class="mt-6">
    <div class="space-y-4 text-sm leading-relaxed text-dasar-800">
        <p>
            <strong class="uppercase">Kuota harian model penglihatan.</strong>
            Pada paket gratis, kuota habis setelah <strong>21 panggilan dalam satu hari</strong>,
            dan sisa unggahan harus menunggu kuota disetel ulang keesokan harinya.
            Angka ini <strong>dialami</strong>, bukan diperkirakan &mdash; galatnya tercatat di
            <code class="bg-dasar-200 px-1.5 py-0.5">ai_logs</code>.
        </p>

        <p>
            Ketika itu terjadi sistem tidak rusak: galat aslinya disimpan untuk diagnosis, dan
            pengguna menerima kalimat
            <span class="border-2 border-dasar-900 bg-waspada-200 px-2 py-0.5 font-semibold text-waspada-800">
                Kuota AI hari ini sudah habis. Coba lagi besok.
            </span>
        </p>

        <p class="border-t-2 border-dashed border-dasar-300 pt-4">
            Tiga cara menaikkannya, dari yang paling murah: menyimpan hasil agar poster yang sama
            tidak diproses ulang <span class="font-semibold text-sukses-700">(sudah berjalan &mdash;
            {{ $angka['hemat_persen'] }}% panggilan dilayani dari simpanan)</span>, membatasi unggahan
            per pengguna per hari, lalu berpindah ke paket berbayar. Seluruh pemanggilan AI terkumpul
            di satu lapisan layanan, jadi perpindahan itu tidak mengubah kode fitur mana pun.
        </p>
    </div>
</x-kartu>

{{-- ---------- Penutup ---------- --}}
<div data-reveal class="mt-16">
    <div class="kaca-pekat px-8 py-14 text-center shadow-melayang sm:px-14">
        <h2 class="mx-auto max-w-2xl font-judul text-2xl font-bold uppercase leading-tight
                   tracking-tight text-dasar-50 sm:text-4xl">
            AI di sini rekan kerja,<br class="hidden sm:block"> bukan pengganti
        </h2>
        <p class="mx-auto mt-5 max-w-xl text-base leading-relaxed text-dasar-300">
            AI membaca posternya dalam hitungan detik. Manusia yang memutuskan apakah
            bacaannya benar sebelum tayang &mdash; dan setiap koreksinya dicatat, lalu
            dipakai memperbaiki instruksinya.
        </p>

        <div class="mt-9 flex flex-wrap items-center justify-center gap-3">
            <x-tombol :href="route('peluang.index')" class="px-7 py-3 text-base">Lihat katalognya</x-tombol>
            @guest
                <x-tombol :href="route('daftar')" jenis="kedua" class="px-7 py-3 text-base">Buat akun</x-tombol>
            @endguest
        </div>
    </div>
</div>

@endsection
