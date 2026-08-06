@props([
    'peluang',
    'tersimpan' => false,
    'berlabel' => false,
])

@php
    $keterangan = $tersimpan ? 'Hapus dari simpanan' : 'Simpan peluang ini';
@endphp

<form method="POST"
      action="{{ $tersimpan ? route('tersimpan.destroy', $peluang) : route('tersimpan.store', $peluang) }}"
      {{ $attributes }}>
    @csrf

    @if ($tersimpan)
        @method('DELETE')
    @endif

    <button type="submit"
            title="{{ $keterangan }}"
            aria-label="{{ $keterangan }}"
            class="inline-flex items-center gap-2 rounded-md px-2 py-1.5 text-sm transition
                   focus:outline-none focus:ring-2 focus:ring-utama-600 focus:ring-offset-1
                   {{ $tersimpan
                        ? 'text-utama-700 hover:bg-utama-50'
                        : 'text-slate-400 hover:bg-slate-100 hover:text-slate-600' }}">

        <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0"
             fill="{{ $tersimpan ? 'currentColor' : 'none' }}"
             stroke="currentColor" stroke-width="1.7" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17.6 3.3c1.1.1 1.9 1.1 1.9 2.2V21L12 17.25 4.5 21V5.5c0-1.1.8-2.1 1.9-2.2a48.5 48.5 0 0 1 11.2 0Z"/>
        </svg>

        @if ($berlabel)
            <span>{{ $tersimpan ? 'Tersimpan' : 'Simpan' }}</span>
        @endif
    </button>
</form>
