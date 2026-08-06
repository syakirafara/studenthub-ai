@props([
    'name',
    'label',
    'kosong' => 'Pilih salah satu',
])

@php
    // Nama bertingkat seperti "preferensi[format]" perlu diterjemahkan:
    // Laravel menyimpan galatnya sebagai "preferensi.format", sedangkan
    // atribut id pada HTML tidak boleh mengandung tanda kurung.
    $kunciError = str_replace(['[', ']'], ['.', ''], $name);
    $idIsian = str_replace(['[', ']'], ['-', ''], $name);

    $adaError = $errors->has($kunciError);

    $kelas = 'mt-2 w-full appearance-none rounded-xl border bg-white/4 px-4 py-2.5 text-sm
              text-slate-100 transition-all duration-300 focus:outline-none focus:bg-white/6
              [&>option]:bg-dasar-850 [&>option]:text-slate-100 '
        .($adaError
            ? 'border-bahaya-500/60 focus:border-bahaya-400 focus:ring-2 focus:ring-bahaya-500/25'
            : 'border-white/12 focus:border-utama-400/70 focus:ring-2 focus:ring-utama-500/25');
@endphp

<div>
    <label for="{{ $idIsian }}" class="block text-sm font-medium text-slate-300">
        {{ $label }}
    </label>

    <div class="relative">
        <select id="{{ $idIsian }}"
                name="{{ $name }}"
                @if ($adaError) aria-invalid="true" aria-describedby="{{ $idIsian }}-error" @endif
                {{ $attributes->merge(['class' => $kelas]) }}>
            @if ($kosong)
                <option value="">{{ $kosong }}</option>
            @endif
            {{ $slot }}
        </select>

        <svg class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
             viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M5.6 7.5 10 11.9l4.4-4.4 1.1 1.1-5.5 5.5-5.5-5.5z"/>
        </svg>
    </div>

    @error($kunciError)
        <p id="{{ $idIsian }}-error" class="mt-1.5 text-sm text-bahaya-300">{{ $message }}</p>
    @enderror
</div>
