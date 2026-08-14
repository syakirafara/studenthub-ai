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
     |
     | Ketiga warnanya diambil dari tiga arah berbeda -- olive yang kehijauan,
     | tanah liat yang hangat, biru batu yang dingin. Tiga warna dari satu
     | keluarga akan terlihat mirip begitu kartunya berjejer puluhan.
     */
    $rupa = match ($peluang->kategori) {
        'lomba' => [
            'label' => 'Lomba',
            'kotak' => 'bg-utama-300',
            'teks' => 'text-utama-800',
            'pita' => 'bg-utama-500',
            // Piala
            'ikon' => 'M8 4h8v1.5h3v2.2a3.8 3.8 0 0 1-3.3 3.8A4 4 0 0 1 13 13.6V16h2.5a1 1 0 0 1 0 2h-7a1 1 0 0 1 0-2H11v-2.4a4 4 0 0 1-2.7-2.1A3.8 3.8 0 0 1 5 7.7V5.5h3V4Zm0 3.5H6.8v.2c0 .9.5 1.6 1.2 2V7.5Zm8 2.2c.7-.4 1.2-1.1 1.2-2v-.2H16v2.2Z',
        ],
        'beasiswa' => [
            'label' => 'Beasiswa',
            'kotak' => 'bg-tanah-300',
            'teks' => 'text-tanah-700',
            'pita' => 'bg-tanah-500',
            // Topi wisuda
            'ikon' => 'M12 3 1.5 8.2 12 13.4l8.5-4.2v5.1a1 1 0 0 0 2 0V8.2L12 3ZM5.5 11.6v3.6c0 .5.3 1 .8 1.3a12.6 12.6 0 0 0 11.4 0c.5-.3.8-.8.8-1.3v-3.6L12 14.9l-6.5-3.3Z',
        ],
        'magang' => [
            'label' => 'Magang',
            'kotak' => 'bg-nila-300',
            'teks' => 'text-nila-700',
            'pita' => 'bg-nila-500',
            // Tas kerja
            'ikon' => 'M9 4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V6h3.5A2.5 2.5 0 0 1 21 8.5v2.1a24 24 0 0 1-18 0V8.5A2.5 2.5 0 0 1 5.5 6H9V4.5ZM10.8 6h2.4V4.8h-2.4V6ZM3 12.6a26 26 0 0 0 7.9 1.7v.9a1.1 1.1 0 0 0 2.2 0v-.9A26 26 0 0 0 21 12.6v4.9A2.5 2.5 0 0 1 18.5 20h-13A2.5 2.5 0 0 1 3 17.5v-4.9Z',
        ],
        default => [
            'label' => ucfirst($peluang->kategori),
            'kotak' => 'bg-dasar-300',
            'teks' => 'text-dasar-800',
            'pita' => 'bg-dasar-500',
            // Percikan
            'ikon' => 'M12 3c0 4.97 4.03 9 9 9-4.97 0-9 4.03-9 9 0-4.97-4.03-9-9-9 4.97 0 9-4.03 9-9z',
        ],
    };

    $warnaWaktu = match ($peluang->statusDeadline()) {
        'lewat' => 'text-dasar-500',
        'hari_ini' => 'text-bahaya-600',
        'mepet' => 'text-waspada-700',
        default => 'text-dasar-600',
    };

    $gayaSkor = $skor ? match ($skor->warna()) {
        'utama' => 'text-utama-700',
        'waspada' => 'text-waspada-700',
        default => 'text-dasar-600',
    } : '';
@endphp

{{--
    Kartu ini TIDAK dibungkus tautan, melainkan memakai pola "tautan melar":
    judulnya yang menjadi tautan, lalu direntangkan menutupi seluruh kartu lewat
    after:absolute after:inset-0. Dengan begitu seluruh kartu tetap bisa diklik,
    tetapi tombol simpan di dalamnya tidak menjadi tombol di dalam tautan --
    susunan yang tidak sah di HTML dan membuat kliknya bentrok.
--}}
<x-kartu jarak="" class="group flex h-full flex-col">

    {{-- Pita kategori di bibir atas kartu. SELALU terlihat, bukan hanya saat
         disentuh: inilah penanda yang dipakai mata saat menyapu satu halaman
         penuh kartu, dan penanda semacam itu tidak boleh perlu dicari. --}}
    <div class="h-2 border-b-[2.5px] border-dasar-900 {{ $rupa['pita'] }}" aria-hidden="true"></div>

    @if ($skor)
        {{-- Skor ditaruh paling atas: inilah alasan kartu ini muncul di sini. --}}
        <div class="flex items-center gap-2.5 border-b-[2.5px] border-dasar-900 bg-dasar-100 px-5 py-3">
            <span class="font-judul text-3xl font-bold leading-none {{ $gayaSkor }}">{{ $skor->skor }}</span>
            <span class="text-[0.7rem] font-medium uppercase leading-tight tracking-wide text-dasar-600">
                dari 100<br>{{ $skor->keterangan() }}
            </span>
        </div>
    @endif

    <div class="flex flex-1 flex-col gap-4 p-5">

        {{-- ---------- Kepala: lambang + jenis ---------- --}}
        <div class="flex items-start gap-3">

            <span aria-hidden="true"
                  class="grid h-11 w-11 shrink-0 place-items-center border-[2.5px] border-dasar-900
                         shadow-keras transition-transform duration-150
                         group-hover:-translate-x-0.5 group-hover:-translate-y-0.5
                         {{ $rupa['kotak'] }} {{ $rupa['teks'] }}">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor">
                    <path d="{{ $rupa['ikon'] }}"/>
                </svg>
            </span>

            <div class="min-w-0 flex-1 pt-0.5">
                {{-- Baris jenis dibuat kecil dan berjarak huruf lebar supaya
                     terbaca sebagai keterangan, bukan bersaing dengan judul. --}}
                <p class="text-[0.68rem] font-bold uppercase tracking-[0.14em] {{ $rupa['teks'] }}">
                    {{ $rupa['label'] }}
                    @if ($peluang->tingkat !== 'tidak_disebutkan')
                        <span class="text-dasar-400">/</span>
                        <span class="text-dasar-600">{{ ucfirst($peluang->tingkat) }}</span>
                    @endif
                </p>

                @if ($peluang->penyelenggara)
                    <p class="mt-0.5 truncate text-xs text-dasar-500" title="{{ $peluang->penyelenggara }}">
                        {{ $peluang->penyelenggara }}
                    </p>
                @endif
            </div>

            @if ($peluang->biaya === 'gratis')
                <span class="shrink-0 border-2 border-dasar-900 bg-sukses-300 px-2 py-0.5
                             text-[0.68rem] font-bold uppercase tracking-wide text-sukses-800">
                    Gratis
                </span>
            @endif
        </div>

        {{-- ---------- Judul: satu-satunya tulisan besar ---------- --}}
        <div>
            <h3 class="font-judul text-[1.1rem] font-bold leading-snug text-dasar-900">
                <a href="{{ route('peluang.show', $peluang) }}"
                   class="after:absolute after:inset-0 focus:outline-none
                          group-hover:underline group-hover:decoration-2 group-hover:underline-offset-2">
                    {{ $peluang->judul }}
                </a>
            </h3>

            @if ($peluang->deskripsi)
                <p class="mt-1.5 line-clamp-2 text-sm leading-relaxed text-dasar-600">
                    {{ $peluang->deskripsi }}
                </p>
            @endif
        </div>

        {{-- ---------- Kaki: satu baris waktu ---------- --}}
        <div class="mt-auto flex items-center justify-between gap-2 border-t-2 border-dashed
                    border-dasar-300 pt-3.5">

            <p class="flex min-w-0 items-center gap-1.5 text-xs font-semibold {{ $warnaWaktu }}">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 shrink-0" fill="currentColor" aria-hidden="true">
                    <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm1 5v5.3l3.6 2.1-.8 1.4-4.3-2.5V7h1.5Z"/>
                </svg>

                <span class="truncate">
                    {{ $peluang->teksDeadline() }}
                    @if ($peluang->deadline)
                        <span class="text-dasar-400">/</span>
                        <span class="font-normal text-dasar-500">{{ $peluang->deadline->translatedFormat('d M Y') }}</span>
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
