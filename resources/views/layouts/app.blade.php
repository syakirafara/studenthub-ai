<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('judul', 'Beranda') &middot; {{ config('app.name') }}</title>

    <meta name="description" content="StudentHub AI mengumpulkan informasi lomba, beasiswa, dan magang mahasiswa dalam satu tempat, lalu membantu menilai kecocokannya dengan profilmu.">
    <meta name="theme-color" content="#07111f">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="relative flex min-h-full flex-col bg-dasar-900 font-sans text-slate-200">

    {{--
        Latar berlapis. Semuanya di belakang isi halaman dan tidak bisa diklik.
        Empat lapis: warna dasar, gumpalan cahaya bergerak, anyaman garis tipis,
        lalu peredup agar teks tetap terbaca di atasnya.
    --}}
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-dasar-900"></div>

        <div data-paralaks="26"
             class="gumpalan -left-40 -top-40 h-[34rem] w-[34rem] bg-utama-600/25"></div>
        <div data-paralaks="16" style="animation-delay: -7s"
             class="gumpalan -right-52 top-24 h-[30rem] w-[30rem] bg-nila-500/20"></div>
        <div data-paralaks="34" style="animation-delay: -13s"
             class="gumpalan bottom-0 left-1/3 h-[26rem] w-[26rem] bg-langit-500/12"></div>

        <div class="kisi absolute inset-0"></div>

        <div class="absolute inset-0 bg-gradient-to-b from-dasar-950/40 via-transparent to-dasar-950/85"></div>
    </div>

    {{-- ================= Bilah menu ================= --}}
    <header data-navbar
            class="sticky top-0 z-50 border-b border-transparent transition-all duration-500
                   [&.nav-digulir]:border-white/8 [&.nav-digulir]:bg-dasar-950/70
                   [&.nav-digulir]:backdrop-blur-xl [&.nav-digulir]:shadow-naik">
        <nav class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-4">

            <a href="{{ route('depan') }}" class="group flex items-center gap-2.5">
                <span class="relative grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-utama-200 to-utama-600 shadow-emas">
                    <svg viewBox="0 0 24 24" class="h-5 w-5 text-dasar-950" fill="currentColor" aria-hidden="true">
                        <path d="M12 3c0 4.97 4.03 9 9 9-4.97 0-9 4.03-9 9 0-4.97-4.03-9-9-9 4.97 0 9-4.03 9-9z"/>
                    </svg>
                </span>
                <span class="font-judul text-lg font-semibold tracking-tight text-white">
                    StudentHub <span class="teks-emas">AI</span>
                </span>
            </a>

            <div class="flex items-center gap-1 text-sm">

                @php
                    $tautan = 'relative rounded-lg px-3 py-2 text-slate-300 transition-colors duration-300 hover:text-white
                               after:absolute after:inset-x-3 after:bottom-1 after:h-px after:origin-left after:scale-x-0
                               after:bg-gradient-to-r after:from-utama-300 after:to-utama-500 after:transition-transform
                               after:duration-400 hover:after:scale-x-100';
                @endphp

                <a href="{{ route('peluang.index') }}" class="{{ $tautan }}">Katalog</a>

                @guest
                    <a href="{{ route('masuk') }}" class="{{ $tautan }}">Masuk</a>
                    <a href="{{ route('daftar') }}"
                       class="tombol-emas ml-1 rounded-lg px-4 py-2 text-sm font-semibold">
                        Daftar
                    </a>
                @endguest

                @auth
                    <a href="{{ route('beranda') }}" class="{{ $tautan }} hidden sm:inline-block">Beranda</a>
                    <a href="{{ route('tersimpan.index') }}" class="{{ $tautan }} hidden sm:inline-block">Tersimpan</a>

                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dasbor') }}" class="{{ $tautan }}">Dasbor</a>
                    @endif

                    <a href="{{ route('unggah.buat') }}"
                       class="tombol-emas ml-1 rounded-lg px-4 py-2 text-sm font-semibold">
                        Unggah poster
                    </a>

                    <a href="{{ route('profil.edit') }}"
                       title="Profil saya"
                       class="ml-1 grid h-9 w-9 place-items-center rounded-full border border-white/12 bg-white/5
                              text-xs font-semibold text-utama-200 transition-all duration-300
                              hover:border-utama-400/50 hover:bg-white/10">
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
                 class="kaca kaca-tepi mb-8 flex items-start gap-3 rounded-xl px-5 py-4 text-sm text-sukses-100">
                <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-sukses-500/20 text-sukses-400">
                    <svg viewBox="0 0 20 20" class="h-3 w-3" fill="currentColor" aria-hidden="true">
                        <path d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0z"/>
                    </svg>
                </span>
                <span>{{ session('sukses') }}</span>
            </div>
        @endif

        @if (session('gagal'))
            <div role="alert" data-reveal
                 class="kaca kaca-tepi mb-8 flex items-start gap-3 rounded-xl border-bahaya-500/25 px-5 py-4 text-sm text-bahaya-100">
                <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-bahaya-500/20 text-bahaya-400">
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
    <footer class="relative z-10 mt-16 border-t border-white/8 bg-dasar-950/50 backdrop-blur-sm">
        <div class="mx-auto flex max-w-6xl flex-col items-center gap-5 px-5 py-10 text-center">

            <div class="flex items-center justify-center gap-6 opacity-90">
                <img src="{{ asset('images/logo-utm.png') }}" alt="Logo Universitas Trunojoyo Madura" class="h-11 w-auto">
                <img src="{{ asset('images/logo-tcc.png') }}" alt="Logo TCC Vibe Code 2026" class="h-12 w-auto">
                <img src="{{ asset('images/logo-triple-c.png') }}" alt="Logo Creative Computer Club" class="h-11 w-auto">
            </div>

            <div class="h-px w-24 bg-gradient-to-r from-transparent via-utama-500/50 to-transparent"></div>

            <p class="text-xs text-slate-400">
                {{ config('app.name') }} &middot; karya untuk
                <span class="font-medium text-utama-200">TCC Vibe Code 2026</span>
            </p>
            <p class="text-xs text-slate-500">
                Diselenggarakan oleh Creative Computer Club &middot; Universitas Trunojoyo Madura
            </p>

        </div>
    </footer>

    @stack('skrip')

</body>
</html>
