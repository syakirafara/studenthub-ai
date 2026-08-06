@props(['skor'])

@php
    $warna = $skor->warna();

    $gayaAngka = match ($warna) {
        'utama' => 'teks-emas',
        'waspada' => 'text-waspada-300',
        default => 'text-slate-400',
    };

    $gayaBatang = match ($warna) {
        'utama' => 'bg-gradient-to-r from-utama-300 to-utama-500 shadow-[0_0_18px_-2px_rgba(212,175,55,0.7)]',
        'waspada' => 'bg-gradient-to-r from-waspada-300 to-waspada-500',
        default => 'bg-slate-500',
    };
@endphp

<x-kartu :datar="true" {{ $attributes->merge(['class' => 'space-y-5 overflow-hidden']) }}>

    <div>
        <p class="text-xs uppercase tracking-[0.16em] text-slate-400">Kecocokan denganmu</p>

        <div class="mt-2 flex items-baseline gap-2.5">
            <span class="font-judul text-5xl font-bold leading-none {{ $gayaAngka }}"
                  data-hitung="{{ $skor->skor }}">{{ $skor->skor }}</span>
            <span class="text-sm text-slate-400">dari 100</span>
        </div>

        <p class="mt-1.5 text-sm font-medium text-slate-200">{{ $skor->keterangan() }}</p>

        <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-white/8"
             role="progressbar" aria-valuenow="{{ $skor->skor }}" aria-valuemin="0" aria-valuemax="100"
             aria-label="Skor kecocokan {{ $skor->skor }} dari 100">
            <div class="h-full rounded-full {{ $gayaBatang }}" data-batang="{{ $skor->skor }}"></div>
        </div>
    </div>

    @if (! empty($skor->terpenuhi))
        <div>
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-slate-400">Sudah kamu penuhi</p>
            <ul class="mt-2.5 space-y-2" data-reveal-grup>
                @foreach ($skor->terpenuhi as $butir)
                    <li data-reveal-anak class="flex gap-2.5 text-sm text-slate-200">
                        <span class="mt-0.5 grid h-4 w-4 shrink-0 place-items-center rounded-full
                                     bg-utama-500/18 text-utama-300" aria-hidden="true">
                            <svg viewBox="0 0 20 20" class="h-2.5 w-2.5" fill="currentColor">
                                <path d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0z"/>
                            </svg>
                        </span>
                        <span>{{ $butir }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (! empty($skor->belum_terpenuhi))
        <div>
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-slate-400">Belum terpenuhi</p>
            <ul class="mt-2.5 space-y-2" data-reveal-grup>
                @foreach ($skor->belum_terpenuhi as $butir)
                    <li data-reveal-anak class="flex gap-2.5 text-sm text-slate-200">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-waspada-400" aria-hidden="true"></span>
                        <span>{{ $butir }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($skor->saran)
        <div class="rounded-xl border border-white/8 bg-white/4 p-4">
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-utama-200">Saran</p>
            <p class="mt-1.5 text-sm leading-relaxed text-slate-300">{{ $skor->saran }}</p>
        </div>
    @endif

    <p class="border-t border-white/8 pt-4 text-xs leading-relaxed text-slate-500">
        Dinilai AI berdasarkan profilmu. Tetap periksa syarat resmi di situs penyelenggara.
    </p>

</x-kartu>
