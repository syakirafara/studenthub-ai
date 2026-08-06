@props(['skor'])

@php
    $warna = $skor->warna();

    $gayaAngka = match ($warna) {
        'utama' => 'text-utama-700',
        'waspada' => 'text-waspada-700',
        default => 'text-slate-500',
    };

    $gayaBatang = match ($warna) {
        'utama' => 'bg-utama-600',
        'waspada' => 'bg-waspada-600',
        default => 'bg-slate-400',
    };
@endphp

<x-kartu {{ $attributes->merge(['class' => 'space-y-4']) }}>

    <div>
        <p class="text-xs uppercase tracking-wide text-slate-400">Kecocokan denganmu</p>

        <div class="mt-1 flex items-baseline gap-2">
            <span class="text-3xl font-semibold {{ $gayaAngka }}">{{ $skor->skor }}</span>
            <span class="text-sm text-slate-500">dari 100</span>
        </div>

        <p class="mt-0.5 text-sm font-medium text-slate-700">{{ $skor->keterangan() }}</p>

        <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-100"
             role="progressbar" aria-valuenow="{{ $skor->skor }}" aria-valuemin="0" aria-valuemax="100"
             aria-label="Skor kecocokan {{ $skor->skor }} dari 100">
            <div class="h-full rounded-full {{ $gayaBatang }}" style="width: {{ $skor->skor }}%"></div>
        </div>
    </div>

    @if (! empty($skor->terpenuhi))
        <div>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Sudah kamu penuhi</p>
            <ul class="mt-1.5 space-y-1">
                @foreach ($skor->terpenuhi as $butir)
                    <li class="flex gap-2 text-sm text-slate-700">
                        <span class="text-utama-600" aria-hidden="true">&check;</span>
                        <span>{{ $butir }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (! empty($skor->belum_terpenuhi))
        <div>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Belum terpenuhi</p>
            <ul class="mt-1.5 space-y-1">
                @foreach ($skor->belum_terpenuhi as $butir)
                    <li class="flex gap-2 text-sm text-slate-700">
                        <span class="text-waspada-600" aria-hidden="true">&bull;</span>
                        <span>{{ $butir }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($skor->saran)
        <div class="rounded-md bg-slate-50 p-3">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Saran</p>
            <p class="mt-1 text-sm leading-relaxed text-slate-700">{{ $skor->saran }}</p>
        </div>
    @endif

    <p class="border-t border-slate-200 pt-3 text-xs text-slate-400">
        Dinilai AI berdasarkan profilmu. Tetap periksa syarat resmi di situs penyelenggara.
    </p>

</x-kartu>
