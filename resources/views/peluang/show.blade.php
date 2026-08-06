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

<a href="{{ route('peluang.index') }}" class="text-sm text-slate-400 hover:text-utama-300">
    &larr; Kembali ke katalog
</a>

@if ($peluang->status !== 'disetujui')
    <div role="alert"
         class="mt-4 rounded-lg border border-waspada-400/30 bg-waspada-500/10 px-4 py-3 text-sm text-waspada-200">
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

        <h1 class="mt-3 font-judul text-3xl font-bold tracking-tight text-white text-white sm:text-3xl">
            {{ $peluang->judul }}
        </h1>

        @if ($peluang->penyelenggara)
            <p class="mt-1 text-slate-400">{{ $peluang->penyelenggara }}</p>
        @endif

        @if ($peluang->deskripsi)
            <p class="mt-6 whitespace-pre-line leading-relaxed text-slate-200">{{ $peluang->deskripsi }}</p>
        @endif

        <h2 class="mt-10 font-judul text-xl font-semibold text-white">Syarat peserta</h2>

        <dl class="mt-3 divide-y divide-white/8 overflow-hidden rounded-xl border border-white/8 bg-white/5">

            <div class="flex gap-4 px-4 py-3">
                <dt class="w-28 shrink-0 text-sm text-slate-400">Jurusan</dt>
                <dd class="text-sm text-white">
                    {{ empty($syarat['jurusan']) ? 'Semua jurusan' : implode(', ', $syarat['jurusan']) }}
                </dd>
            </div>

            <div class="flex gap-4 px-4 py-3">
                <dt class="w-28 shrink-0 text-sm text-slate-400">Semester</dt>
                <dd class="text-sm text-white">
                    @if ($min && $maks) Semester {{ $min }} sampai {{ $maks }}
                    @elseif ($min) Minimal semester {{ $min }}
                    @elseif ($maks) Maksimal semester {{ $maks }}
                    @else Bebas @endif
                </dd>
            </div>

            <div class="flex gap-4 px-4 py-3">
                <dt class="w-28 shrink-0 text-sm text-slate-400">Peserta</dt>
                <dd class="text-sm text-white">
                    {{ ($syarat['ukuran_tim'] ?? 1) > 1
                        ? 'Tim maksimal ' . $syarat['ukuran_tim'] . ' orang'
                        : 'Perorangan' }}
                </dd>
            </div>

            <div class="flex gap-4 px-4 py-3">
                <dt class="w-28 shrink-0 text-sm text-slate-400">Format</dt>
                <dd class="text-sm text-white">
                    {{ isset($syarat['format']) ? ucfirst($syarat['format']) : 'Tidak disebutkan' }}
                </dd>
            </div>

            @if (! empty($syarat['lainnya']))
                <div class="flex gap-4 px-4 py-3">
                    <dt class="w-28 shrink-0 text-sm text-slate-400">Lainnya</dt>
                    <dd class="text-sm text-white">
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
                <p class="text-xs uppercase tracking-wide text-slate-500">Deadline</p>
                <p class="mt-1 font-medium text-white">
                    {{ $peluang->deadline?->translatedFormat('d F Y') ?? 'Tidak disebutkan' }}
                </p>
                <p class="text-sm text-slate-400">{{ $peluang->teksDeadline() }}</p>
            </div>

            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500">Biaya</p>
                <p class="mt-1 font-medium text-white">
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
                                 class="flex justify-center border-t border-white/8 pt-4" />
            @endauth

            @guest
                <p class="border-t border-white/8 pt-4 text-xs leading-relaxed text-slate-400">
                    <a href="{{ route('masuk') }}" class="font-medium text-utama-300 hover:underline">Masuk</a>
                    untuk melihat seberapa cocok peluang ini dengan profilmu.
                </p>
            @endguest

        </x-kartu>

        @auth
            @if ($skor)
                <x-kartu-skor :skor="$skor" class="mt-4" />
            @elseif ($peluang->status === 'disetujui')
                <x-kartu class="mt-4 space-y-3">
                    <div>
                        <p class="text-sm font-medium text-slate-200">Seberapa cocok denganmu?</p>
                        <p class="mt-1 text-sm text-slate-400">
                            AI akan membandingkan syarat peluang ini dengan jurusan, semester,
                            minat, dan kemampuanmu.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('kecocokan.hitung', $peluang) }}" data-form-skor>
                        @csrf
                        <x-tombol class="w-full" data-tombol-skor>Hitung kecocokan</x-tombol>
                    </form>

                    <p class="text-xs text-slate-500">Sekitar 2 detik. Hasilnya disimpan, jadi tidak dihitung dua kali.</p>
                </x-kartu>
            @endif
        @endauth
    </aside>

</div>

@push('skrip')
<script>
    // Perhitungan makan sekitar 2 detik. Tanpa penanda, tombolnya mudah
    // ditekan berulang -- dan tiap tekanan berarti satu panggilan AI lagi.
    document.querySelector('[data-form-skor]')?.addEventListener('submit', (e) => {
        const tombol = e.currentTarget.querySelector('[data-tombol-skor]');
        if (tombol) {
            tombol.disabled = true;
            tombol.textContent = 'Sedang menghitung...';
            tombol.classList.add('cursor-not-allowed', 'opacity-60');
        }
    });
</script>
@endpush

@endsection
