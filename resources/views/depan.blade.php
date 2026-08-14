@extends('layouts.app')

@section('judul', 'Semua peluang mahasiswa dalam satu tempat')

@section('isi')

{{-- =====================================================================
   | HERO
   ===================================================================== --}}
<section class="relative -mt-4 py-14 sm:py-20">

    <div class="mx-auto max-w-3xl text-center">

        {{-- Label dimiringkan sedikit supaya terbaca seperti stiker yang
             ditempel, bukan kotak yang digambar. Kemiringannya kecil dengan
             sengaja: lebih dari dua derajat mulai terasa berantakan. --}}
        <div data-reveal class="mb-7 flex justify-center">
            <span class="stiker kaca inline-flex items-center gap-2.5 px-4 py-1.5 text-xs
                         font-bold uppercase tracking-wide text-dasar-800 shadow-keras">
                <span class="h-2 w-2 bg-utama-500"></span>
                Ditenagai AI &middot; karya untuk TCC Vibe Code 2026
            </span>
        </div>

        <h1 data-reveal data-reveal-jeda="90"
            class="font-judul text-[2.6rem] font-bold uppercase leading-[0.95] tracking-[-0.04em]
                   text-dasar-900 sm:text-7xl">
            Semua peluang<br class="hidden sm:block">
            mahasiswa,
            <span class="teks-emas">satu tempat</span>
        </h1>

        <p data-reveal data-reveal-jeda="180"
           class="mx-auto mt-6 max-w-xl text-base leading-relaxed text-dasar-700 sm:text-lg">
            Lomba, beasiswa, dan magang dikumpulkan bersama, lalu dibaca AI supaya kamu tahu
            <span class="font-medium text-dasar-900">mana yang benar-benar cocok denganmu</span>
            sebelum mendaftar.
        </p>

        <div data-reveal data-reveal-jeda="270"
             class="mt-9 flex flex-wrap items-center justify-center gap-3">
            @guest
                <div data-magnet="10">
                    <x-tombol :href="route('daftar')" class="px-7 py-3 text-base">
                        Mulai sekarang
                        <svg viewBox="0 0 20 20" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                            <path d="M10.5 3.5 16 9l-5.5 5.5-1.1-1.1 3.6-3.6H4V8.2h9l-3.6-3.6z"/>
                        </svg>
                    </x-tombol>
                </div>
                <x-tombol :href="route('peluang.index')" jenis="kedua" class="px-7 py-3 text-base">
                    Lihat katalog
                </x-tombol>
            @endguest

            @auth
                <div data-magnet="10">
                    <x-tombol :href="route('beranda')" class="px-7 py-3 text-base">Buka beranda</x-tombol>
                </div>
                <x-tombol :href="route('unggah.buat')" jenis="kedua" class="px-7 py-3 text-base">
                    Unggah poster
                </x-tombol>
            @endauth
        </div>

        {{-- Angka nyata dari basis data, bukan hiasan --}}
        <div data-reveal data-reveal-jeda="360" data-reveal-grup
             class="mx-auto mt-14 grid max-w-lg grid-cols-3 gap-4">
            @foreach ([
                ['peluang', 'Peluang aktif'],
                ['kategori', 'Kategori'],
                ['kontributor', 'Kontributor'],
            ] as [$kunci, $label])
                <div data-reveal-anak class="kaca px-3 py-5 shadow-keras">
                    <p class="font-judul text-3xl font-bold tabular-nums text-dasar-900"
                       data-hitung="{{ $jumlah[$kunci] }}">0</p>
                    <p class="mt-1 text-[0.68rem] font-bold uppercase tracking-wide text-dasar-600">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-14 flex justify-center" aria-hidden="true">
            <svg viewBox="0 0 24 24" class="turun-naik h-5 w-5 text-dasar-600" fill="none"
                 stroke="currentColor" stroke-width="1.6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
            </svg>
        </div>

    </div>
</section>

{{-- =====================================================================
   | CARA KERJA
   ===================================================================== --}}
<section class="py-16">

    <div data-reveal class="mx-auto max-w-2xl text-center">
        <p class="text-xs font-medium uppercase tracking-[0.2em] text-utama-600">Cara kerja</p>
        <h2 class="mt-3 font-judul text-3xl font-bold tracking-tight text-dasar-900 sm:text-4xl">
            Tiga langkah, tanpa mengetik ulang apa pun
        </h2>
    </div>

    <div data-reveal-grup class="mt-12 grid gap-5 md:grid-cols-3">

        @foreach ([
            [
                '01',
                'Unggah posternya',
                'Temukan poster lomba di Instagram? Cukup unggah screenshot-nya. Tidak perlu mengetik judul, tanggal, atau syarat satu per satu.',
                'M12 16.5V7.5m0 0L8.25 11.25M12 7.5l3.75 3.75M3 16.5v1.875A2.625 2.625 0 0 0 5.625 21h12.75A2.625 2.625 0 0 0 21 18.375V16.5',
            ],
            [
                '02',
                'AI membacanya',
                'Judul, penyelenggara, deadline, biaya, dan syarat peserta diambil otomatis menjadi data terstruktur. Yang tidak tertulis di poster dikosongkan, bukan ditebak.',
                'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z',
            ],
            [
                '03',
                'Tahu cocok atau tidak',
                'AI membandingkan syaratnya dengan jurusan, semester, minat, dan kemampuanmu. Hasilnya skor 0-100 beserta apa yang masih perlu disiapkan.',
                'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
            ],
        ] as [$nomor, $judul, $isi, $ikon])
            <div data-reveal-anak>
                <x-kartu class="h-full">
                    <div class="flex items-start justify-between">
                        <span class="grid h-12 w-12 place-items-center border-[2.5px] border-dasar-900
                                     bg-utama-300 text-utama-800 shadow-keras">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor"
                                 stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikon }}"/>
                            </svg>
                        </span>
                        <span class="font-judul text-4xl font-bold leading-none text-dasar-300">{{ $nomor }}</span>
                    </div>

                    <h3 class="mt-5 font-judul text-lg font-bold uppercase text-dasar-900">{{ $judul }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-dasar-600">{{ $isi }}</p>
                </x-kartu>
            </div>
        @endforeach

    </div>
</section>

{{-- =====================================================================
   | PEMBEDA
   ===================================================================== --}}
<section class="py-16">

    <div class="grid gap-10 lg:grid-cols-2 lg:items-center">

        <div data-reveal>
            <p class="text-xs font-medium uppercase tracking-[0.2em] text-utama-600">Yang membedakan</p>
            <h2 class="mt-3 font-judul text-3xl font-bold leading-tight tracking-tight text-dasar-900 sm:text-4xl">
                Bukan sekadar katalog.<br>
                <span class="teks-emas">Penjaga syarat.</span>
            </h2>
            <p class="mt-5 text-base leading-relaxed text-dasar-700">
                Katalog peluang sudah banyak. Yang belum ada adalah yang mau berterus terang
                bahwa kamu <em class="not-italic text-dasar-900">belum</em> memenuhi syarat &mdash;
                sebelum kamu terlanjur mendaftar dan tersingkir di tahap administrasi.
            </p>

            <ul class="mt-7 space-y-3.5" data-reveal-grup>
                @foreach ([
                    'Skor jujur, tidak dikatrol supaya terdengar menyenangkan',
                    'Menyebut syarat yang belum terpenuhi, bukan hanya yang sudah',
                    'Memberi saran yang bisa langsung dikerjakan',
                    'Akurasi pembacaan diukur dari koreksi admin, bukan diklaim',
                ] as $butir)
                    <li data-reveal-anak class="flex gap-3 text-sm text-dasar-700">
                        <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center border-2
                                     border-dasar-900 bg-utama-300 text-utama-800" aria-hidden="true">
                            <svg viewBox="0 0 20 20" class="h-3 w-3" fill="currentColor">
                                <path d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0z"/>
                            </svg>
                        </span>
                        <span>{{ $butir }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Pratinjau kartu skor. Dimiringkan sedikit supaya terbaca sebagai
             contoh yang ditempel, bukan sebagai bagian tata letak. --}}
        <div data-reveal data-reveal-jeda="140" class="relative">
            <x-kartu datar class="stiker-kanan space-y-5 shadow-melayang">
                <div>
                    <p class="text-[0.68rem] font-bold uppercase tracking-[0.16em] text-dasar-500">
                        Kecocokan denganmu
                    </p>
                    <div class="mt-2 flex items-baseline gap-2.5">
                        <span class="font-judul text-6xl font-bold leading-none tabular-nums text-utama-700"
                              data-hitung="83">0</span>
                        <span class="text-sm font-semibold text-dasar-500">dari 100</span>
                    </div>
                    <p class="mt-1.5 text-sm font-bold uppercase text-dasar-800">Sangat cocok untukmu</p>

                    {{-- Batang diberi tepi penuh, bukan sekadar bidang warna.
                         Tanpa tepi, batang yang hampir penuh sulit dibedakan
                         dari wadahnya. --}}
                    <div class="mt-3 h-4 w-full border-2 border-dasar-900 bg-dasar-200">
                        <div class="h-full bg-utama-500" data-batang="83"></div>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    <p class="flex gap-2.5 text-dasar-800">
                        <span class="text-utama-600" aria-hidden="true">&check;</span>
                        Jurusan sesuai dengan syarat
                    </p>
                    <p class="flex gap-2.5 text-dasar-800">
                        <span class="text-utama-600" aria-hidden="true">&check;</span>
                        Semester memenuhi batas minimum
                    </p>
                    <p class="flex gap-2.5 text-dasar-800">
                        <span class="mt-1 h-3 w-3 shrink-0 border-2 border-dasar-900 bg-waspada-400"
                              aria-hidden="true"></span>
                        Perlu tim 3 orang &mdash; kamu belum punya
                    </p>
                </div>

                <div class="border-[2.5px] border-dasar-900 bg-utama-200 p-4">
                    <p class="text-[0.68rem] font-bold uppercase tracking-[0.16em] text-utama-800">Saran</p>
                    <p class="mt-1.5 text-sm leading-relaxed text-dasar-800">
                        Ajak dua rekan satu jurusan, dan gunakan tugas kuliahmu sebagai portofolio.
                    </p>
                </div>
            </x-kartu>
        </div>

    </div>
</section>

{{-- =====================================================================
   | AJAKAN PENUTUP
   ===================================================================== --}}
<section class="py-16">
    {{-- Blok penutup dibuat GELAP, kebalikan dari seluruh halaman.
         Pembalikan warna adalah cara paling kuat menandai "ini bagian
         terakhir dan terpenting" tanpa perlu memperbesar apa pun. --}}
    <div data-reveal class="relative">

        <div class="kaca-pekat relative px-8 py-16 text-center shadow-melayang sm:px-14">
            <div class="relative">
                <h2 class="mx-auto max-w-2xl font-judul text-3xl font-bold uppercase leading-tight
                           tracking-tight text-dasar-50 sm:text-5xl">
                    Berhenti memantau<br class="hidden sm:block">
                    sepuluh akun setiap hari
                </h2>
                <p class="mx-auto mt-5 max-w-lg text-base leading-relaxed text-dasar-300">
                    Satu tempat, satu profil, dan AI yang memberitahu mana yang layak kamu kejar.
                </p>

                <div class="mt-9 flex flex-wrap items-center justify-center gap-3">
                    @guest
                        <div data-magnet="10">
                            <x-tombol :href="route('daftar')" class="px-7 py-3 text-base">Buat akun gratis</x-tombol>
                        </div>
                        <x-tombol :href="route('peluang.index')" jenis="kedua" class="px-7 py-3 text-base">
                            Lihat dulu isinya
                        </x-tombol>
                    @endguest

                    @auth
                        <div data-magnet="10">
                            <x-tombol :href="route('peluang.index')" class="px-7 py-3 text-base">Jelajahi katalog</x-tombol>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

    </div>
</section>

@endsection
