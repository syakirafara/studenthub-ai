<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('judul', 'Beranda') &middot; {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex h-full flex-col bg-slate-50 text-slate-800 antialiased">

    <header class="border-b border-slate-200 bg-white">
        <nav class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-3">

            <a href="{{ route('depan') }}" class="text-lg font-semibold tracking-tight">
                StudentHub <span class="text-indigo-600">AI</span>
            </a>

            <div class="flex items-center gap-4 text-sm">
                @guest
                    <a href="{{ route('masuk') }}" class="hover:text-indigo-600">Masuk</a>
                    <a href="{{ route('daftar') }}"
                       class="rounded-md bg-indigo-600 px-3 py-1.5 font-medium text-white hover:bg-indigo-700">
                        Daftar
                    </a>
                @endguest

                @auth
                    <a href="{{ route('beranda') }}" class="hover:text-indigo-600">Beranda</a>

                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dasbor') }}" class="hover:text-indigo-600">Dasbor Admin</a>
                    @endif

                    <span class="hidden text-slate-500 sm:inline">{{ auth()->user()->name }}</span>

                    <form method="POST" action="{{ route('keluar') }}">
                        @csrf
                        <button type="submit" class="hover:text-indigo-600">Keluar</button>
                    </form>
                @endauth
            </div>

        </nav>
    </header>

    <main class="mx-auto w-full max-w-5xl flex-1 px-4 py-8">

        @if (session('sukses'))
            <div role="status"
                 class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('sukses') }}
            </div>
        @endif

        @if (session('gagal'))
            <div role="alert"
                 class="mb-6 rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                {{ session('gagal') }}
            </div>
        @endif

        @yield('isi')

    </main>

    <footer class="border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        StudentHub AI &middot; TCC Vibe Code 2026
    </footer>

</body>
</html>
