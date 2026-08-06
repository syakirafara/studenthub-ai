<?php

namespace Tests\Feature;

use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
 | Uji alur utama dari ujung ke ujung.
 |
 | Tujuannya bukan menguji tiap baris, melainkan memastikan JALAN YANG
 | DIPAKAI PENGGUNA tidak putus: tamu bisa melihat katalog, mahasiswa bisa
 | menyimpan dan mengunggah, admin bisa memverifikasi, dan yang belum
 | disetujui tidak bocor ke publik.
 |
 | Angka hasil uji ini yang nanti disebut di presentasi -- bukan klaim
 | "sistem sudah diuji", tetapi jumlah yang bisa ditunjukkan.
 */
class AlurUtamaTest extends TestCase
{
    use RefreshDatabase;

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
     | Halaman publik
     --------------------------------------------------------------------- */

    public function test_halaman_depan_terbuka_untuk_tamu(): void
    {
        $this->peluang();

        $this->get(route('depan'))->assertOk();
    }

    public function test_katalog_menampilkan_peluang_yang_disetujui(): void
    {
        $this->peluang(['judul' => 'Hackathon Terlihat']);

        $this->get(route('peluang.index'))
            ->assertOk()
            ->assertSee('Hackathon Terlihat');
    }

    public function test_katalog_menyembunyikan_yang_belum_disetujui(): void
    {
        $this->peluang(['judul' => 'Masih Menunggu', 'status' => 'menunggu']);
        $this->peluang(['judul' => 'Sudah Ditolak', 'status' => 'ditolak']);

        $this->get(route('peluang.index'))
            ->assertOk()
            ->assertDontSee('Masih Menunggu')
            ->assertDontSee('Sudah Ditolak');
    }

    public function test_peluang_tanpa_deadline_tetap_muncul(): void
    {
        // Bug yang pernah terjadi: penyaring deadline ikut membuang baris
        // yang deadline-nya kosong, sehingga poster tanpa tanggal hilang
        // diam-diam dari katalog.
        $this->peluang(['judul' => 'Tanpa Tanggal', 'deadline' => null]);

        $this->get(route('peluang.index'))
            ->assertOk()
            ->assertSee('Tanpa Tanggal');
    }

    public function test_halaman_rincian_terbuka_untuk_tamu(): void
    {
        $peluang = $this->peluang();

        $this->get(route('peluang.show', $peluang))
            ->assertOk()
            ->assertSee('Lomba Uji Coba');
    }

    public function test_tamu_tidak_bisa_membuka_rincian_yang_belum_disetujui(): void
    {
        $peluang = $this->peluang(['status' => 'menunggu']);

        $this->get(route('peluang.show', $peluang))->assertNotFound();
    }

    /* ---------------------------------------------------------------------
     | Penjagaan hak akses
     --------------------------------------------------------------------- */

    public function test_tamu_dialihkan_dari_halaman_yang_butuh_masuk(): void
    {
        foreach (['beranda', 'profil.edit', 'tersimpan.index', 'unggah.buat'] as $rute) {
            $this->get(route($rute))->assertRedirect(route('masuk'));
        }
    }

    public function test_mahasiswa_biasa_ditolak_dari_area_admin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'mahasiswa']))
            ->get(route('admin.dasbor'))
            ->assertForbidden();
    }

    public function test_admin_bisa_membuka_dasbor(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('admin.dasbor'))
            ->assertOk();
    }

    /* ---------------------------------------------------------------------
     | Autentikasi
     --------------------------------------------------------------------- */

    private function isianDaftar(array $ubah = []): array
    {
        return [
            'name' => 'Mahasiswa Uji',
            'email' => 'uji@contoh.test',
            'password' => 'katasandi123',
            'password_confirmation' => 'katasandi123',
            'universitas' => 'Universitas Dian Nuswantoro',
            'jurusan' => 'Teknik Informatika',
            'semester' => 5,
            ...$ubah,
        ];
    }

    public function test_mahasiswa_bisa_mendaftar(): void
    {
        $this->post(route('daftar.simpan'), $this->isianDaftar())
            ->assertRedirect(route('beranda'));

        $this->assertDatabaseHas('users', [
            'email' => 'uji@contoh.test',
            'role' => 'mahasiswa',
        ]);
    }

    public function test_pendaftaran_sekaligus_membuat_profil(): void
    {
        // Akun tanpa profil akan meruntuhkan perhitungan kecocokan, karena
        // AI membandingkan syarat peluang dengan isi profil.
        $this->post(route('daftar.simpan'), $this->isianDaftar());

        $this->assertDatabaseHas('profiles', [
            'universitas' => 'Universitas Dian Nuswantoro',
            'jurusan' => 'Teknik Informatika',
            'semester' => 5,
        ]);
    }

    public function test_pendaftaran_selalu_berperan_mahasiswa(): void
    {
        // Penting untuk keamanan: kolom role tidak boleh bisa diisi dari
        // formulir, kalau tidak siapa pun bisa mengangkat dirinya jadi admin.
        $this->post(route('daftar.simpan'), $this->isianDaftar([
            'email' => 'penyusup@contoh.test',
            'role' => 'admin',
        ]));

        $this->assertDatabaseHas('users', [
            'email' => 'penyusup@contoh.test',
            'role' => 'mahasiswa',
        ]);
    }

    public function test_email_yang_sudah_dipakai_ditolak(): void
    {
        User::factory()->create(['email' => 'uji@contoh.test']);

        $this->post(route('daftar.simpan'), $this->isianDaftar())
            ->assertSessionHasErrors('email');
    }

    public function test_masuk_dengan_sandi_salah_ditolak(): void
    {
        User::factory()->create(['email' => 'ada@contoh.test']);

        $this->post(route('masuk.proses'), [
            'email' => 'ada@contoh.test',
            'password' => 'sandi-yang-salah',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    /* ---------------------------------------------------------------------
     | Menyimpan peluang
     --------------------------------------------------------------------- */

    public function test_mahasiswa_bisa_menyimpan_dan_menghapus_simpanan(): void
    {
        $pengguna = User::factory()->create();
        $peluang = $this->peluang();

        $this->actingAs($pengguna)
            ->post(route('tersimpan.store', $peluang))
            ->assertRedirect();

        $this->assertDatabaseHas('saved_items', [
            'user_id' => $pengguna->id,
            'opportunity_id' => $peluang->id,
        ]);

        $this->actingAs($pengguna)
            ->delete(route('tersimpan.destroy', $peluang))
            ->assertRedirect();

        $this->assertDatabaseMissing('saved_items', [
            'user_id' => $pengguna->id,
            'opportunity_id' => $peluang->id,
        ]);
    }

    public function test_menyimpan_dua_kali_tidak_menggandakan_baris(): void
    {
        $pengguna = User::factory()->create();
        $peluang = $this->peluang();

        $this->actingAs($pengguna)->post(route('tersimpan.store', $peluang));
        $this->actingAs($pengguna)->post(route('tersimpan.store', $peluang));

        $this->assertSame(1, $pengguna->savedItems()->count());
    }

    /* ---------------------------------------------------------------------
     | Verifikasi admin
     --------------------------------------------------------------------- */

    public function test_admin_menyetujui_membuat_peluang_tampil_di_katalog(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $peluang = $this->peluang(['judul' => 'Menanti Setuju', 'status' => 'menunggu']);

        $this->actingAs($admin)->put(route('admin.setujui', $peluang), [
            'judul' => 'Sudah Diperbaiki Admin',
            'penyelenggara' => 'Panitia Uji',
            'kategori' => 'lomba',
            'deskripsi' => 'Keterangan.',
            'deadline' => now()->addDays(20)->toDateString(),
            'biaya' => 'gratis',
            'tingkat' => 'nasional',
            'link' => 'https://contoh.test/daftar',
        ])->assertRedirect(route('admin.dasbor'));

        $peluang->refresh();

        $this->assertSame('disetujui', $peluang->status);
        $this->assertSame('Sudah Diperbaiki Admin', $peluang->judul);
        $this->assertSame($admin->id, $peluang->verified_by);
        $this->assertNotNull($peluang->verified_at);

        $this->get(route('peluang.index'))->assertSee('Sudah Diperbaiki Admin');
    }

    public function test_penolakan_wajib_disertai_alasan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $peluang = $this->peluang(['status' => 'menunggu']);

        $this->actingAs($admin)
            ->put(route('admin.tolak', $peluang), ['catatan_admin' => 'pendek'])
            ->assertSessionHasErrors('catatan_admin');

        $this->assertSame('menunggu', $peluang->fresh()->status);
    }

    public function test_admin_bisa_menolak_dengan_alasan_yang_cukup(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $peluang = $this->peluang(['status' => 'menunggu']);

        $this->actingAs($admin)->put(route('admin.tolak', $peluang), [
            'catatan_admin' => 'Poster tidak terbaca, tulisannya terlalu kecil.',
        ])->assertRedirect();

        $this->assertSame('ditolak', $peluang->fresh()->status);
    }
}
