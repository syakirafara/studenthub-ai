@extends('layouts.app')

@section('judul', 'Unggah Poster')

@section('isi')
<div class="mx-auto max-w-xl">

    <div data-reveal>
        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-utama-300">
            Dibaca AI
        </p>
        <h1 class="mt-1.5 font-judul text-3xl font-bold tracking-tight text-white">Unggah poster</h1>
        <p class="mt-1.5 text-sm leading-relaxed text-slate-400">
            Temukan poster lomba, beasiswa, atau magang di Instagram? Unggah di sini.
            AI akan membacanya untukmu &mdash; kamu tidak perlu mengetik ulang apa pun.
        </p>
    </div>

    <form method="POST" action="{{ route('unggah.simpan') }}" enctype="multipart/form-data"
          class="mt-6 space-y-4" data-form-unggah>
        @csrf

        <x-kartu datar data-reveal data-reveal-jeda="80">
            <label for="poster" class="block text-sm font-medium text-slate-200">Berkas poster</label>

            <input id="poster" name="poster" type="file" required
                   accept="image/jpeg,image/png,image/webp"
                   @error('poster') aria-invalid="true" aria-describedby="poster-error" @enderror
                   class="mt-1 w-full rounded-lg border border-white/12 bg-white/5 p-2 text-sm
                          file:mr-3 file:rounded-lg file:border-0 file:bg-utama-500/12 file:px-3 file:py-1.5
                          file:text-sm file:font-medium file:text-utama-200 hover:file:bg-utama-500/20">

            <p class="mt-1 text-xs text-slate-400">JPG, PNG, atau WebP. Maksimal 8 MB.</p>

            @error('poster')
                <p id="poster-error" class="mt-1 text-sm text-bahaya-300">{{ $message }}</p>
            @enderror
        </x-kartu>

        {{-- jarak="p-5" lewat prop, BUKAN class="p-5".
             Kelas Tailwind yang bentrok dimenangkan oleh urutan di berkas CSS
             hasil bangunan, bukan urutan penulisan -- jadi p-6 bawaan komponen
             akan selalu menang atas p-5 yang ditulis di sini. --}}
        <x-kartu datar jarak="p-5" data-reveal data-reveal-jeda="140">
            <p class="text-[0.68rem] font-semibold uppercase tracking-[0.14em] text-slate-500">
                Yang terjadi setelah kamu kirim
            </p>

            <ol class="mt-3 space-y-2.5">
                @foreach ([
                    'Poster diperkecil dulu agar hemat kuota',
                    'AI membaca judul, syarat, deadline, dan biayanya',
                    'Kamu memeriksa hasilnya — kalau ada yang keliru, bisa dibetulkan',
                    'Admin memverifikasi sebelum tampil di katalog',
                ] as $nomor => $langkah)
                    <li class="flex items-start gap-3 text-sm leading-relaxed text-slate-300">
                        <span aria-hidden="true"
                              class="mt-px grid h-5 w-5 shrink-0 place-items-center rounded-md
                                     bg-utama-500/12 text-[0.65rem] font-semibold text-utama-300
                                     ring-1 ring-inset ring-utama-400/25">{{ $nomor + 1 }}</span>
                        {{ $langkah }}
                    </li>
                @endforeach
            </ol>

            <p class="mt-4 border-t border-white/8 pt-3 text-xs text-slate-400">
                Pembacaan memakan waktu sekitar <strong class="text-slate-300">10&ndash;20 detik</strong>.
                Jangan tutup halaman ini.
            </p>
        </x-kartu>

        <div data-reveal data-reveal-jeda="200" class="flex items-center gap-3 pt-1">
            <x-tombol data-tombol-kirim>Baca dengan AI</x-tombol>
            <x-tombol :href="route('peluang.index')" jenis="kedua">Batal</x-tombol>
        </div>

        <div data-penanda-tunggu hidden
             class="flex items-center gap-3 rounded-lg border border-utama-400/30 bg-utama-500/12 px-4 py-3"
             role="status" aria-live="polite">
            <svg class="h-5 w-5 shrink-0 animate-spin text-utama-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/>
                <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
            </svg>
            <div class="text-sm text-utama-200">
                <p class="font-medium">Sedang membaca posternya...</p>
                <p class="text-utama-200">Biasanya 10&ndash;20 detik. Jangan tutup atau segarkan halaman.</p>
            </div>
        </div>

    </form>
</div>

@push('skrip')
<script>
    // Pembacaan AI memakan 10-20 detik. Tanpa penanda, halaman terlihat
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
