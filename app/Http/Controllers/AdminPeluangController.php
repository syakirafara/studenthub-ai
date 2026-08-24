<?php

namespace App\Http\Controllers;

use App\Models\AiLog;
use App\Models\ExtractionReview;
use App\Models\Opportunity;
use App\Models\OpportunityMatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPeluangController extends Controller
{
    /**
     * Kolom yang dibandingkan antara bacaan AI dan hasil akhir admin.
     * Dari sinilah angka akurasi AI berasal.
     */
    private const KOLOM_DINILAI = [
        'judul', 'penyelenggara', 'kategori', 'deskripsi',
        'deadline', 'biaya', 'nominal_biaya', 'tingkat', 'link', 'syarat',
    ];

    /**
     * Dasbor admin: antrean verifikasi dan angka-angka pemakaian AI.
     */
    public function dasbor(): View
    {
        return view('admin.dasbor', [
            'antrean' => Opportunity::where('status', 'menunggu')
                ->with('pengunggah:id,name')
                ->oldest()
                ->paginate(10),
            'jumlah' => [
                'menunggu' => Opportunity::where('status', 'menunggu')->count(),
                'disetujui' => Opportunity::where('status', 'disetujui')->count(),
                'ditolak' => Opportunity::where('status', 'ditolak')->count(),
            ],
            'ai' => $this->angkaAi(),
        ]);
    }

    /**
     * Halaman verifikasi satu peluang.
     */
    public function periksa(Opportunity $peluang): View
    {
        return view('admin.periksa', [
            'peluang' => $peluang,
            'review' => $peluang->review,
            'duplikat' => $peluang->kemungkinanDuplikat(),
        ]);
    }

    /**
     * Menyetujui peluang, sekaligus mencatat koreksi yang dilakukan admin
     * terhadap bacaan AI.
     */
    public function setujui(Request $request, Opportunity $peluang): RedirectResponse
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'penyelenggara' => ['nullable', 'string', 'max:255'],
            'kategori' => ['required', Rule::in(['lomba', 'beasiswa', 'magang'])],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'deadline' => ['nullable', 'date'],
            'biaya' => ['required', Rule::in(['gratis', 'berbayar', 'tidak_disebutkan'])],
            'nominal_biaya' => ['nullable', 'integer', 'min:0'],
            'tingkat' => ['required', Rule::in(['kampus', 'regional', 'nasional', 'internasional', 'tidak_disebutkan'])],
            'link' => ['nullable', 'url', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $peluang, $request) {
            $peluang->update([
                ...$data,
                'status' => 'disetujui',
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
                'catatan_admin' => null,
            ]);

            $this->catatAkurasi($peluang->fresh(), $request->user()->id);
        });

        return redirect()
            ->route('admin.dasbor')
            ->with('sukses', "Peluang \"{$peluang->judul}\" disetujui dan sudah tampil di katalog.");
    }

    /**
     * Menolak peluang, wajib disertai alasan.
     */
    public function tolak(Request $request, Opportunity $peluang): RedirectResponse
    {
        $data = $request->validate([
            'catatan_admin' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'catatan_admin.required' => 'Tulis alasan penolakan supaya pengunggah tahu apa yang perlu diperbaiki.',
            'catatan_admin.min' => 'Alasan penolakan terlalu singkat, minimal 10 karakter.',
        ]);

        $peluang->update([
            'status' => 'ditolak',
            'catatan_admin' => $data['catatan_admin'],
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.dasbor')
            ->with('sukses', 'Peluang ditolak beserta alasannya.');
    }

    /**
     * Membandingkan bacaan AI dengan hasil akhir setelah dikoreksi admin.
     *
     * Inilah yang membuat klaim akurasi bisa dibuktikan dengan angka, bukan
     * sekadar diucapkan. Kolom mana yang paling sering dikoreksi juga tercatat,
     * sehingga polanya terlihat dan petunjuk AI bisa diperbaiki dari data.
     */
    private function catatAkurasi(Opportunity $peluang, int $adminId): void
    {
        $review = $peluang->review;

        if (! $review) {
            return;
        }

        $hasilAi = $review->hasil_ai ?? [];
        $hasilFinal = $peluang->only(self::KOLOM_DINILAI);

        $dikoreksi = [];

        foreach (self::KOLOM_DINILAI as $kolom) {
            $sebelum = $hasilAi[$kolom] ?? null;
            $sesudah = $hasilFinal[$kolom] ?? null;

            // Tanggal dan larik perlu disamakan bentuknya dulu, supaya
            // "2026-09-20" tidak dianggap berbeda dari objek tanggal.
            $sebelum = is_array($sebelum) ? json_encode($sebelum) : (string) $sebelum;
            $sesudah = is_array($sesudah) ? json_encode($sesudah) : (string) $sesudah;

            if (trim($sebelum) !== trim($sesudah)) {
                $dikoreksi[] = $kolom;
            }
        }

        $review->update([
            'hasil_final' => $hasilFinal,
            'jumlah_koreksi' => count($dikoreksi),
            'field_dikoreksi' => $dikoreksi,
            'reviewed_by' => $adminId,
        ]);
    }

    /**
     * Angka pemakaian AI untuk ditampilkan di dasbor -- dan nanti di slide.
     */
    private function angkaAi(): array
    {
        $total = AiLog::count();
        $dariCache = AiLog::dariCache()->count();
        $gagal = AiLog::where('status', 'gagal')->count();

        $ditinjau = ExtractionReview::whereNotNull('hasil_final')->count();
        $totalKolom = $ditinjau * count(self::KOLOM_DINILAI);
        $totalKoreksi = (int) ExtractionReview::whereNotNull('hasil_final')->sum('jumlah_koreksi');

        return [
            'total' => $total,
            'dari_cache' => $dariCache,
            'hemat_persen' => $total > 0 ? (int) round($dariCache / $total * 100) : 0,
            'gagal' => $gagal,
            'berhasil_persen' => $total > 0 ? (int) round(($total - $gagal) / $total * 100) : 0,
            'durasi_rata' => (int) round((float) AiLog::where('status', 'berhasil')->avg('durasi_ms')),
            'token_total' => (int) (AiLog::sum('token_masuk') + AiLog::sum('token_keluar')),
            'poster_ditinjau' => $ditinjau,
            'akurasi_persen' => $totalKolom > 0 ? (int) round(($totalKolom - $totalKoreksi) / $totalKolom * 100) : null,

            /*
             | Dua angka di bawah ini ada untuk MENJELASKAN kejanggalan, bukan
             | untuk dipamerkan.
             |
             | Skor kecocokan bawaan seeder ditulis langsung ke tabel dan tidak
             | pernah melewati LayananAI -- memang disengaja, agar mengisi data
             | contoh tidak membakar kuota AI. Akibatnya jumlah skor tersimpan
             | jauh melebihi jumlah panggilan AI yang tercatat, dan itu terlihat
             | seperti kesalahan padahal bukan.
             |
             | Selisihnya ditampilkan terang-terangan di dasbor. Angka yang
             | tampak janggal tanpa keterangan akan dibaca sebagai kekeliruan;
             | angka janggal yang dijelaskan sendiri justru menunjukkan bahwa
             | sistemnya dipahami.
             */
            'skor_tersimpan' => OpportunityMatch::count(),
            'skor_dari_ai' => AiLog::where('jenis', 'skor_kecocokan')->count(),
        ];
    }
}
