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

    $kelas = 'mt-1 w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-1 '
        . ($adaError
            ? 'border-bahaya-600 focus:border-bahaya-600 focus:ring-bahaya-600'
            : 'border-slate-300 focus:border-utama-600 focus:ring-utama-600');
@endphp

<div>
    <label for="{{ $idIsian }}" class="block text-sm font-medium text-slate-700">
        {{ $label }}
    </label>

    <select id="{{ $idIsian }}"
            name="{{ $name }}"
            @if ($adaError) aria-invalid="true" aria-describedby="{{ $idIsian }}-error" @endif
            {{ $attributes->merge(['class' => $kelas]) }}>
        @if ($kosong)
            <option value="">{{ $kosong }}</option>
        @endif
        {{ $slot }}
    </select>

    @error($kunciError)
        <p id="{{ $idIsian }}-error" class="mt-1 text-sm text-bahaya-600">{{ $message }}</p>
    @enderror
</div>
