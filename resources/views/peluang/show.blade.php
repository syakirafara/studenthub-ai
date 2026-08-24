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

    /*
     | Batang sisa waktu.
     |
     | Panjangnya dihitung terhadap jendela 60 hari, lalu MENYUSUT seiring
     | tenggat mendekat. Arah ini disengaja: batang yang memenuh saat waktu
     | menipis akan terbaca sebagai "kemajuan", persis kebalikan dari yang
     | dimaksud. Batang yang menyusut terbaca sebagai persediaan yang habis.
     */
    $sisa = $peluang->sisaHari();
    $persenWaktu = $sisa === null ? null : max(0, min(100, (int) round($sisa / 60 * 100)));

    $rupaWaktu = match ($peluang->statusDeadline()) {
        'lewat'     => ['teks' => 'text-dasar-500',   'batang' => 'from-slate-600 to-slate-500'],
        'hari_ini'  => ['teks' => 'text-bahaya-600',  'batang' => 'from-bahaya-600 to-bahaya-400'],
        'mepet'     => ['teks' => 'text-waspada-600', 'batang' => 'from-waspada-600 to-waspada-400'],
        default     => ['teks' => 'text-sukses-600',  'batang' => 'from-sukses-600 to-sukses-400'],
    };
@endphp

<a href="{{ route('peluang.index') }}" class="text-sm text-dasar-600 hover:text-utama-700">
    &larr; Kembali ke katalog
</a>

@if ($peluang->status !== 'disetujui')
    <div role="alert"
         class="mt-4  border border-dasar-900 bg-waspada-200 px-4 py-3 text-sm text-waspada-700">
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

        <h1 class="mt-3 font-judul text-3xl font-bold tracking-tight text-dasar-900 text-dasar-900 sm:text-3xl">
            {{ $peluang->judul }}
        </h1>

        @if ($peluang->penyelenggara)
            <p class="mt-1 text-dasar-600">{{ $peluang->penyelenggara }}</p>
        @endif

        @if ($peluang->deskripsi)
            <p class="mt-6 whitespace-pre-line leading-relaxed text-dasar-800">{{ $peluang->deskripsi }}</p>
        @endif

        <h2 class="mt-10 font-judul text-xl font-semibold text-dasar-900">Syarat peserta</h2>

        <dl class="kaca kaca-tepi mt-3 divide-y divide-dasar-900 overflow-hidden  shadow-naik">

            <div class="flex gap-4 px-4 py-3">
                <dt class="w-28 shrink-0 text-sm text-dasar-600">Jurusan</dt>
                <dd class="text-sm text-dasar-900">
                    {{ empty($syarat['jurusan']) ? 'Semua jurusan' : implode(', ', $syarat['jurusan']) }}
                </dd>
            </div>

            <div class="flex gap-4 px-4 py-3">
                <dt class="w-28 shrink-0 text-sm text-dasar-600">Semester</dt>
                <dd class="text-sm text-dasar-900">
                    @if ($min && $maks) Semester {{ $min }} sampai {{ $maks }}
                    @elseif ($min) Minimal semester {{ $min }}
                    @elseif ($maks) Maksimal semester {{ $maks }}
                    @else Bebas @endif
                </dd>
            </div>

            <div class="flex gap-4 px-4 py-3">
                <dt class="w-28 shrink-0 text-sm text-dasar-600">Peserta</dt>
                <dd class="text-sm text-dasar-900">
                    {{ ($syarat['ukuran_tim'] ?? 1) > 1
                        ? 'Tim maksimal ' . $syarat['ukuran_tim'] . ' orang'
                        : 'Perorangan' }}
                </dd>
            </div>

            <div class="flex gap-4 px-4 py-3">
                <dt class="w-28 shrink-0 text-sm text-dasar-600">Format</dt>
                <dd class="text-sm text-dasar-900">
                    {{ isset($syarat['format']) ? ucfirst($syarat['format']) : 'Tidak disebutkan' }}
                </dd>
            </div>

            @if (! empty($syarat['lainnya']))
                <div class="flex gap-4 px-4 py-3">
                    <dt class="w-28 shrink-0 text-sm text-dasar-600">Lainnya</dt>
                    <dd class="text-sm text-dasar-900">
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

    {{-- Panel dibuat menempel saat digulir. Halaman rincian sering panjang, dan
         tombol daftar tidak boleh ikut hilang ke atas layar. --}}
    <aside class="lg:sticky lg:top-24 lg:self-start">
        <x-kartu jarak="" datar>

            {{-- ---------- Tenggat ---------- --}}
            <div class="p-5">
                <p class="flex items-center gap-2 text-[0.68rem] font-semibold uppercase
                          tracking-[0.14em] text-dasar-500">
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="currentColor" aria-hidden="true">
                        <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm1 5v5.3l3.6 2.1-.8 1.4-4.3-2.5V7h1.5Z"/>
                    </svg>
                    Tenggat pendaftaran
                </p>

                <p class="mt-2 font-judul text-xl font-bold leading-tight text-dasar-900">
                    {{ $peluang->deadline?->translatedFormat('d F Y') ?? 'Tidak disebutkan' }}
                </p>

                @if ($persenWaktu !== null)
                    <div class="mt-3.5 h-1.5 overflow-hidden  bg-dasar-200"
                         role="img" aria-label="{{ $peluang->teksDeadline() }}">
                        <div data-batang="{{ $persenWaktu }}"
                             class="h-full  bg-gradient-to-r {{ $rupaWaktu['batang'] }}"></div>
                    </div>
                @endif

                <p class="mt-2 text-sm font-medium {{ $rupaWaktu['teks'] }}">
                    {{ $peluang->teksDeadline() }}
                </p>
            </div>

            {{-- ---------- Biaya ---------- --}}
            <div class="border-t border-dasar-900 p-5">
                <p class="flex items-center gap-2 text-[0.68rem] font-semibold uppercase
                          tracking-[0.14em] text-dasar-500">
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="currentColor" aria-hidden="true">
                        <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm2.6 6.4-1 1.1a3 3 0 0 0-1.6-.6c-.8 0-1.3.3-1.3.9 0 .5.4.8 1.5 1.1 1.7.5 2.5 1.2 2.5 2.6 0 1.3-.9 2.2-2.2 2.4V17h-1.6v-1.1a4.3 4.3 0 0 1-2.5-1.2l1-1.1c.6.5 1.3.8 2.1.8.9 0 1.4-.4 1.4-1 0-.5-.4-.8-1.6-1.2-1.6-.4-2.4-1.1-2.4-2.5 0-1.2.9-2.1 2.2-2.4V6.2h1.6v1.1c.8.1 1.5.5 1.9 1.1Z"/>
                    </svg>
                    Biaya pendaftaran
                </p>

                @if ($peluang->biaya === 'gratis')
                    <p class="mt-2 font-judul text-xl font-bold leading-tight text-sukses-600">Gratis</p>
                @elseif ($peluang->biaya === 'berbayar')
                    <p class="mt-2 font-judul text-xl font-bold leading-tight text-dasar-900">
                        <span class="text-sm font-medium text-dasar-600">Rp</span>
                        {{ number_format($peluang->nominal_biaya ?? 0, 0, ',', '.') }}
                    </p>
                @else
                    <p class="mt-2 font-judul text-xl font-bold leading-tight text-dasar-500">
                        Tidak disebutkan
                    </p>
                @endif
            </div>

            {{-- ---------- Aksi ----------
                 Diberi latar sedikit berbeda supaya terbaca sebagai daerah
                 tempat menekan, bukan lanjutan daftar keterangan di atasnya. --}}
            <div class="space-y-3 border-t border-dasar-900 bg-dasar-100 p-5">

                @if ($peluang->link)
                    <x-tombol :href="$peluang->link" class="w-full" target="_blank" rel="noopener noreferrer">
                        Daftar di situs penyelenggara
                        <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none"
                             stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M13.5 4.5h6v6M19.5 4.5 10 14M18 14.5v3a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 4 17.5v-9A2.5 2.5 0 0 1 6.5 6h3"/>
                        </svg>
                    </x-tombol>
                @endif

                @auth
                    <x-tombol-simpan :peluang="$peluang" :tersimpan="$tersimpan" :berlabel="true"
                                     class="flex justify-center" />
                @endauth

                @guest
                    <p class="flex items-start gap-2.5  bg-utama-200 px-3.5 py-3
                              text-xs leading-relaxed text-dasar-700 ring-1 ring-inset ring-dasar-900">
                        <svg viewBox="0 0 24 24" class="mt-px h-4 w-4 shrink-0 text-utama-600"
                             fill="currentColor" aria-hidden="true">
                            <path d="M12 3c0 4.97 4.03 9 9 9-4.97 0-9 4.03-9 9 0-4.97-4.03-9-9-9 4.97 0 9-4.03 9-9z"/>
                        </svg>
                        <span>
                            <a href="{{ route('masuk') }}"
                               class="font-semibold text-utama-700 underline-offset-2 hover:underline">Masuk</a>
                            untuk melihat seberapa cocok peluang ini dengan profilmu.
                        </span>
                    </p>
                @endguest

            </div>

        </x-kartu>

        @auth
            @if ($skor)
                <x-kartu-skor :skor="$skor" class="mt-4" />
            @elseif ($peluang->status === 'disetujui')
                <x-kartu datar class="mt-4 space-y-3">
                    <div>
                        <p class="text-sm font-medium text-dasar-800">Seberapa cocok denganmu?</p>
                        <p class="mt-1 text-sm text-dasar-600">
                            AI akan membandingkan syarat peluang ini dengan jurusan, semester,
                            minat, dan kemampuanmu.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('kecocokan.hitung', $peluang) }}" data-form-skor>
                        @csrf
                        <x-tombol class="w-full" data-tombol-skor>Hitung kecocokan</x-tombol>
                    </form>

                    <p class="text-xs text-dasar-500">
                        Perhitungan memakan {{ $perkiraanWaktu['teks'] }}. Hasilnya disimpan,
                        jadi tidak dihitung dua kali.
                        @unless ($perkiraanWaktu['terukur'])
                            <span class="block">Angka ini masih perkiraan.</span>
                        @endunless
                    </p>
                </x-kartu>
            @endif
        @endauth
    </aside>

</div>

@push('skrip')
<script>
    // Perhitungan makan beberapa detik. Tanpa penanda, tombolnya mudah
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
