<?php

namespace App\Http\Controllers;

use App\Models\ExtractionReview;
use App\Models\Opportunity;
use App\Services\LayananAI;
use App\Services\PengolahGambar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class UnggahPosterController extends Controller
{
    /**
     * Halaman unggah poster.
     */
    public function create(): View
    {
        return view('peluang.unggah');
    }

    /**
     * Menerima poster, membacanya dengan AI, lalu menyimpannya sebagai
     * peluang berstatus menunggu verifikasi.
     */
    public function store(Request $request, PengolahGambar $gambar, LayananAI $ai): RedirectResponse
    {
        $request->validate([
            'poster' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
        ], [
            'poster.image' => 'Berkas yang diunggah harus berupa gambar.',
            'poster.mimes' => 'Poster harus berformat JPG, PNG, atau WebP.',
            'poster.max' => 'Ukuran poster maksimal 8 MB.',
        ]);

        $berkas = $request->file('poster');
        $isiAsli = (string) file_get_contents($berkas->getRealPath());

        // 1. Perkecil dulu, baru dikirim ke AI. Menghemat token dan waktu.
        try {
            $isiKecil = $gambar->kecilkan($isiAsli);
        } catch (Throwable $e) {
            return back()->with('gagal', 'Gambar tidak dapat diproses. Coba unggah berkas lain.');
        }

        // 2. Minta AI membacanya.
        try {
            $hasilAi = $ai->bacaPoster($isiKecil, 'image/jpeg', $request->user()->id);
        } catch (Throwable $e) {
            Log::warning('Gagal membaca poster', ['pesan' => $e->getMessage()]);

            return back()->with('gagal', $e->getMessage());
        }

        // 3. Simpan poster versi kecil, bukan aslinya. Ratusan poster asli
        //    akan cepat memenuhi 1 GB jatah hosting gratis.
        $jalurPoster = 'poster/'.now()->format('Y/m').'/'.Str::uuid().'.jpg';
        Storage::disk('public')->put($jalurPoster, $isiKecil);

        // 4. Simpan peluang dan catatan bacaan AI dalam satu transaksi.
        //    Kalau salah satu gagal, tidak ada yang tersimpan setengah jadi.
        $peluang = DB::transaction(function () use ($hasilAi, $jalurPoster, $request) {
            $peluang = Opportunity::create([
                'judul' => $hasilAi['judul'] ?? 'Tanpa judul',
                'penyelenggara' => $hasilAi['penyelenggara'] ?? null,
                'kategori' => $hasilAi['kategori'] ?? 'lomba',
                'deskripsi' => $hasilAi['deskripsi'] ?? null,
                'deadline' => $this->tanggalSah($hasilAi['deadline'] ?? null),
                'biaya' => $hasilAi['biaya'] ?? 'tidak_disebutkan',
                'nominal_biaya' => $hasilAi['nominal_biaya'] ?? null,
                'tingkat' => $hasilAi['tingkat'] ?? 'tidak_disebutkan',
                'link' => $hasilAi['link'] ?? null,
                'poster_path' => $jalurPoster,
                'syarat' => $hasilAi['syarat'] ?? [],
                'status' => 'menunggu',
                'submitted_by' => $request->user()->id,
            ]);

            // Rekaman apa yang dibaca AI, sebelum siapa pun menyuntingnya.
            // Inilah pembanding untuk mengukur akurasi nanti.
            ExtractionReview::create([
                'opportunity_id' => $peluang->id,
                'hasil_ai' => $hasilAi,
            ]);

            return $peluang;
        });

        return redirect()
            ->route('unggah.periksa', $peluang)
            ->with('sukses', 'Poster berhasil dibaca. Periksa hasilnya sebelum dikirim ke admin.');
    }

    /**
     * Halaman periksa hasil bacaan AI.
     *
     * Hanya boleh dibuka oleh pengunggahnya sendiri atau admin -- peluang ini
     * masih berstatus menunggu, jadi belum boleh dilihat umum.
     */
    public function periksa(Request $request, Opportunity $peluang): View
    {
        abort_unless(
            $peluang->submitted_by === $request->user()->id || $request->user()->isAdmin(),
            404
        );

        return view('peluang.periksa', [
            'peluang' => $peluang,
            'review' => $peluang->review,
        ]);
    }

    /**
     * Menolak tanggal yang tidak masuk akal dari bacaan AI.
     *
     * AI kadang membaca "17 Agustus" pada poster lama dan menuliskannya
     * dengan tahun yang sudah lewat jauh. Lebih baik dikosongkan daripada
     * menampilkan deadline yang menyesatkan.
     */
    private function tanggalSah(?string $tanggal): ?string
    {
        if (! $tanggal) {
            return null;
        }

        try {
            $waktu = Carbon::parse($tanggal);
        } catch (Throwable) {
            return null;
        }

        $masukAkal = $waktu->between(now()->subYear(), now()->addYears(3));

        return $masukAkal ? $waktu->toDateString() : null;
    }
}
