<?php

namespace App\Services;

use App\Models\AiLog;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class LayananAI
{
    /**
     * Bentuk data yang WAJIB dikembalikan AI saat membaca poster.
     *
     * Ini bukan permintaan, melainkan paksaan: Gemini dijamin mengembalikan
     * bentuk ini. Susunannya sengaja dibuat sama persis dengan kolom-kolom
     * tabel opportunities, supaya hasilnya bisa langsung disimpan.
     */
    public const SKEMA_PELUANG = [
        'type' => 'OBJECT',
        'properties' => [
            'judul' => ['type' => 'STRING'],
            'penyelenggara' => ['type' => 'STRING', 'nullable' => true],
            'kategori' => ['type' => 'STRING', 'enum' => ['lomba', 'beasiswa', 'magang']],
            'deskripsi' => ['type' => 'STRING', 'nullable' => true],
            'deadline' => ['type' => 'STRING', 'nullable' => true],
            'biaya' => ['type' => 'STRING', 'enum' => ['gratis', 'berbayar', 'tidak_disebutkan']],
            'nominal_biaya' => ['type' => 'INTEGER', 'nullable' => true],
            'tingkat' => ['type' => 'STRING', 'enum' => ['kampus', 'regional', 'nasional', 'internasional', 'tidak_disebutkan']],
            'link' => ['type' => 'STRING', 'nullable' => true],
            'syarat' => [
                'type' => 'OBJECT',
                'properties' => [
                    'jurusan' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                    'semester_min' => ['type' => 'INTEGER', 'nullable' => true],
                    'semester_maks' => ['type' => 'INTEGER', 'nullable' => true],
                    'ukuran_tim' => ['type' => 'INTEGER'],
                    'format' => ['type' => 'STRING', 'enum' => ['online', 'offline', 'hybrid'], 'nullable' => true],
                    'lainnya' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                ],
                'required' => ['jurusan', 'ukuran_tim', 'lainnya'],
            ],
        ],
        'required' => ['judul', 'kategori', 'biaya', 'tingkat', 'syarat'],
    ];

    /**
     * Membaca satu poster dan mengubahnya menjadi data terstruktur.
     */
    /**
     * Instruksi yang dikirim bersama gambar poster.
     *
     * Dibuat publik supaya halaman Transparansi AI dapat menampilkan
     * instruksi YANG BENAR-BENAR DIPAKAI, bukan salinannya. Salinan akan
     * usang diam-diam begitu instruksi aslinya diubah, dan halaman yang
     * mengaku transparan justru jadi menyesatkan.
     */
    public function petunjukPoster(): string
    {
        /*
         | Tanggal hari ini WAJIB disebutkan di dalam instruksi.
         |
         | Versi sebelumnya menulis "gunakan tahun berjalan" -- perintah yang
         | mustahil dipatuhi, karena model bahasa tidak punya jam. Ia tidak
         | tahu sekarang tahun berapa, sehingga jatuh ke tahun masa
         | pelatihannya. Akibatnya 4 dari 20 poster dibaca sebagai 2024
         | padahal acaranya 2026.
         |
         | Diukur dari extraction_reviews, bukan diduga.
         */
        $hariIni = now()->translatedFormat('l, j F Y');
        $tahunIni = now()->year;

        $petunjuk = <<<TEKS
        Kamu membaca poster informasi peluang mahasiswa Indonesia (lomba, beasiswa, atau magang).

        HARI INI: {$hariIni}. Tahun berjalan adalah {$tahunIni}.

        Ambil datanya sesuai skema yang diberikan. Aturan:

        - Tulis apa adanya sesuai poster. JANGAN mengarang data yang tidak tertulis.
        - Kalau sebuah keterangan tidak ada di poster, isi null (atau daftar kosong).
        - deadline: format YYYY-MM-DD. Kalau posternya menulis tahun, PAKAI TAHUN ITU.
          Kalau posternya hanya menulis "31 Agustus" tanpa tahun, pakai {$tahunIni}.
          Kalau tidak ada tanggal sama sekali, null.
        - deadline TIDAK BOLEH berada di masa lalu kecuali posternya memang menulis
          tahun yang sudah lewat. Kalau hasil bacaanmu jatuh sebelum hari ini padahal
          poster tidak menyebut tahun, berarti tahunnya keliru -- pakai {$tahunIni}.
        - deadline bila ada beberapa gelombang pendaftaran: ambil tanggal PENUTUPAN
          GELOMBANG TERAKHIR, karena itulah kesempatan terakhir seseorang bisa mendaftar.
          Jangan mengosongkannya hanya karena tanggalnya lebih dari satu.
        - link: tulis alamat lengkap beserta https://. Poster sering menulis
          "contoh.or.id" saja -- ubah menjadi "https://contoh.or.id". Kalau yang
          tertera hanya nama akun media sosial, bukan alamat situs, isi null.
        - syarat.jurusan: daftar kosong berarti semua jurusan boleh ikut.
        - syarat.ukuran_tim: 1 berarti perorangan.
        - nominal_biaya: angka rupiah tanpa titik. Kosongkan kalau gratis.
        - deskripsi: ringkas 1-2 kalimat, bahasa Indonesia.
        TEKS;

        return $petunjuk;
    }

    public function bacaPoster(string $isiGambar, string $tipeGambar, ?int $userId = null): array
    {
        $petunjuk = $this->petunjukPoster();

        return $this->panggil(
            jenis: 'ekstraksi_poster',
            model: config('services.gemini.model'),
            bagian: [
                ['inline_data' => [
                    'mime_type' => $tipeGambar,
                    'data' => base64_encode($isiGambar),
                ]],
                ['text' => $petunjuk],
            ],
            skema: self::SKEMA_PELUANG,
            userId: $userId,
        );
    }

    /**
     * Bentuk hasil penilaian kecocokan.
     */
    public const SKEMA_KECOCOKAN = [
        'type' => 'OBJECT',
        'properties' => [
            'skor' => ['type' => 'INTEGER'],
            'terpenuhi' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
            'belum_terpenuhi' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
            'saran' => ['type' => 'STRING'],
        ],
        'required' => ['skor', 'terpenuhi', 'belum_terpenuhi', 'saran'],
    ];

    /**
     * Menilai seberapa cocok satu mahasiswa dengan satu peluang.
     *
     * Memakai model ringan: tugasnya membandingkan teks dengan teks, jauh
     * lebih sederhana daripada membaca gambar. Dua kali lebih cepat, dan
     * hemat kuota harian.
     */
    /**
     * Instruksi penilaian kecocokan, beserta contoh isinya.
     *
     * Sama seperti petunjukPoster(): dibuat publik agar halaman
     * Transparansi AI menampilkan instruksi yang benar-benar dipakai.
     */
    public function petunjukKecocokan(array $profil, array $syarat): string
    {
        $petunjuk = <<<TEKS
        Nilai seberapa cocok seorang mahasiswa dengan syarat sebuah peluang.

        PROFIL MAHASISWA:
        {$this->keJson($profil)}

        SYARAT PELUANG:
        {$this->keJson($syarat)}

        Aturan penilaian:
        - skor 0-100. Semakin banyak syarat terpenuhi, semakin tinggi.
        - Syarat yang kosong atau berupa daftar kosong berarti BEBAS, bukan penghalang.
          Jangan menurunkan skor karenanya.
        - jurusan kosong berarti semua jurusan boleh ikut.

        Arti nilai pada preferensi mahasiswa -- baca ini dengan teliti:
        - preferensi.format "keduanya" berarti mahasiswa BERSEDIA online maupun offline.
          Format acara apa pun cocok. JANGAN menyebutnya bertentangan.
        - preferensi.format "online" atau "offline" berarti dia hanya bersedia format itu.
          Baru di situ format yang berbeda menjadi penghalang.
        - preferensi.biaya "keduanya" berarti dia bersedia yang gratis maupun berbayar.
        - preferensi.biaya "gratis" berarti dia hanya mau yang gratis.

        syarat.ukuran_tim bernilai 1 berarti kegiatan perorangan. Itu BUKAN syarat
        yang perlu dipenuhi siapa pun -- jangan pernah memasukkannya ke belum_terpenuhi.
        Baru bila nilainya lebih dari 1, mencari rekan tim menjadi hal yang perlu disiapkan.

        Satu hal tidak boleh muncul di dua daftar sekaligus. Kalau sebuah syarat
        sudah kamu masukkan ke terpenuhi, jangan menuliskannya lagi di belum_terpenuhi.

        belum_terpenuhi hanya untuk hal yang benar-benar menghalangi. Kalau tidak ada
        penghalang sama sekali, kosongkan daftarnya -- jangan mencari-cari kekurangan
        supaya daftarnya terisi.
        - Syarat yang masih bisa diusahakan (mencari rekan tim, menyiapkan portofolio)
          menurunkan skor lebih sedikit daripada syarat yang mustahil diubah
          (misalnya batas semester yang sudah terlewat).
        - terpenuhi: alasan singkat, maksimal 6 butir. Contoh: "Jurusan sesuai".
        - belum_terpenuhi: hal yang belum dipenuhi, maksimal 4 butir. Kosongkan bila semua terpenuhi.
        - saran: satu atau dua kalimat, Bahasa Indonesia, langsung bisa dikerjakan.
          Kalau semua terpenuhi, dorong dia mendaftar.

        Jawab jujur. Jangan mengatrol skor supaya terdengar menyenangkan --
        skor yang terlalu murah hati membuat mahasiswa mendaftar lalu tersingkir
        di tahap administrasi, dan itu lebih merugikan daripada skor rendah yang jujur.
        TEKS;

        return $petunjuk;
    }

    public function hitungKecocokan(array $profil, array $syarat, ?int $userId = null): array
    {
        $petunjuk = $this->petunjukKecocokan($profil, $syarat);

        $hasil = $this->panggil(
            jenis: 'skor_kecocokan',
            model: config('services.gemini.ringan'),
            bagian: [['text' => $petunjuk]],
            skema: self::SKEMA_KECOCOKAN,
            userId: $userId,
        );

        // Jaring pengaman: skor di luar 0-100 dipaksa masuk rentang.
        $hasil['skor'] = max(0, min(100, (int) ($hasil['skor'] ?? 0)));

        return $hasil;
    }

    private function keJson(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Satu-satunya pintu keluar menuju layanan AI.
     *
     * Semua pemanggilan lewat sini, supaya pencatatan token, durasi, dan
     * kegagalan cukup ditulis di satu tempat -- dan supaya mengganti penyedia
     * AI nanti cukup mengubah berkas ini saja.
     */
    private function panggil(string $jenis, string $model, array $bagian, array $skema, ?int $userId): array
    {
        $mulai = microtime(true);

        try {
            $respons = Http::withHeaders(['x-goog-api-key' => config('services.gemini.key')])
                ->timeout(60)
                ->retry(3, 1000, fn ($e) => $e instanceof ConnectionException, throw: false)
                ->post(config('services.gemini.endpoint')."/{$model}:generateContent", [
                    'contents' => [['parts' => $bagian]],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'responseSchema' => $skema,
                    ],
                ]);
        } catch (ConnectionException) {
            $this->catat($jenis, $model, $userId, 0, 0, $this->msSejak($mulai), 'gagal', 'Tidak dapat menghubungi layanan AI.');

            throw new RuntimeException('Layanan AI sedang tidak dapat dihubungi. Coba lagi sebentar lagi.');
        }

        $durasi = $this->msSejak($mulai);

        if ($respons->failed()) {
            $pesanAsli = (string) data_get($respons->json(), 'error.message', 'Galat tidak diketahui.');
            $this->catat($jenis, $model, $userId, 0, 0, $durasi, 'gagal', $pesanAsli);

            throw new RuntimeException(match ($respons->status()) {
                429 => 'Kuota AI hari ini sudah habis. Coba lagi besok.',
                400 => 'Gambar tidak dapat diproses. Coba unggah poster yang lebih jelas.',
                default => 'Layanan AI gagal memproses permintaan. Coba lagi sebentar lagi.',
            });
        }

        $json = $respons->json();
        $data = json_decode((string) data_get($json, 'candidates.0.content.parts.0.text'), true);

        $tokenMasuk = (int) data_get($json, 'usageMetadata.promptTokenCount', 0);
        $tokenKeluar = (int) data_get($json, 'usageMetadata.candidatesTokenCount', 0);

        if (! is_array($data)) {
            $this->catat($jenis, $model, $userId, $tokenMasuk, $tokenKeluar, $durasi, 'gagal', 'Balasan AI bukan JSON yang sah.');

            throw new RuntimeException('AI mengembalikan data yang tidak dapat dibaca. Coba ulangi.');
        }

        $this->catat($jenis, $model, $userId, $tokenMasuk, $tokenKeluar, $durasi, 'berhasil', null);

        return $data;
    }

    /**
     * Mencatat bahwa satu permintaan dilayani dari hasil tersimpan,
     * tanpa memanggil AI sama sekali.
     *
     * Inilah sumber angka penghematan yang nanti ditampilkan ke juri.
     */
    public function catatDariCache(string $jenis, string $model, ?int $userId = null): void
    {
        $this->catat($jenis, $model, $userId, 0, 0, 0, 'dari_cache', null);
    }

    private function catat(
        string $jenis,
        string $model,
        ?int $userId,
        int $tokenMasuk,
        int $tokenKeluar,
        int $durasiMs,
        string $status,
        ?string $pesanError,
    ): void {
        AiLog::create([
            'jenis' => $jenis,
            'model' => $model,
            'user_id' => $userId,
            'token_masuk' => $tokenMasuk,
            'token_keluar' => $tokenKeluar,
            'durasi_ms' => $durasiMs,
            'status' => $status,
            'pesan_error' => $pesanError,
        ]);
    }

    private function msSejak(float $mulai): int
    {
        return (int) round((microtime(true) - $mulai) * 1000);
    }
}
