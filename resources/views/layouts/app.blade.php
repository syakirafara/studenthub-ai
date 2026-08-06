<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('judul', 'Beranda') &middot; {{ config('app.name') }}</title>

    <meta name="description" content="StudentHub AI mengumpulkan informasi lomba, beasiswa, dan magang mahasiswa dalam satu tempat, lalu membantu menilai kecocokannya dengan profilmu.">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex h-full flex-col bg-slate-50 text-slate-800 antialiased">

    <header class="border-b border-slate-200 bg-white">
        <nav class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-3">

            <a href="{{ route('depan') }}" class="text-lg font-semibold tracking-tight">
                StudentHub <span class="text-utama-600">AI</span>
            </a>

            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('peluang.index') }}" class="hover:text-utama-600">Katalog</a>

                @guest
                    <a href="{{ route('masuk') }}" class="hover:text-utama-600">Masuk</a>
                    <a href="{{ route('daftar') }}"
                       class="rounded-md bg-utama-600 px-3 py-1.5 font-medium text-white hover:bg-utama-700">
                        Daftar
                    </a>
                @endguest

                @auth
                    <a href="{{ route('beranda') }}" class="hover:text-utama-600">Beranda</a>

                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dasbor') }}" class="hover:text-utama-600">Dasbor Admin</a>
                    @endif

                    <a href="{{ route('profil.edit') }}" class="hidden hover:text-utama-600 sm:inline">
                        {{ auth()->user()->name }}
                    </a>

                    <form method="POST" action="{{ route('keluar') }}">
                        @csrf
                        <button type="submit" class="hover:text-utama-600">Keluar</button>
                    </form>
                @endauth
            </div>

        </nav>
    </header>

    <main class="mx-auto w-full max-w-5xl flex-1 px-4 py-8">

        @if (session('sukses'))
            <div role="status"
                 class="mb-6 rounded-md border border-sukses-200 bg-sukses-50 px-4 py-3 text-sm text-sukses-800">
                {{ session('sukses') }}
            </div>
        @endif

        @if (session('gagal'))
            <div role="alert"
                 class="mb-6 rounded-md border border-bahaya-200 bg-bahaya-50 px-4 py-3 text-sm text-bahaya-800">
                {{ session('gagal') }}
            </div>
        @endif

        @yield('isi')

    </main>

    <footer class="border-t border-slate-200 py-8">
        <div class="mx-auto flex max-w-5xl flex-col items-center gap-3 px-4 text-center">

            <div class="flex items-center justify-center gap-6">
                <img src="{{ asset('images/logo-utm.png') }}"
                     alt="Logo Universitas Trunojoyo Madura" class="h-12 w-auto">
                <img src="{{ asset('images/logo-tcc.png') }}"
                     alt="Logo TCC Vibe Code 2026" class="h-12 w-auto">
                <img src="{{ asset('images/logo-triple-c.png') }}"
                     alt="Logo Creative Computer Club" class="h-12 w-auto">
            </div>

            <p class="text-xs text-slate-500">
                {{ config('app.name') }} &middot; karya untuk <strong>TCC Vibe Code 2026</strong>
            </p>

            <p class="text-xs text-slate-400">
                Diselenggarakan oleh Creative Computer Club &middot; Universitas Trunojoyo Madura
            </p>

        </div>
    </footer>

</body>
</html>
