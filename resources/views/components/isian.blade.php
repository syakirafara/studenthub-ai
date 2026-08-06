@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'petunjuk' => null,
])

@php
    $adaError = $errors->has($name);

    $kelas = 'mt-2 w-full rounded-xl border bg-white/4 px-4 py-2.5 text-sm text-slate-100
              placeholder:text-slate-500 transition-all duration-300 focus:outline-none
              focus:bg-white/6 '
        .($adaError
            ? 'border-bahaya-500/60 focus:border-bahaya-400 focus:ring-2 focus:ring-bahaya-500/25'
            : 'border-white/12 focus:border-utama-400/70 focus:ring-2 focus:ring-utama-500/25');
@endphp

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-300">
        {{ $label }}
    </label>

    <input id="{{ $name }}"
           name="{{ $name }}"
           type="{{ $type }}"
           @unless ($type === 'password') value="{{ old($name, $value) }}" @endunless
           @if ($adaError) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
           {{ $attributes->merge(['class' => $kelas]) }}>

    @if ($petunjuk && ! $adaError)
        <p class="mt-1.5 text-xs text-slate-500">{{ $petunjuk }}</p>
    @endif

    @error($name)
        <p id="{{ $name }}-error" class="mt-1.5 text-sm text-bahaya-300">{{ $message }}</p>
    @enderror
</div>
