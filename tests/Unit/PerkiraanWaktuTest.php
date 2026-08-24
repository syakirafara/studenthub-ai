<?php

namespace Tests\Unit;

use App\Models\AiLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
 | Menguji perkiraan lama panggilan AI.
 |
 | Aturan yang dijaga di sini lahir dari kesalahan nyata: halaman unggah
 | pernah menjanjikan "10-20 detik" sementara satu-satunya panggilan yang
 | tercatat memakan 30 detik. Janji yang meleset di layar sendiri merusak
 | kepercayaan -- apalagi kalau melesetnya terjadi saat ditonton juri.
 */
class PerkiraanWaktuTest extends TestCase
{
    use RefreshDatabase;

    private function catat(string $jenis, int $ms, string $status = 'berhasil'): void
    {
        AiLog::create([
            'jenis' => $jenis,
            'model' => 'uji',
            'user_id' => null,
            'token_masuk' => 0,
            'token_keluar' => 0,
            'durasi_ms' => $ms,
            'status' => $status,
        ]);
    }

    public function test_tanpa_catatan_memakai_kalimat_cadangan(): void
    {
        $hasil = AiLog::perkiraan('ekstraksi_poster', '10 sampai 40 detik');

        $this->assertFalse($hasil['terukur']);
        $this->assertSame(0, $hasil['jumlah']);
        $this->assertSame('10 sampai 40 detik', $hasil['teks']);
    }

    public function test_dua_catatan_belum_dianggap_terukur(): void
    {
        // Batasnya tiga. Dua panggilan belum cukup untuk menyebut rata-rata,
        // karena satu yang kebetulan lambat menarik seluruh angka.
        $this->catat('ekstraksi_poster', 5_000);
        $this->catat('ekstraksi_poster', 45_000);

        $hasil = AiLog::perkiraan('ekstraksi_poster', 'cadangan');

        $this->assertFalse($hasil['terukur']);
        $this->assertSame('cadangan', $hasil['teks']);
    }

    public function test_tiga_catatan_menghasilkan_angka_terukur(): void
    {
        $this->catat('ekstraksi_poster', 10_000);
        $this->catat('ekstraksi_poster', 20_000);
        $this->catat('ekstraksi_poster', 30_000);

        $hasil = AiLog::perkiraan('ekstraksi_poster', 'cadangan');

        $this->assertTrue($hasil['terukur']);
        $this->assertSame(3, $hasil['jumlah']);
        $this->assertStringContainsString('20 detik', $hasil['teks']);
        $this->assertStringContainsString('30 detik', $hasil['teks']);
    }

    public function test_panggilan_gagal_tidak_ikut_dihitung(): void
    {
        // Panggilan gagal sering berhenti di tengah, sehingga durasinya
        // pendek dan justru membuat perkiraan terlihat lebih cepat dari
        // kenyataan.
        $this->catat('ekstraksi_poster', 20_000);
        $this->catat('ekstraksi_poster', 20_000);
        $this->catat('ekstraksi_poster', 100, 'gagal');

        $hasil = AiLog::perkiraan('ekstraksi_poster', 'cadangan');

        $this->assertFalse($hasil['terukur'], 'yang gagal seharusnya tidak menambah jumlah sampel');
        $this->assertSame(2, $hasil['jumlah']);
    }

    public function test_jenis_lain_tidak_tercampur(): void
    {
        $this->catat('skor_kecocokan', 2_000);
        $this->catat('skor_kecocokan', 2_000);
        $this->catat('skor_kecocokan', 2_000);

        $poster = AiLog::perkiraan('ekstraksi_poster', 'cadangan poster');
        $skor = AiLog::perkiraan('skor_kecocokan', 'cadangan skor');

        $this->assertFalse($poster['terukur']);
        $this->assertTrue($skor['terukur']);
        $this->assertSame('sekitar 2 detik', $skor['teks']);
    }
}
