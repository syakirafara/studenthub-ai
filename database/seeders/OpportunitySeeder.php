<?php

namespace Database\Seeders;

use App\Models\Opportunity;
use App\Models\OpportunityMatch;
use App\Models\SavedItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class OpportunitySeeder extends Seeder
{
    public function run(): void
    {
        $kontributor = User::where('role', 'mahasiswa')->pluck('id')->all();
        $adminId = User::where('role', 'admin')->value('id');

        foreach ($this->daftarPeluang() as $i => $p) {
            // Sebar deadline: sebagian sudah lewat, sebagian mepet, sebagian jauh
            $hari = match (true) {
                $i % 9 === 0 => -fake()->numberBetween(3, 30),   // sudah lewat
                $i % 5 === 0 => fake()->numberBetween(2, 7),     // mepet
                default => fake()->numberBetween(10, 90),   // masih lama
            };

            // Sebar status: mayoritas disetujui, sisanya menunggu / ditolak
            $status = match (true) {
                $i % 11 === 0 => 'ditolak',
                $i % 7 === 0 => 'menunggu',
                default => 'disetujui',
            };

            Opportunity::create([
                'judul' => $p['judul'],
                'penyelenggara' => $p['penyelenggara'],
                'kategori' => $p['kategori'],
                'deskripsi' => $p['deskripsi'],
                'deadline' => now()->addDays($hari)->toDateString(),
                'biaya' => $p['biaya'],
                'nominal_biaya' => $p['biaya'] === 'berbayar' ? fake()->numberBetween(25, 150) * 1000 : null,
                'tingkat' => $p['tingkat'],
                'link' => 'https://contoh.test/'.str()->slug($p['judul']),
                'poster_path' => null,
                'syarat' => $p['syarat'],
                'status' => $status,
                'catatan_admin' => $status === 'ditolak' ? 'Informasi tidak lengkap, deadline tidak tercantum di poster.' : null,
                'submitted_by' => fake()->randomElement($kontributor),
                'verified_by' => $status === 'menunggu' ? null : $adminId,
                'verified_at' => $status === 'menunggu' ? null : now()->subDays(fake()->numberBetween(1, 20)),
            ]);
        }

        $this->buatSimpananDanSkor();
    }

    private function buatSimpananDanSkor(): void
    {
        $mahasiswa = User::where('role', 'mahasiswa')->get();
        $peluang = Opportunity::where('status', 'disetujui')->pluck('id')->all();

        foreach ($mahasiswa as $user) {
            // Setiap mahasiswa menyimpan 2-5 peluang
            foreach (fake()->randomElements($peluang, fake()->numberBetween(2, 5)) as $id) {
                SavedItem::create([
                    'user_id' => $user->id,
                    'opportunity_id' => $id,
                ]);
            }

            // Skor kecocokan untuk 6 peluang
            foreach (fake()->randomElements($peluang, 6) as $id) {
                OpportunityMatch::create([
                    'user_id' => $user->id,
                    'opportunity_id' => $id,
                    'skor' => fake()->numberBetween(35, 96),
                    'terpenuhi' => fake()->randomElements(
                        ['jurusan sesuai', 'semester sesuai', 'format sesuai', 'biaya sesuai'], 2
                    ),
                    'belum_terpenuhi' => fake()->randomElements(
                        ['butuh tim 3 orang', 'perlu portofolio', 'IPK belum memenuhi'], 1
                    ),
                    'saran' => 'Ajak dua rekan satu jurusan dan gunakan tugas kuliah sebagai portofolio.',
                    'dihitung_pada' => now()->subHours(fake()->numberBetween(1, 72)),
                ]);
            }
        }
    }

    private function daftarPeluang(): array
    {
        return [
            [
                'judul' => 'Gemastik XIX - Divisi Pemrograman',
                'penyelenggara' => 'Puspresnas Kemendikbudristek',
                'kategori' => 'lomba', 'tingkat' => 'nasional', 'biaya' => 'gratis',
                'deskripsi' => 'Kompetisi pemrograman kompetitif tingkat nasional untuk mahasiswa aktif jenjang D3 hingga S1.',
                'syarat' => ['jurusan' => ['Teknik Informatika', 'Sistem Informasi'], 'semester_min' => 1, 'semester_maks' => 8, 'ukuran_tim' => 3, 'format' => 'hybrid', 'lainnya' => ['mahasiswa aktif D3/S1']],
            ],
            [
                'judul' => 'Program Kreativitas Mahasiswa - Karsa Cipta',
                'penyelenggara' => 'Kemendikbudristek',
                'kategori' => 'lomba', 'tingkat' => 'nasional', 'biaya' => 'gratis',
                'deskripsi' => 'Pendanaan penelitian dan karya cipta mahasiswa yang menghasilkan produk fungsional.',
                'syarat' => ['jurusan' => [], 'semester_min' => 1, 'semester_maks' => 6, 'ukuran_tim' => 5, 'format' => 'offline', 'lainnya' => ['wajib ada dosen pendamping']],
            ],
            [
                'judul' => 'Lomba UI/UX Design Nasional',
                'penyelenggara' => 'Himpunan Mahasiswa Sistem Informasi',
                'kategori' => 'lomba', 'tingkat' => 'nasional', 'biaya' => 'berbayar',
                'deskripsi' => 'Merancang antarmuka aplikasi yang menyelesaikan masalah sosial di lingkungan perkotaan.',
                'syarat' => ['jurusan' => ['Sistem Informasi', 'Desain Komunikasi Visual', 'Teknik Informatika'], 'semester_min' => 1, 'semester_maks' => 8, 'ukuran_tim' => 2, 'format' => 'online', 'lainnya' => ['punya portofolio desain']],
            ],
            [
                'judul' => 'Data Science Competition',
                'penyelenggara' => 'Fakultas Ilmu Komputer',
                'kategori' => 'lomba', 'tingkat' => 'nasional', 'biaya' => 'berbayar',
                'deskripsi' => 'Analisis data terbuka pemerintah untuk menghasilkan rekomendasi kebijakan.',
                'syarat' => ['jurusan' => ['Teknik Informatika', 'Sistem Informasi'], 'semester_min' => 3, 'semester_maks' => 8, 'ukuran_tim' => 3, 'format' => 'online', 'lainnya' => ['menguasai Python atau R']],
            ],
            [
                'judul' => 'Business Plan Competition',
                'penyelenggara' => 'Fakultas Ekonomi dan Bisnis',
                'kategori' => 'lomba', 'tingkat' => 'regional', 'biaya' => 'berbayar',
                'deskripsi' => 'Menyusun rencana bisnis berkelanjutan yang memberdayakan UMKM daerah.',
                'syarat' => ['jurusan' => ['Manajemen', 'Akuntansi'], 'semester_min' => 3, 'semester_maks' => 8, 'ukuran_tim' => 3, 'format' => 'offline', 'lainnya' => []],
            ],
            [
                'judul' => 'Lomba Karya Tulis Ilmiah Nasional',
                'penyelenggara' => 'Universitas Trunojoyo Madura',
                'kategori' => 'lomba', 'tingkat' => 'nasional', 'biaya' => 'berbayar',
                'deskripsi' => 'Karya tulis bertema teknologi digital untuk komunitas berkelanjutan.',
                'syarat' => ['jurusan' => [], 'semester_min' => 1, 'semester_maks' => 8, 'ukuran_tim' => 3, 'format' => 'hybrid', 'lainnya' => ['melampirkan KTM']],
            ],
            [
                'judul' => 'Hackathon Teknologi Kesehatan',
                'penyelenggara' => 'Komunitas Developer Jawa Tengah',
                'kategori' => 'lomba', 'tingkat' => 'regional', 'biaya' => 'gratis',
                'deskripsi' => 'Membangun prototipe aplikasi kesehatan dalam 48 jam.',
                'syarat' => ['jurusan' => ['Teknik Informatika', 'Sistem Informasi', 'Teknik Elektro'], 'semester_min' => 2, 'semester_maks' => 8, 'ukuran_tim' => 4, 'format' => 'offline', 'lainnya' => ['bawa laptop sendiri']],
            ],
            [
                'judul' => 'Kompetisi Esai Mahasiswa',
                'penyelenggara' => 'Badan Eksekutif Mahasiswa',
                'kategori' => 'lomba', 'tingkat' => 'kampus', 'biaya' => 'gratis',
                'deskripsi' => 'Esai bertema peran generasi muda dalam transformasi digital daerah.',
                'syarat' => ['jurusan' => [], 'semester_min' => 1, 'semester_maks' => 8, 'ukuran_tim' => 1, 'format' => 'online', 'lainnya' => []],
            ],
            [
                'judul' => 'Beasiswa Unggulan Kemendikbudristek',
                'penyelenggara' => 'Kemendikbudristek',
                'kategori' => 'beasiswa', 'tingkat' => 'nasional', 'biaya' => 'gratis',
                'deskripsi' => 'Beasiswa penuh untuk mahasiswa berprestasi akademik maupun non-akademik.',
                'syarat' => ['jurusan' => [], 'semester_min' => 3, 'semester_maks' => 6, 'ukuran_tim' => 1, 'format' => 'online', 'lainnya' => ['IPK minimal 3.25', 'sertifikat prestasi']],
            ],
            [
                'judul' => 'Beasiswa Bank Indonesia',
                'penyelenggara' => 'Bank Indonesia',
                'kategori' => 'beasiswa', 'tingkat' => 'nasional', 'biaya' => 'gratis',
                'deskripsi' => 'Bantuan biaya pendidikan disertai program pembinaan komunitas penerima beasiswa.',
                'syarat' => ['jurusan' => [], 'semester_min' => 4, 'semester_maks' => 6, 'ukuran_tim' => 1, 'format' => 'offline', 'lainnya' => ['IPK minimal 3.00', 'aktif berorganisasi']],
            ],
            [
                'judul' => 'KIP Kuliah Merdeka',
                'penyelenggara' => 'Puslapdik Kemendikbudristek',
                'kategori' => 'beasiswa', 'tingkat' => 'nasional', 'biaya' => 'gratis',
                'deskripsi' => 'Bantuan biaya kuliah dan biaya hidup bagi mahasiswa dari keluarga kurang mampu.',
                'syarat' => ['jurusan' => [], 'semester_min' => 1, 'semester_maks' => 2, 'ukuran_tim' => 1, 'format' => 'online', 'lainnya' => ['surat keterangan tidak mampu']],
            ],
            [
                'judul' => 'Beasiswa Baznas Cendekia',
                'penyelenggara' => 'Baznas RI',
                'kategori' => 'beasiswa', 'tingkat' => 'nasional', 'biaya' => 'gratis',
                'deskripsi' => 'Beasiswa bagi mahasiswa aktif yang terkendala biaya pendidikan.',
                'syarat' => ['jurusan' => [], 'semester_min' => 2, 'semester_maks' => 8, 'ukuran_tim' => 1, 'format' => 'online', 'lainnya' => ['IPK minimal 3.00']],
            ],
            [
                'judul' => 'Beasiswa Prestasi Akademik Kampus',
                'penyelenggara' => 'Biro Kemahasiswaan',
                'kategori' => 'beasiswa', 'tingkat' => 'kampus', 'biaya' => 'gratis',
                'deskripsi' => 'Potongan biaya kuliah bagi mahasiswa dengan indeks prestasi tertinggi tiap program studi.',
                'syarat' => ['jurusan' => [], 'semester_min' => 3, 'semester_maks' => 7, 'ukuran_tim' => 1, 'format' => 'offline', 'lainnya' => ['IPK minimal 3.50']],
            ],
            [
                'judul' => 'Beasiswa Riset Mahasiswa Tingkat Akhir',
                'penyelenggara' => 'Lembaga Penelitian dan Pengabdian Masyarakat',
                'kategori' => 'beasiswa', 'tingkat' => 'kampus', 'biaya' => 'gratis',
                'deskripsi' => 'Dana penelitian untuk mahasiswa yang sedang menyusun tugas akhir.',
                'syarat' => ['jurusan' => [], 'semester_min' => 6, 'semester_maks' => 8, 'ukuran_tim' => 1, 'format' => 'offline', 'lainnya' => ['proposal penelitian', 'dosen pembimbing']],
            ],
            [
                'judul' => 'Magang Bersertifikat Kampus Merdeka',
                'penyelenggara' => 'Kemendikbudristek',
                'kategori' => 'magang', 'tingkat' => 'nasional', 'biaya' => 'gratis',
                'deskripsi' => 'Magang satu semester di mitra industri dengan konversi 20 SKS.',
                'syarat' => ['jurusan' => [], 'semester_min' => 5, 'semester_maks' => 7, 'ukuran_tim' => 1, 'format' => 'hybrid', 'lainnya' => ['persetujuan program studi']],
            ],
            [
                'judul' => 'Magang Pengembang Web - PT Nusantara Digital',
                'penyelenggara' => 'PT Nusantara Digital',
                'kategori' => 'magang', 'tingkat' => 'regional', 'biaya' => 'gratis',
                'deskripsi' => 'Magang tiga bulan membangun aplikasi web internal perusahaan.',
                'syarat' => ['jurusan' => ['Teknik Informatika', 'Sistem Informasi'], 'semester_min' => 5, 'semester_maks' => 8, 'ukuran_tim' => 1, 'format' => 'offline', 'lainnya' => ['menguasai HTML, CSS, JavaScript', 'punya portofolio']],
            ],
            [
                'judul' => 'Magang Desain Produk - Studio Karsa Kreatif',
                'penyelenggara' => 'Studio Karsa Kreatif',
                'kategori' => 'magang', 'tingkat' => 'regional', 'biaya' => 'gratis',
                'deskripsi' => 'Magang desain antarmuka dan identitas visual untuk klien UMKM.',
                'syarat' => ['jurusan' => ['Desain Komunikasi Visual', 'Sistem Informasi'], 'semester_min' => 4, 'semester_maks' => 8, 'ukuran_tim' => 1, 'format' => 'hybrid', 'lainnya' => ['menguasai Figma']],
            ],
            [
                'judul' => 'Magang Analis Data - Dinas Komunikasi dan Informatika',
                'penyelenggara' => 'Dinas Komunikasi dan Informatika',
                'kategori' => 'magang', 'tingkat' => 'regional', 'biaya' => 'gratis',
                'deskripsi' => 'Mengolah data layanan publik daerah menjadi laporan dan visualisasi.',
                'syarat' => ['jurusan' => ['Teknik Informatika', 'Sistem Informasi', 'Akuntansi'], 'semester_min' => 5, 'semester_maks' => 8, 'ukuran_tim' => 1, 'format' => 'offline', 'lainnya' => ['menguasai Excel atau Python']],
            ],
        ];
    }
}
