<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('judul', 'Beranda') &middot; {{ config('app.name') }}</title>

    <meta name="description" content="StudentHub AI mengumpulkan informasi lomba, beasiswa, dan magang mahasiswa dalam satu tempat, lalu membantu menilai kecocokannya dengan profilmu.">
    <meta name="theme-color" content="#efede8">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="relative flex min-h-full flex-col bg-dasar-100 font-sans text-dasar-800">

    {{--
        Latar: kisi titik saja.

        Tema sebelumnya memakai gumpalan cahaya berpendar yang bergeser
        mengikuti kursor. Di gaya ini benda semacam itu justru merusak --
        gaya ini hidup dari bidang rata dan tepi tegas, bukan dari cahaya.
        Pola geometris yang berulang jauh lebih tepat, dan biayanya nol.
    --}}
    <div class="kisi pointer-events-none fixed inset-0 -z-10" aria-hidden="true"></div>

    {{-- ================= Bilah menu =================
         Tepi bawahnya SELALU ada, bukan hanya saat digulir. Di gaya ini tepi
         adalah bagian dari bentuk, bukan hiasan yang muncul belakangan.
         Yang berubah saat digulir hanya bayangannya. --}}
    <header data-navbar
            class="sticky top-0 z-50 border-b-[2.5px] border-dasar-900 bg-dasar-50
                   transition-shadow duration-200 [&.nav-digulir]:shadow-naik">
        <nav class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-3">

            <a href="{{ route('depan') }}" class="group flex items-center gap-2.5">
                <span class="grid h-10 w-10 place-items-center border-[2.5px] border-dasar-900
                             bg-utama-500 shadow-keras transition-transform duration-150
                             group-hover:-translate-x-0.5 group-hover:-translate-y-0.5">
                    <svg viewBox="0 0 24 24" class="h-5 w-5 text-dasar-50" fill="currentColor" aria-hidden="true">
                        <path d="M12 3c0 4.97 4.03 9 9 9-4.97 0-9 4.03-9 9 0-4.97-4.03-9-9-9 4.97 0 9-4.03 9-9z"/>
                    </svg>
                </span>
                <span class="font-judul text-lg font-bold uppercase tracking-tight text-dasar-900">
                    StudentHub <span class="teks-emas">AI</span>
                </span>
            </a>

            <div class="flex items-center gap-1.5 text-sm">

                @php
                    /*
                     | Tautan menu memakai tepi TEMBUS PANDANG saat tenang, dan
                     | tepi pekat saat disentuh. Karena tebalnya sudah dipesan
                     | sejak awal, tulisannya tidak bergeser saat tepinya muncul.
                     | Kalau tepinya baru ditambahkan saat disentuh, seluruh
                     | menu akan tersentak setiap kali kursor lewat.
                     */
                    $tautan = 'border-[2.5px] border-transparent px-3 py-1.5 font-semibold
                               text-dasar-700 transition-all duration-150
                               hover:border-dasar-900 hover:bg-utama-200 hover:text-dasar-900';
                @endphp

                <a href="{{ route('peluang.index') }}" class="{{ $tautan }}">Katalog</a>
                <a href="{{ route('transparansi') }}" class="{{ $tautan }} hidden md:inline-block">Transparansi AI</a>

                @guest
                    <a href="{{ route('masuk') }}" class="{{ $tautan }}">Masuk</a>
                    <a href="{{ route('daftar') }}" class="tombol-emas px-4 py-1.5 text-sm">
                        Daftar
                    </a>
                @endguest

                @auth
                    <a href="{{ route('beranda') }}" class="{{ $tautan }} hidden sm:inline-block">Beranda</a>
                    <a href="{{ route('tersimpan.index') }}" class="{{ $tautan }} hidden sm:inline-block">Tersimpan</a>

                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dasbor') }}" class="{{ $tautan }}">Dasbor</a>
                    @endif

                    <a href="{{ route('unggah.buat') }}" class="tombol-emas px-4 py-1.5 text-sm">
                        Unggah poster
                    </a>

                    <a href="{{ route('profil.edit') }}"
                       title="Profil saya"
                       class="grid h-9 w-9 place-items-center border-[2.5px] border-dasar-900
                              bg-tanah-200 text-xs font-bold text-dasar-900 shadow-keras
                              transition-transform duration-150
                              hover:-translate-x-0.5 hover:-translate-y-0.5">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </a>

                    <form method="POST" action="{{ route('keluar') }}">
                        @csrf
                        <button type="submit" class="{{ $tautan }}">Keluar</button>
                    </form>
                @endauth
            </div>

        </nav>
    </header>

    {{-- ================= Isi ================= --}}
    <main class="relative z-10 mx-auto w-full max-w-6xl flex-1 px-5 py-10">

        @if (session('sukses'))
            <div role="status" data-reveal
                 class="mb-8 flex items-start gap-3 border-[2.5px] border-dasar-900 bg-sukses-100
                        px-5 py-4 text-sm font-medium text-sukses-800 shadow-keras">
                <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center border-2
                             border-dasar-900 bg-sukses-400 text-dasar-900">
                    <svg viewBox="0 0 20 20" class="h-3 w-3" fill="currentColor" aria-hidden="true">
                        <path d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0z"/>
                    </svg>
                </span>
                <span>{{ session('sukses') }}</span>
            </div>
        @endif

        @if (session('gagal'))
            <div role="alert" data-reveal
                 class="mb-8 flex items-start gap-3 border-[2.5px] border-dasar-900 bg-bahaya-100
                        px-5 py-4 text-sm font-medium text-bahaya-800 shadow-keras">
                <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center border-2
                             border-dasar-900 bg-bahaya-400 text-dasar-50">
                    <svg viewBox="0 0 20 20" class="h-3 w-3" fill="currentColor" aria-hidden="true">
                        <path d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16zm1 11H9v-2h2v2zm0-4H9V5h2v4z"/>
                    </svg>
                </span>
                <span>{{ session('gagal') }}</span>
            </div>
        @endif

        @yield('isi')

    </main>

    {{-- ================= Kaki halaman ================= --}}
    <footer class="relative z-10 mt-16 border-t-[2.5px] border-dasar-900 bg-dasar-50">

        {{-- Pita bergaris miring — pemisah khas gaya ini. --}}
        <div class="pita h-3 border-b-[2.5px] border-dasar-900" aria-hidden="true"></div>

        <div class="mx-auto flex max-w-6xl flex-col items-center gap-5 px-5 py-10 text-center">

            <div class="flex items-center justify-center gap-6">
                <img src="{{ asset('images/logo-utm.png') }}" alt="Logo Universitas Trunojoyo Madura" class="h-11 w-auto">
                <img src="{{ asset('images/logo-tcc.png') }}" alt="Logo TCC Vibe Code 2026" class="h-12 w-auto">
                <img src="{{ asset('images/logo-triple-c.png') }}" alt="Logo Creative Computer Club" class="h-11 w-auto">
            </div>

            <p class="text-xs font-bold uppercase tracking-[0.14em] text-dasar-600">
                {{ config('app.name') }} &middot; karya untuk
                <span class="text-utama-700">TCC Vibe Code 2026</span>
            </p>
            <p class="text-xs text-dasar-500">
                Diselenggarakan oleh Creative Computer Club &middot; Universitas Trunojoyo Madura
            </p>

            <a href="{{ route('transparansi') }}"
               class="border-2 border-dasar-900 bg-dasar-100 px-3 py-1.5 text-[0.68rem] font-bold
                      uppercase tracking-wide text-dasar-800 transition-all duration-150
                      hover:-translate-x-0.5 hover:-translate-y-0.5 hover:bg-utama-200 hover:shadow-keras">
                Bagaimana AI dipakai di sini
            </a>

        </div>
    </footer>

    @stack('skrip')

</body>
</html>
