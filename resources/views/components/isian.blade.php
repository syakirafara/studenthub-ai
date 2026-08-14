@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'petunjuk' => null,
])

@php
    $adaError = $errors->has($name);

    /*
     | Isian memakai bayangan keras seperti kartu dan tombol. Saat difokus,
     | bayangannya HILANG dan isiannya bergeser masuk sejauh bayangan tadi --
     | terbaca seperti benda yang sedang ditekan.
     |
     | Isyarat ini menggantikan cincin fokus tipis milik tema lama, yang di
     | atas tepi setebal ini nyaris tidak terlihat.
     */
    $kelas = 'mt-2 w-full border-[2.5px] bg-dasar-50 px-4 py-2.5 text-sm text-dasar-900
              placeholder:text-dasar-500 shadow-keras transition-all duration-100
              focus:translate-x-[3px] focus:translate-y-[3px] focus:shadow-none focus:outline-none '
        .($adaError ? 'border-bahaya-500 bg-bahaya-50' : 'border-dasar-900');
@endphp

<div>
    <label for="{{ $name }}"
           class="block text-xs font-bold uppercase tracking-wide text-dasar-700">
        {{ $label }}
    </label>

    <input id="{{ $name }}"
           name="{{ $name }}"
           type="{{ $type }}"
           @unless ($type === 'password') value="{{ old($name, $value) }}" @endunless
           @if ($adaError) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
           {{ $attributes->merge(['class' => $kelas]) }}>

    @if ($petunjuk && ! $adaError)
        <p class="mt-1.5 text-xs text-dasar-500">{{ $petunjuk }}</p>
    @endif

    @error($name)
        <p id="{{ $name }}-error" class="mt-1.5 text-sm font-semibold text-bahaya-600">{{ $message }}</p>
    @enderror
</div>
