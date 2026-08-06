@props([
    'peluang',
    'tersimpan' => false,
    'skor' => null,
])

@php
    /*
     | Setiap kategori punya satu warna DAN satu lambang. Dua penanda untuk hal
     | yang sama memang sengaja: warna terbaca sekejap saat menyapu halaman,
     | lambang terbaca oleh mata yang sulit membedakan warna. Kartu yang hanya
     | mengandalkan warna akan gagal pada sebagian pembaca.
     */
    $rupa = match ($peluang->kategori) {
        'lomba' => [
            'label' => 'Lomba',
            'teks' => 'text-utama-200',
            'latar' => 'from-utama-400/25 to-utama-600/10',
            'tepi' => 'ring-utama-400/25',
            'garis' => 'from-utama-300 via-utama-500 to-transparent',
            // Piala
            'ikon' => 'M8 4h8v1.5h3v2.2a3.8 3.8 0 0 1-3.3 3.8A4 4 0 0 1 13 13.6V16h2.5a1 1 0 0 1 0 2h-7a1 1 0 0 1 0-2H11v-2.4a4 4 0 0 1-2.7-2.1A3.8 3.8 0 0 1 5 7.7V5.5h3V4Zm0 3.5H6.8v.2c0 .9.5 1.6 1.2 2V7.5Zm8 2.2c.7-.4 1.2-1.1 1.2-2v-.2H16v2.2Z',
        ],
        'beasiswa' => [
            'label' => 'Beasiswa',
            'teks' => 'text-sukses-200',
            'latar' => 'from-sukses-400/25 to-sukses-600/10',
            'tepi' => 'ring-sukses-400/25',
            'garis' => 'from-sukses-300 via-sukses-500 to-transparent',
            // Topi wisuda
            'ikon' => 'M12 3 1.5 8.2 12 13.4l8.5-4.2v5.1a1 1 0 0 0 2 0V8.2L12 3ZM5.5 11.6v3.6c0 .5.3 1 .8 1.3a12.6 12.6 0 0 0 11.4 0c.5-.3.8-.8.8-1.3v-3.6L12 14.9l-6.5-3.3Z',
        ],
        'magang' => [
            'label' => 'Magang',
            'teks' => 'text-langit-200',
            'latar' => 'from-langit-400/25 to-langit-600/10',
            'tepi' => 'ring-langit-400/25',
            'garis' => 'from-langit-300 via-langit-500 to-transparent',
            // Tas kerja
            'ikon' => 'M9 4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V6h3.5A2.5 2.5 0 0 1 21 8.5v2.1a24 24 0 0 1-18 0V8.5A2.5 2.5 0 0 1 5.5 6H9V4.5ZM10.8 6h2.4V4.8h-2.4V6ZM3 12.6a26 26 0 0 0 7.9 1.7v.9a1.1 1.1 0 0 0 2.2 0v-.9A26 26 0 0 0 21 12.6v4.9A2.5 2.5 0 0 1 18.5 20h-13A2.5 2.5 0 0 1 3 17.5v-4.9Z',
        ],
        default => [
            'label' => ucfirst($peluang->kategori),
            'teks' => 'text-slate-200',
            'latar' => 'from-white/15 to-white/5',
            'tepi' => 'ring-white/15',
            'garis' => 'from-slate-300 via-slate-500 to-transparent',
            // Percikan
            'ikon' => 'M12 3c0 4.97 4.03 9 9 9-4.97 0-9 4.03-9 9 0-4.97-4.03-9-9-9 4.97 0 9-4.03 9-9z',
        ],
    };

    $status = $peluang->statusDeadline();

    $warnaWaktu = match ($status) {
        'lewat' => 'text-slate-500',
        'hari_ini' => 'text-bahaya-300',
        'mepet' => 'text-waspada-300',
        default => 'text-slate-400',
    };

    $gayaSkor = $skor ? match ($skor->warna()) {
        'utama' => 'text-utama-300',
        'waspada' => 'text-waspada-300',
        default => 'text-slate-400',
    } : '';
@endphp

{{--
    Kartu ini TIDAK dibungkus tautan, melainkan memakai pola "tautan melar":
    judulnya yang menjadi tautan, lalu direntangkan menutupi seluruh kartu lewat
    after:absolute after:inset-0. Dengan begitu seluruh kartu tetap bisa diklik,
    tetapi tombol simpan di dalamnya tidak menjadi tombol di dalam tautan --
    susunan yang tidak sah di HTML dan membuat kliknya bentrok.
--}}
<x-kartu data-tilt="4" jarak=""
         class="group relative flex h-full flex-col
                focus-within:ring-2 focus-within:ring-utama-500/40">

    {{-- Garis aksen di bibir atas kartu. Diam saat tenang, menyala saat
         disentuh -- penanda kategori sekaligus umpan balik sentuhan. --}}
    <span aria-hidden="true"
          class="absolute inset-x-0 top-0 h-px bg-gradient-to-r opacity-0
                 transition-opacity duration-500 group-hover:opacity-100 {{ $rupa['garis'] }}"></span>

    @if ($skor)
        {{-- Skor ditaruh paling atas: inilah alasan kartu ini muncul di sini. --}}
        <div class="flex items-center gap-2.5 border-b border-white/8 bg-white/[0.02] px-5 py-3">
            <span class="font-judul text-2xl font-bold leading-none {{ $gayaSkor }}">{{ $skor->skor }}</span>
            <span class="text-[0.7rem] leading-tight text-slate-400">
                dari 100<br>{{ $skor->keterangan() }}
            </span>
        </div>
    @endif

    <div class="flex flex-1 flex-col gap-4 p-5">

        {{-- ---------- Kepala: lambang + jenis ---------- --}}
        <div class="flex items-start gap-3">

            <span aria-hidden="true"
                  class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br
                         ring-1 ring-inset transition-transform duration-500 group-hover:scale-105
                         {{ $rupa['latar'] }} {{ $rupa['tepi'] }} {{ $rupa['teks'] }}">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor">
                    <path d="{{ $rupa['ikon'] }}"/>
                </svg>
            </span>

            <div class="min-w-0 flex-1 pt-0.5">
                {{-- Baris jenis dibuat kecil dan berjarak huruf lebar supaya
                     terbaca sebagai keterangan, bukan bersaing dengan judul. --}}
                <p class="text-[0.68rem] font-semibold uppercase tracking-[0.14em] {{ $rupa['teks'] }}">
                    {{ $rupa['label'] }}
                    @if ($peluang->tingkat !== 'tidak_disebutkan')
                        <span class="text-slate-500">&middot;</span>
                        <span class="text-slate-400">{{ ucfirst($peluang->tingkat) }}</span>
                    @endif
                </p>

                @if ($peluang->penyelenggara)
                    <p class="mt-0.5 truncate text-xs text-slate-500" title="{{ $peluang->penyelenggara }}">
                        {{ $peluang->penyelenggara }}
                    </p>
                @endif
            </div>

            @if ($peluang->biaya === 'gratis')
                <span class="shrink-0 rounded-md bg-sukses-500/12 px-2 py-0.5 text-[0.68rem]
                             font-semibold text-sukses-300 ring-1 ring-inset ring-sukses-400/25">
                    Gratis
                </span>
            @endif
        </div>

        {{-- ---------- Judul: satu-satunya tulisan besar ---------- --}}
        <div>
            <h3 class="font-judul text-[1.05rem] font-semibold leading-snug text-white">
                <a href="{{ route('peluang.show', $peluang) }}"
                   class="transition-colors duration-300 after:absolute after:inset-0
                          focus:outline-none group-hover:text-utama-100">
                    {{ $peluang->judul }}
                </a>
            </h3>

            @if ($peluang->deskripsi)
                <p class="mt-1.5 line-clamp-2 text-sm leading-relaxed text-slate-400">
                    {{ $peluang->deskripsi }}
                </p>
            @endif
        </div>

        {{-- ---------- Kaki: satu baris waktu ---------- --}}
        <div class="mt-auto flex items-center justify-between gap-2 border-t border-white/6 pt-3.5">

            <p class="flex min-w-0 items-center gap-1.5 text-xs {{ $warnaWaktu }}">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 shrink-0" fill="currentColor" aria-hidden="true">
                    <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm1 5v5.3l3.6 2.1-.8 1.4-4.3-2.5V7h1.5Z"/>
                </svg>

                <span class="truncate">
                    {{ $peluang->teksDeadline() }}
                    @if ($peluang->deadline)
                        <span class="text-slate-600">&middot;</span>
                        <span class="text-slate-500">{{ $peluang->deadline->translatedFormat('d M Y') }}</span>
                    @endif
                </span>
            </p>

            @auth
                {{-- relative z-10 menaikkan tombol di atas tautan yang melar tadi --}}
                <x-tombol-simpan :peluang="$peluang" :tersimpan="$tersimpan" class="relative z-10 shrink-0" />
            @endauth
        </div>

    </div>

</x-kartu>
