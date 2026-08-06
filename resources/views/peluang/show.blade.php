@extends('layouts.app')

@section('judul', $peluang->judul)

@section('isi')

@php
    $warnaKategori = match ($peluang->kategori) {
        'lomba'    => 'utama',
        'beasiswa' => 'sukses',
        'magang'   => 'waspada',
        default    => 'abu',
    };

    $warnaDeadline = match ($peluang->statusDeadline()) {
        'lewat', 'hari_ini' => 'bahaya',
        'mepet'             => 'waspada',
        default             => 'abu',
    };

    $syarat = $peluang->syarat ?? [];
    $min    = $syarat['semester_min'] ?? null;
    $maks   = $syarat['semester_maks'] ?? null;
@endphp

<a href="{{ route('peluang.index') }}" class="text-sm text-slate-500 hover:text-utama-600">
    &larr; Kembali ke katalog
</a>

@if ($peluang->status !== 'disetujui')
    <div role="alert"
         class="mt-4 rounded-md border border-waspada-200 bg-waspada-50 px-4 py-3 text-sm text-waspada-800">
        Pratinjau admin. Peluang ini berstatus <strong>{{ $peluang->status }}</strong>
        dan belum tampil di katalog.
    </div>
@endif

<div class="mt-4 grid gap-8 lg:grid-cols-3">

    <div class="lg:col-span-2">

        <div class="flex flex-wrap items-center gap-2">
            <x-lencana :warna="$warnaKategori">{{ ucfirst($peluang->kategori) }}</x-lencana>

            @if ($peluang->tingkat !== 'tidak_disebutkan')
                <x-lencana>{{ ucfirst($peluang->tingkat) }}</x-lencana>
            @endif

            <x-lencana :warna="$warnaDeadline">{{ $peluang->teksDeadline() }}</x-lencana>
        </div>

        <h1 class="mt-3 text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">
            {{ $peluang->judul }}
        </h1>

        @if ($peluang->penyelenggara)
            <p class="mt-1 text-slate-500">{{ $peluang->penyelenggara }}</p>
        @endif

        @if ($peluang->deskripsi)
            <p class="mt-6 whitespace-pre-line leading-relaxed text-slate-700">{{ $peluang->deskripsi }}</p>
        @endif

        <h2 class="mt-8 text-lg font-semibold">Syarat peserta</h2>

        <dl class="mt-3 divide-y divide-slate-200 overflow-hidden rounded-lg border border-slate-200 bg-white">

            <div class="flex gap-4 px-4 py-3">
                <dt class="w-28 shrink-0 text-sm text-slate-500">Jurusan</dt>
                <dd class="text-sm text-slate-800">
                    {{ empty($syarat['jurusan']) ? 'Semua jurusan' : implode(', ', $syarat['jurusan']) }}
                </dd>
            </div>

            <div class="flex gap-4 px-4 py-3">
                <dt class="w-28 shrink-0 text-sm text-slate-500">Semester</dt>
                <dd class="text-sm text-slate-800">
                    @if ($min && $maks) Semester {{ $min }} sampai {{ $maks }}
                    @elseif ($min) Minimal semester {{ $min }}
                    @elseif ($maks) Maksimal semester {{ $maks }}
                    @else Bebas @endif
                </dd>
            </div>

            <div class="flex gap-4 px-4 py-3">
                <dt class="w-28 shrink-0 text-sm text-slate-500">Peserta</dt>
                <dd class="text-sm text-slate-800">
                    {{ ($syarat['ukuran_tim'] ?? 1) > 1
                        ? 'Tim maksimal ' . $syarat['ukuran_tim'] . ' orang'
                        : 'Perorangan' }}
                </dd>
            </div>

            <div class="flex gap-4 px-4 py-3">
                <dt class="w-28 shrink-0 text-sm text-slate-500">Format</dt>
                <dd class="text-sm text-slate-800">
                    {{ isset($syarat['format']) ? ucfirst($syarat['format']) : 'Tidak disebutkan' }}
                </dd>
            </div>

            @if (! empty($syarat['lainnya']))
                <div class="flex gap-4 px-4 py-3">
                    <dt class="w-28 shrink-0 text-sm text-slate-500">Lainnya</dt>
                    <dd class="text-sm text-slate-800">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($syarat['lainnya'] as $butir)
                                <li>{{ $butir }}</li>
                            @endforeach
                        </ul>
                    </dd>
                </div>
            @endif

        </dl>
    </div>

    <aside>
        <x-kartu class="space-y-5">

            <div>
                <p class="text-xs uppercase tracking-wide text-slate-400">Deadline</p>
                <p class="mt-1 font-medium text-slate-900">
                    {{ $peluang->deadline?->translatedFormat('d F Y') ?? 'Tidak disebutkan' }}
                </p>
                <p class="text-sm text-slate-500">{{ $peluang->teksDeadline() }}</p>
            </div>

            <div>
                <p class="text-xs uppercase tracking-wide text-slate-400">Biaya</p>
                <p class="mt-1 font-medium text-slate-900">
                    @if ($peluang->biaya === 'gratis')
                        Gratis
                    @elseif ($peluang->biaya === 'berbayar')
                        Rp {{ number_format($peluang->nominal_biaya ?? 0, 0, ',', '.') }}
                    @else
                        Tidak disebutkan
                    @endif
                </p>
            </div>

            @if ($peluang->link)
                <x-tombol :href="$peluang->link" class="w-full" target="_blank" rel="noopener noreferrer">
                    Daftar di situs penyelenggara
                </x-tombol>
            @endif

            @auth
                <x-tombol-simpan :peluang="$peluang" :tersimpan="$tersimpan" :berlabel="true"
                                 class="flex justify-center border-t border-slate-200 pt-4" />
            @endauth

            @guest
                <p class="border-t border-slate-200 pt-4 text-xs leading-relaxed text-slate-500">
                    <a href="{{ route('masuk') }}" class="font-medium text-utama-600 hover:underline">Masuk</a>
                    untuk melihat seberapa cocok peluang ini dengan profilmu.
                </p>
            @endguest

        </x-kartu>
    </aside>

</div>

@endsection
