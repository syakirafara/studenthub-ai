<?php

namespace App\Http\Controllers;

use App\Models\AiLog;
use App\Models\ExtractionReview;
use App\Services\LayananAI;
use Illuminate\View\View;

/*
 | Halaman publik yang menjelaskan bagaimana AI dipakai di sini.
 |
 | Alasan halaman ini ada: seluruh bukti pemakaian AI selama ini tersimpan di
 | Dasbor Admin, dan penilai tidak punya akun admin. Bukti yang tidak bisa
 | dilihat sama saja dengan bukti yang tidak ada.
 |
 | Semua angka di halaman ini dihitung saat halaman dibuka, langsung dari
 | ai_logs dan extraction_reviews. Tidak ada satu pun angka yang ditulis
 | tetap -- kalau datanya berubah, halamannya ikut berubah. Instruksi AI-nya
 | pun diambil dari LayananAI yang sama dengan yang dipakai memproses poster,
 | bukan dari salinan yang bisa usang diam-diam.
 */
class TransparansiController extends Controller
{
    /** Kolom yang dinilai saat mengukur akurasi. Sama dengan yang dipakai admin. */
    private const KOLOM = [
        'judul', 'penyelenggara', 'kategori', 'deskripsi', 'deadline',
        'biaya', 'nominal_biaya', 'tingkat', 'link', 'syarat',
    ];

    public function index(LayananAI $ai): View
    {
        return view('transparansi', [
            'petunjukPoster' => $ai->petunjukPoster(),
            'petunjukKecocokan' => $ai->petunjukKecocokan($this->contohProfil(), $this->contohSyarat()),
            'skemaPeluang' => LayananAI::SKEMA_PELUANG,
            'angka' => $this->angka(),
            'perKolom' => $this->perKolom(),
            'model' => [
                'berat' => config('services.gemini.model'),
                'ringan' => config('services.gemini.ringan'),
            ],
        ]);
    }

    /**
     * Angka pemakaian, dihitung saat halaman dibuka.
     */
    private function angka(): array
    {
        $total = AiLog::count();
        $gagal = AiLog::where('status', 'gagal')->count();
        $dariCache = AiLog::dariCache()->count();

        $poster = AiLog::where('jenis', 'ekstraksi_poster')->where('status', 'berhasil');
        $jumlahPoster = (clone $poster)->count();

        $ditinjau = ExtractionReview::whereNotNull('hasil_final')->count();
        $totalKolom = $ditinjau * count(self::KOLOM);
        $koreksi = (int) ExtractionReview::whereNotNull('hasil_final')->sum('jumlah_koreksi');

        return [
            'total' => $total,
            'gagal' => $gagal,
            'berhasil_persen' => $total > 0 ? (int) round(($total - $gagal) / $total * 100) : null,
            'dari_cache' => $dariCache,
            'hemat_persen' => $total > 0 ? (int) round($dariCache / $total * 100) : 0,

            'poster_dibaca' => $jumlahPoster,
            'detik_rata' => $jumlahPoster > 0 ? round((float) (clone $poster)->avg('durasi_ms') / 1000, 1) : null,
            'detik_tercepat' => $jumlahPoster > 0 ? round((float) (clone $poster)->min('durasi_ms') / 1000, 1) : null,
            'detik_terlama' => $jumlahPoster > 0 ? round((float) (clone $poster)->max('durasi_ms') / 1000, 1) : null,

            'poster_ditinjau' => $ditinjau,
            'kolom_dinilai' => $totalKolom,
            'kolom_benar' => $totalKolom - $koreksi,
            'akurasi' => $totalKolom > 0 ? round(($totalKolom - $koreksi) / $totalKolom * 100, 1) : null,
            'sempurna' => ExtractionReview::whereNotNull('hasil_final')->where('jumlah_koreksi', 0)->count(),

            'token' => (int) (AiLog::sum('token_masuk') + AiLog::sum('token_keluar')),
        ];
    }

    /**
     * Berapa kali tiap kolom perlu dikoreksi admin.
     *
     * Inilah bagian yang paling jujur di halaman ini: ia menunjukkan di mana
     * AI-nya MASIH SALAH, bukan hanya di mana ia benar.
     */
    private function perKolom(): array
    {
        $hitung = array_fill_keys(self::KOLOM, 0);

        foreach (ExtractionReview::whereNotNull('hasil_final')->get() as $review) {
            foreach ((array) $review->field_dikoreksi as $kolom) {
                if (array_key_exists($kolom, $hitung)) {
                    $hitung[$kolom]++;
                }
            }
        }

        arsort($hitung);

        return $hitung;
    }

    private function contohProfil(): array
    {
        return [
            'jurusan' => 'Teknik Informatika',
            'semester' => 5,
            'minat' => ['teknologi', 'desain'],
            'skill' => ['pemrograman', 'desain_grafis'],
            'preferensi' => ['format' => 'keduanya', 'biaya' => 'keduanya'],
        ];
    }

    private function contohSyarat(): array
    {
        return [
            'jurusan' => ['Teknik Informatika', 'Sistem Informasi'],
            'semester_min' => 3,
            'semester_maks' => 8,
            'ukuran_tim' => 3,
            'format' => 'online',
            'lainnya' => ['IPK minimal 3.00'],
        ];
    }
}
