@extends('layouts.app')

@section('judul', 'Unggah Poster')

@section('isi')
<div class="mx-auto max-w-xl">

    <div data-reveal>
        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-utama-600">
            Dibaca AI
        </p>
        <h1 class="mt-1.5 font-judul text-3xl font-bold tracking-tight text-dasar-900">Unggah poster</h1>
        <p class="mt-1.5 text-sm leading-relaxed text-dasar-600">
            Temukan poster lomba, beasiswa, atau magang di Instagram? Unggah di sini.
            AI akan membacanya untukmu &mdash; kamu tidak perlu mengetik ulang apa pun.
        </p>
    </div>

    <form method="POST" action="{{ route('unggah.simpan') }}" enctype="multipart/form-data"
          class="mt-6 space-y-4" data-form-unggah>
        @csrf

        <x-kartu datar data-reveal data-reveal-jeda="80">
            <label for="poster"
                   class="block text-xs font-bold uppercase tracking-wide text-dasar-700">
                Berkas poster
            </label>

            {{-- Tombol "Choose File" di dalam isian berkas hanya bisa disentuh
                 lewat awalan file:. Ia TIDAK mewarisi warna dari isian
                 induknya, jadi warnanya harus disebut sendiri -- dan inilah
                 yang paling sering terlewat saat tema berganti. --}}
            <input id="poster" name="poster" type="file" required
                   accept="image/jpeg,image/png,image/webp"
                   @error('poster') aria-invalid="true" aria-describedby="poster-error" @enderror
                   class="mt-2 w-full cursor-pointer border-[2.5px] border-dasar-900 bg-dasar-50 p-2
                          text-sm text-dasar-700 shadow-keras
                          file:mr-3 file:cursor-pointer file:border-[2.5px] file:border-dasar-900
                          file:bg-utama-500 file:px-3 file:py-1.5 file:text-sm file:font-bold
                          file:uppercase file:tracking-wide file:text-white
                          hover:file:bg-utama-600">

            <p class="mt-2 text-xs text-dasar-600">JPG, PNG, atau WebP. Maksimal 8 MB.</p>

            @error('poster')
                <p id="poster-error" class="mt-1 text-sm text-bahaya-600">{{ $message }}</p>
            @enderror
        </x-kartu>

        {{-- jarak="p-5" lewat prop, BUKAN class="p-5".
             Kelas Tailwind yang bentrok dimenangkan oleh urutan di berkas CSS
             hasil bangunan, bukan urutan penulisan -- jadi p-6 bawaan komponen
             akan selalu menang atas p-5 yang ditulis di sini. --}}
        <x-kartu datar jarak="p-5" data-reveal data-reveal-jeda="140">
            <p class="text-[0.68rem] font-semibold uppercase tracking-[0.14em] text-dasar-500">
                Yang terjadi setelah kamu kirim
            </p>

            <ol class="mt-3 space-y-2.5">
                @foreach ([
                    'Poster diperkecil dulu agar hemat kuota',
                    'AI membaca judul, syarat, deadline, dan biayanya',
                    'Kamu memeriksa hasilnya — kalau ada yang keliru, bisa dibetulkan',
                    'Admin memverifikasi sebelum tampil di katalog',
                ] as $nomor => $langkah)
                    <li class="flex items-start gap-3 text-sm leading-relaxed text-dasar-700">
                        <span aria-hidden="true"
                              class="mt-px grid h-5 w-5 shrink-0 place-items-center 
                                     bg-utama-200 text-[0.65rem] font-semibold text-utama-600
                                     ring-1 ring-inset ring-dasar-900">{{ $nomor + 1 }}</span>
                        {{ $langkah }}
                    </li>
                @endforeach
            </ol>

            {{-- Angka waktunya DIHITUNG dari ai_logs, bukan ditulis tetap.
                 Selama sampelnya masih sedikit, halaman ini mengaku bahwa
                 angkanya baru perkiraan -- bukan berlagak tahu. --}}
            <p class="mt-4 border-t-2 border-dashed border-dasar-300 pt-3 text-xs text-dasar-600">
                Pembacaan memakan waktu
                <strong class="text-dasar-800">{{ $perkiraanWaktu['teks'] }}</strong>.
                Jangan tutup halaman ini.
                <span class="mt-1 block text-dasar-500">
                    @if ($perkiraanWaktu['terukur'])
                        Dihitung dari {{ $perkiraanWaktu['jumlah'] }} pembacaan yang sudah tercatat.
                    @else
                        Masih perkiraan &mdash; baru {{ $perkiraanWaktu['jumlah'] }} pembacaan yang tercatat.
                    @endif
                </span>
            </p>
        </x-kartu>

        <div data-reveal data-reveal-jeda="200" class="flex items-center gap-3 pt-1">
            <x-tombol data-tombol-kirim>Baca dengan AI</x-tombol>
            <x-tombol :href="route('peluang.index')" jenis="kedua">Batal</x-tombol>
        </div>

        <div data-penanda-tunggu hidden
             class="flex items-center gap-3  border border-dasar-900 bg-utama-200 px-4 py-3"
             role="status" aria-live="polite">
            <svg class="h-5 w-5 shrink-0 animate-spin text-utama-600" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/>
                <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
            </svg>
            <div class="text-sm text-utama-700">
                <p class="font-medium">Sedang membaca posternya...</p>
                <p class="text-utama-700">
                    Biasanya {{ $perkiraanWaktu['teks'] }}. Jangan tutup atau segarkan halaman.
                </p>
            </div>
        </div>

    </form>
</div>

@push('skrip')
<script>
    // Pembacaan AI memakan puluhan detik. Tanpa penanda, halaman terlihat
    // membeku dan pengguna cenderung menekan tombol berulang kali -- yang
    // berarti beberapa kali panggilan AI untuk satu poster yang sama.
    document.querySelector('[data-form-unggah]')?.addEventListener('submit', (e) => {
        const tombol = e.currentTarget.querySelector('[data-tombol-kirim]');
        const penanda = e.currentTarget.querySelector('[data-penanda-tunggu]');

        if (tombol) {
            tombol.disabled = true;
            tombol.textContent = 'Sedang membaca...';
            tombol.classList.add('cursor-not-allowed', 'opacity-60');
        }

        penanda?.removeAttribute('hidden');
    });
</script>
@endpush

@endsection
