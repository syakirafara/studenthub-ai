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
    private const SKEMA_PELUANG = [
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
    public function bacaPoster(string $isiGambar, string $tipeGambar, ?int $userId = null): array
    {
        $petunjuk = <<<'TEKS'
        Kamu membaca poster informasi peluang mahasiswa Indonesia (lomba, beasiswa, atau magang).

        Ambil datanya sesuai skema yang diberikan. Aturan:

        - Tulis apa adanya sesuai poster. JANGAN mengarang data yang tidak tertulis.
        - Kalau sebuah keterangan tidak ada di poster, isi null (atau daftar kosong).
        - deadline: format YYYY-MM-DD. Kalau posternya hanya menulis "31 Agustus"
          tanpa tahun, gunakan tahun berjalan. Kalau tidak ada tanggal sama sekali, null.
        - syarat.jurusan: daftar kosong berarti semua jurusan boleh ikut.
        - syarat.ukuran_tim: 1 berarti perorangan.
        - nominal_biaya: angka rupiah tanpa titik. Kosongkan kalau gratis.
        - deskripsi: ringkas 1-2 kalimat, bahasa Indonesia.
        TEKS;

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
