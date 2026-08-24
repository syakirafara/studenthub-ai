<?php

namespace Tests\Unit;

use App\Models\ExtractionReview;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
 | Menguji pengukuran akurasi pembacaan poster.
 |
 | Aturan di sini lahir dari bug nyata: deadline tercatat "dikoreksi" pada 12
 | dari 20 poster yang sebenarnya dibaca BENAR, karena "2026-08-19" dari AI
 | dibandingkan mentah-mentah dengan "2026-08-18T17:00:00Z" dari basis data --
 | dua tulisan berbeda untuk tanggal yang sama, sebab yang kedua disimpan UTC.
 |
 | Akibatnya akurasi terlaporkan 92% padahal sebenarnya 97,5%. Alat ukur yang
 | salah lebih berbahaya daripada tidak mengukur, karena hasilnya tetap
 | terlihat meyakinkan.
 */
class PembandingAkurasiTest extends TestCase
{
    use RefreshDatabase;

    private function verifikasi(?string $bacaanAi, ?string $isianAdmin): ExtractionReview
    {
        $peluang = Opportunity::create([
            'judul' => 'Lomba Uji',
            'kategori' => 'lomba',
            'biaya' => 'gratis',
            'tingkat' => 'nasional',
            'status' => 'menunggu',
            'syarat' => [],
        ]);

        ExtractionReview::create([
            'opportunity_id' => $peluang->id,
            'hasil_ai' => ['judul' => 'Lomba Uji', 'deadline' => $bacaanAi],
        ]);

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->put(route('admin.setujui', $peluang), [
                'judul' => 'Lomba Uji',
                'kategori' => 'lomba',
                'biaya' => 'gratis',
                'tingkat' => 'nasional',
                'deadline' => $isianAdmin,
            ]);

        return $peluang->fresh()->review;
    }

    public function test_tanggal_sama_tidak_dihitung_sebagai_koreksi(): void
    {
        // Admin tidak mengubah apa pun. Yang berbeda hanya bentuk simpanannya.
        $review = $this->verifikasi('2026-08-19', '2026-08-19');

        $this->assertNotContains('deadline', $review->field_dikoreksi);
    }

    public function test_tanggal_berbeda_tetap_dihitung_sebagai_koreksi(): void
    {
        $review = $this->verifikasi('2024-08-19', '2026-08-19');

        $this->assertContains('deadline', $review->field_dikoreksi);
    }

    public function test_admin_mengosongkan_tanggal_dihitung_sebagai_koreksi(): void
    {
        $review = $this->verifikasi('2024-09-20', null);

        $this->assertContains('deadline', $review->field_dikoreksi);
    }

    public function test_dua_duanya_kosong_bukan_koreksi(): void
    {
        $review = $this->verifikasi(null, null);

        $this->assertNotContains('deadline', $review->field_dikoreksi);
    }
}
