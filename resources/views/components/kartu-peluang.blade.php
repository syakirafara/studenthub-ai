@props(['peluang'])

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
@endphp

<a href="{{ route('peluang.show', $peluang) }}"
   class="block h-full rounded-lg focus:outline-none focus:ring-2 focus:ring-utama-600 focus:ring-offset-2">
<x-kartu class="flex h-full flex-col gap-3 p-5 transition hover:border-utama-300 hover:shadow-sm">

    <div class="flex flex-wrap items-center gap-2">
        <x-lencana :warna="$warnaKategori">{{ ucfirst($peluang->kategori) }}</x-lencana>

        @if ($peluang->tingkat !== 'tidak_disebutkan')
            <x-lencana>{{ ucfirst($peluang->tingkat) }}</x-lencana>
        @endif

        @if ($peluang->biaya === 'gratis')
            <x-lencana warna="sukses">Gratis</x-lencana>
        @endif
    </div>

    <div>
        <h3 class="text-base font-semibold leading-snug text-slate-900">
            {{ $peluang->judul }}
        </h3>

        @if ($peluang->penyelenggara)
            <p class="mt-0.5 text-sm text-slate-500">{{ $peluang->penyelenggara }}</p>
        @endif
    </div>

    @if ($peluang->deskripsi)
        <p class="line-clamp-2 text-sm text-slate-600">{{ $peluang->deskripsi }}</p>
    @endif

    <div class="mt-auto flex items-center justify-between gap-2 pt-1">
        <x-lencana :warna="$warnaDeadline">{{ $peluang->teksDeadline() }}</x-lencana>

        @if ($peluang->deadline)
            <span class="text-xs text-slate-400">
                {{ $peluang->deadline->translatedFormat('d M Y') }}
            </span>
        @endif
    </div>

</x-kartu>
</a>
