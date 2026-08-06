<?php

namespace Tests\Feature;

use App\Models\Opportunity;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/*
 | Uji "asap": memastikan tiap halaman benar-benar tergambar tanpa galat.
 |
 | Uji semacam ini tidak memeriksa isi, hanya memastikan tidak ada yang
 | meledak. Kelihatannya sepele, tetapi inilah yang menangkap kesalahan
 | paling memalukan -- salah nama peubah di Blade, komponen yang dipanggil
 | dengan prop yang tidak ada, atau relasi yang lupa dimuat. Semua itu tidak
 | ketahuan sampai halamannya benar-benar dibuka.
 */
class HalamanTergambarTest extends TestCase
{
    use RefreshDatabase;

    private function mahasiswa(bool $berprofil = true): User
    {
        $pengguna = User::factory()->create(['role' => 'mahasiswa']);

        if ($berprofil) {
            Profile::create([
                'user_id' => $pengguna->id,
                'universitas' => 'Universitas Dian Nuswantoro',
                'jurusan' => 'Teknik Informatika',
                'semester' => 5,
                'minat' => ['teknologi'],
                'skill' => ['pemrograman'],
                'preferensi' => ['format' => 'keduanya', 'biaya' => 'keduanya'],
            ]);
        }

        return $pengguna;
    }

    private function peluang(array $ubah = []): Opportunity
    {
        return Opportunity::create([
            'judul' => 'Lomba Uji Coba',
            'penyelenggara' => 'Panitia Uji',
            'kategori' => 'lomba',
            'deskripsi' => 'Keterangan singkat untuk pengujian.',
            'deadline' => now()->addDays(30),
            'biaya' => 'gratis',
            'tingkat' => 'nasional',
            'status' => 'disetujui',
            'syarat' => ['jurusan' => [], 'ukuran_tim' => 1],
            ...$ubah,
        ]);
    }

    /* ---------------------------------------------------------------------
     | Halaman tamu
     --------------------------------------------------------------------- */

    public static function halamanTamu(): array
    {
        return [
            'depan' => ['depan'],
            'katalog' => ['peluang.index'],
            'masuk' => ['masuk'],
            'daftar' => ['daftar'],
        ];
    }

    #[DataProvider('halamanTamu')]
    public function test_halaman_tamu_tergambar(string $rute): void
    {
        $this->peluang();

        $this->get(route($rute))->assertOk();
    }

    /* ---------------------------------------------------------------------
     | Halaman mahasiswa
     --------------------------------------------------------------------- */

    public static function halamanMahasiswa(): array
    {
        return [
            'beranda' => ['beranda'],
            'profil' => ['profil.edit'],
            'tersimpan' => ['tersimpan.index'],
            'unggah' => ['unggah.buat'],
        ];
    }

    #[DataProvider('halamanMahasiswa')]
    public function test_halaman_mahasiswa_tergambar(string $rute): void
    {
        $this->peluang();

        $this->actingAs($this->mahasiswa())
            ->get(route($rute))
            ->assertOk();
    }

    public function test_beranda_tergambar_walau_profil_belum_ada(): void
    {
        // Keadaan yang mudah terlewat: akun ada, profil belum. Halaman
        // beranda memakai $profil->jurusan -- kalau tidak dijaga, ini
        // langsung meledak.
        $this->actingAs($this->mahasiswa(berprofil: false))
            ->get(route('beranda'))
            ->assertOk();
    }

    public function test_katalog_tergambar_walau_kosong(): void
    {
        $this->get(route('peluang.index'))->assertOk();
    }

    public function test_katalog_tergambar_dengan_semua_penyaring(): void
    {
        $this->peluang();

        $this->get(route('peluang.index', [
            'cari' => 'lomba',
            'kategori' => 'lomba',
            'tingkat' => 'nasional',
            'urut' => 'terbaru',
            'biaya' => 'gratis',
            'tampilkan_lewat' => '1',
        ]))->assertOk();
    }

    public function test_rincian_tergambar_untuk_tamu_dan_mahasiswa(): void
    {
        $peluang = $this->peluang();

        $this->get(route('peluang.show', $peluang))->assertOk();

        $this->actingAs($this->mahasiswa())
            ->get(route('peluang.show', $peluang))
            ->assertOk();
    }

    public function test_rincian_tergambar_walau_deadline_kosong(): void
    {
        // Batang sisa waktu dihitung dari deadline. Kalau kosong, batangnya
        // harus dilewati -- bukan membagi dengan nilai kosong.
        $peluang = $this->peluang(['deadline' => null]);

        $this->get(route('peluang.show', $peluang))->assertOk();
    }

    public function test_rincian_tergambar_walau_deadline_sudah_lewat(): void
    {
        $peluang = $this->peluang(['deadline' => now()->subDays(10)]);

        $this->get(route('peluang.show', $peluang))->assertOk();
    }

    public function test_rincian_tergambar_untuk_tiap_kategori(): void
    {
        foreach (['lomba', 'beasiswa', 'magang'] as $kategori) {
            $peluang = $this->peluang(['kategori' => $kategori]);

            $this->get(route('peluang.show', $peluang))->assertOk();
        }
    }

    /* ---------------------------------------------------------------------
     | Halaman admin
     --------------------------------------------------------------------- */

    public function test_dasbor_admin_tergambar_walau_belum_ada_catatan_ai(): void
    {
        // Angka akurasi bernilai kosong sebelum ada poster diverifikasi.
        // Tampilan harus menanganinya, bukan menampilkan pembagian nol.
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('admin.dasbor'))
            ->assertOk();
    }

    public function test_halaman_periksa_admin_tergambar(): void
    {
        $peluang = $this->peluang(['status' => 'menunggu']);

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('admin.periksa', $peluang))
            ->assertOk();
    }

    public function test_admin_bisa_melihat_pratinjau_yang_belum_disetujui(): void
    {
        // Admin perlu bisa membuka rincian peluang yang belum tayang, tetapi
        // mahasiswa biasa tidak. Ini penjagaan yang mudah keliru.
        $peluang = $this->peluang(['status' => 'menunggu']);

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('peluang.show', $peluang))
            ->assertOk();

        $this->actingAs($this->mahasiswa())
            ->get(route('peluang.show', $peluang))
            ->assertNotFound();
    }
}
