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

    // Perilaku fokusnya disamakan dengan x-isian: bergeser masuk dan
    // bayangannya hilang, seperti benda yang ditekan.
    $kelas = 'mt-2 w-full appearance-none border-[2.5px] bg-dasar-50 px-4 py-2.5 pr-10 text-sm
              text-dasar-900 shadow-keras transition-all duration-100
              focus:translate-x-[3px] focus:translate-y-[3px] focus:shadow-none focus:outline-none
              [&>option]:bg-dasar-50 [&>option]:text-dasar-900 '
        .($adaError ? 'border-bahaya-500 bg-bahaya-50' : 'border-dasar-900');
@endphp

<div>
    <label for="{{ $idIsian }}"
           class="block text-xs font-bold uppercase tracking-wide text-dasar-700">
        {{ $label }}
    </label>

    <div class="relative">
        <select id="{{ $idIsian }}"
                name="{{ $name }}"
                @if ($adaError) aria-invalid="true" aria-describedby="{{ $idIsian }}-error" @endif
                {{ $attributes->merge(['class' => 'peer '.$kelas]) }}>
            @if ($kosong)
                <option value="">{{ $kosong }}</option>
            @endif
            {{ $slot }}
        </select>

        {{-- Panah harus ikut bergeser bersama isiannya saat difokus. Kalau
             tidak, isiannya masuk sementara panahnya tertinggal di tempat --
             dan panah itu terlihat melayang keluar dari kotaknya. --}}
        <svg class="pointer-events-none absolute right-4 top-1/2 mt-1 h-4 w-4 -translate-y-1/2
                    text-dasar-700 transition-transform duration-100
                    peer-focus:translate-x-[3px] peer-focus:translate-y-[3px]"
             viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M5.6 7.5 10 11.9l4.4-4.4 1.1 1.1-5.5 5.5-5.5-5.5z"/>
        </svg>
    </div>

    @error($kunciError)
        <p id="{{ $idIsian }}-error" class="mt-1.5 text-sm font-semibold text-bahaya-600">{{ $message }}</p>
    @enderror
</div>
