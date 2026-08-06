<?php

namespace App\Services;

use GdImage;
use RuntimeException;

class PengolahGambar
{
    /**
     * Memperkecil gambar sebelum dikirim ke AI.
     *
     * Alasannya biaya dan kecepatan: AI menghitung gambar berdasarkan
     * ukurannya, sehingga poster 4000 piksel memakan berkali-kali lipat token
     * dibanding 1280 piksel -- padahal tulisan di poster tetap terbaca jelas
     * pada 1280. Uji di Sesi 07 menunjukkan pola yang sama pada logo
     * penyelenggara: 11,9 MB turun menjadi 66 KB tanpa perbedaan yang terlihat.
     *
     * @param  string  $isiAsli  isi berkas gambar apa adanya
     * @return string isi gambar JPEG yang sudah diperkecil
     */
    public function kecilkan(string $isiAsli, int $maksSisi = 1280, int $mutu = 82): string
    {
        $gambar = @imagecreatefromstring($isiAsli);

        if (! $gambar instanceof GdImage) {
            throw new RuntimeException('Berkas ini bukan gambar yang dapat dibaca.');
        }

        $lebar = imagesx($gambar);
        $tinggi = imagesy($gambar);
        $sisiTerpanjang = max($lebar, $tinggi);

        // Sudah cukup kecil - cukup ubah ke JPEG tanpa mengubah ukuran.
        $skala = $sisiTerpanjang > $maksSisi ? $maksSisi / $sisiTerpanjang : 1.0;

        $lebarBaru = max(1, (int) round($lebar * $skala));
        $tinggiBaru = max(1, (int) round($tinggi * $skala));

        $kanvas = imagecreatetruecolor($lebarBaru, $tinggiBaru);

        // Poster PNG kerap berlatar transparan. JPEG tidak mengenal transparan,
        // dan tanpa langkah ini bagian transparan berubah menjadi hitam pekat
        // sehingga tulisan di atasnya ikut hilang terbaca.
        $putih = imagecolorallocate($kanvas, 255, 255, 255);
        imagefilledrectangle($kanvas, 0, 0, $lebarBaru, $tinggiBaru, $putih);

        imagecopyresampled($kanvas, $gambar, 0, 0, 0, 0, $lebarBaru, $tinggiBaru, $lebar, $tinggi);

        ob_start();
        imagejpeg($kanvas, null, $mutu);
        $hasil = (string) ob_get_clean();

        imagedestroy($gambar);
        imagedestroy($kanvas);

        return $hasil;
    }

    /**
     * Keterangan singkat hasil pengecilan, untuk ditampilkan ke pengguna
     * dan diceritakan ke juri.
     *
     * @return array{sebelum_kb: int, sesudah_kb: int, hemat_persen: int}
     */
    public function ringkasan(string $isiAsli, string $isiBaru): array
    {
        $sebelum = strlen($isiAsli);
        $sesudah = strlen($isiBaru);

        return [
            'sebelum_kb' => (int) round($sebelum / 1024),
            'sesudah_kb' => (int) round($sesudah / 1024),
            'hemat_persen' => $sebelum > 0 ? (int) round((1 - $sesudah / $sebelum) * 100) : 0,
        ];
    }
}
