# StudentHub AI

**Semua peluang mahasiswa dalam satu tempat, dengan AI yang memberitahu mana yang benar-benar cocok untukmu.**

Karya untuk **TCC Vibe Code 2026** — Creative Computer Club, Universitas Trunojoyo Madura.

---

## Akun demo

Basis data contoh sudah tersedia. Silakan masuk langsung tanpa mendaftar:

| Peran | Email | Kata sandi |
|---|---|---|
| Mahasiswa | `mahasiswa@studenthub.test` | `password` |
| Admin | `admin@studenthub.test` | `password` |

Katalog peluang dapat dilihat **tanpa masuk sama sekali** di `/peluang`. Yang membutuhkan akun hanya skor kecocokan, penyimpanan, dan pengunggahan poster.

---

## Masalah yang diselesaikan

Informasi lomba, beasiswa, dan magang untuk mahasiswa tersebar di puluhan akun Instagram, grup WhatsApp, dan situs kampus. Akibatnya:

1. **Tersebar** — satu mahasiswa harus memantau banyak sumber setiap hari agar tidak ketinggalan.
2. **Terlambat** — informasi sering baru diketahui setelah pendaftaran ditutup.
3. **Syarat tidak jelas** — poster jarang menjelaskan siapa yang boleh ikut, sehingga banyak yang batal mendaftar karena ragu memenuhi syarat.
4. **Tidak lengkap** — poster menarik, tetapi detailnya harus dicari sendiri.

> **Catatan kejujuran:** empat poin di atas adalah **hipotesis masalah** yang disusun dari pengalaman sendiri sebagai mahasiswa, bukan hasil survei berstatistik. Yang sudah ada buktinya baru poin ke-3: dari **20 poster Instagram sungguhan** yang diproses sistem ini, sebagian tidak mencantumkan tingkat kegiatan maupun syarat peserta secara jelas — lihat bagian akurasi di bawah. Audit lapangan yang lebih lengkap dilampirkan di `docs/RISET.md`.

---

## Solusi

StudentHub AI mengumpulkan informasi peluang secara gotong royong, lalu memakai AI untuk dua hal yang selama ini dikerjakan manual.

### Fitur 01 — Poster dibaca AI, bukan diketik ulang

Mahasiswa cukup mengunggah **screenshot poster**. AI membacanya dan mengisi sendiri judul, penyelenggara, kategori, deadline, biaya, tautan, dan **syarat peserta dalam bentuk terstruktur**.

Ini menjawab hambatan terbesar model gotong royong: orang bersedia berkontribusi, asal caranya cepat.

### Fitur 04 — Skor kecocokan

Untuk setiap peluang, AI membandingkan **syaratnya** dengan **profil mahasiswa** (jurusan, semester, minat, kemampuan, preferensi) lalu menghasilkan:

- skor 0–100
- daftar syarat yang **sudah** dipenuhi
- daftar syarat yang **belum** dipenuhi
- saran yang bisa langsung dikerjakan

### Fitur pendukung

| | |
|---|---|
| **Katalog** | Pencarian, empat penyaring, dua urutan, hitung mundur deadline. Terbuka untuk umum |
| **Simpan** | Menandai peluang, diurutkan dari deadline terdekat |
| **Profil** | Minat dan kemampuan sebagai bahan perhitungan kecocokan |
| **Dasbor Admin** | Antrean verifikasi, deteksi kemiripan, dan pengukuran akurasi AI |
| **Beranda Saya** | Rekomendasi berdasarkan skor tertinggi + pengingat deadline |

---

## Bagaimana AI dipakai

### Dua model, dipilih sesuai berat tugas

| Peran | Model | Untuk |
|---|---|---|
| berat | `gemini-3.6-flash` | Membaca poster — tugas penglihatan, tulisan poster sering kecil |
| ringan | `gemini-3.5-flash-lite` | Skor kecocokan — hanya membandingkan teks |

**Lama pembacaan poster, dari 19 panggilan yang berhasil:** rata-rata **21,1 detik**, tercepat 12,1 detik, terlama 76,3 detik. Sebarannya lebar karena bergantung pada ukuran poster dan keadaan jaringan.

Karena itu tampilan tidak menuliskan angka tetap. Halaman unggah dan halaman rincian membaca langsung dari `ai_logs`, dan **mengaku "masih perkiraan" selama sampelnya di bawah tiga** — lihat `AiLog::perkiraan()`. Janji waktu yang meleset di layar sendiri lebih merusak kepercayaan daripada angka besar yang benar.

Versinya **dipatok**, bukan memakai alias `gemini-flash-latest`, agar hasil dapat diulang: penilai yang menjalankan proyek ini bulan depan mendapat perilaku yang sama.

### Bentuk balasan AI dipaksa, bukan diminta

Proyek ini memakai **Structured Outputs**: skema JSON dikirim bersama permintaan, sehingga model dijamin mengembalikan bentuk yang benar. Tidak ada kode pengurai, tidak ada pengulangan karena format kacau.

Skema `syarat` ditetapkan **sebelum** kedua fitur AI dibangun, sebagai kontrak di antara keduanya:

```json
{
  "jurusan": ["Teknik Informatika", "Sistem Informasi"],
  "semester_min": 1,
  "semester_maks": 8,
  "ukuran_tim": 3,
  "format": "online",
  "lainnya": ["IPK minimal 3.00", "punya portofolio"]
}
```

### AI dilarang mengarang

Instruksi pembacaan poster berisi aturan tegas: **kolom yang tidak tertulis di poster dikosongkan, bukan ditebak.** Sebagian besar kolom di basis data sengaja dibuat boleh kosong justru untuk mendukung ini.

Alasannya bukan kerapian, melainkan keselamatan pengguna: **deadline karangan lebih berbahaya daripada deadline kosong** — mahasiswa bisa melewatkan lomba karena tanggal palsu.

Diuji dengan sengaja memberi gambar yang **bukan** poster; sistem menjawab *"Informasi Tidak Ditemukan"* alih-alih mengarang isinya.

### Akurasi diukur, bukan diklaim

Setiap pembacaan AI disimpan apa adanya di tabel `extraction_reviews`. Saat admin memverifikasi, sistem membandingkan **10 kolom** antara bacaan AI dan hasil akhir, lalu mencatat berapa yang dikoreksi **dan kolom mana saja**.

**Hasil dari 20 poster Instagram sungguhan: akurasi 97,5%** — 195 dari 200 kolom benar tanpa koreksi, dan **16 dari 20 poster dibaca sempurna**.

### Alat ukurnya sendiri pernah salah

Angka pertama yang dilaporkan sistem ini adalah **92%**, dan angka itu keliru.

Penyebabnya bukan AI, melainkan cara membandingkannya. Bacaan AI `"2026-08-19"` dibandingkan mentah-mentah dengan nilai basis data `"2026-08-18T17:00:00.000000Z"` — dua tulisan berbeda untuk **tanggal yang sama persis**, sebab yang kedua disimpan dalam UTC sementara yang pertama tidak. Akibatnya `deadline` tercatat "dikoreksi" pada **12 poster yang sebenarnya dibaca benar**.

Perbaikannya: kolom bertipe tanggal kini diseragamkan ke `YYYY-MM-DD` di zona Asia/Jakarta sebelum dibandingkan (`AdminPeluangController::sama()`), dan perilakunya dikunci empat pengujian di `tests/Unit/PembandingAkurasiTest.php`.

> **Alat ukur yang salah lebih berbahaya daripada tidak mengukur sama sekali**, karena hasilnya tetap terlihat meyakinkan. Kalau tidak diperiksa, karya ini akan mengaku 92% padahal sebenarnya 97,5% — dan menyalahkan AI atas kesalahan kodenya sendiri.

### Lima kesalahan yang tersisa memang nyata

| Kolom | Salah di | Sebabnya | Perbaikan |
|---|---|---|---|
| `deadline` | 4 dari 20 | Instruksi berbunyi *"gunakan tahun berjalan"* — perintah yang **mustahil dipatuhi**, karena model bahasa tidak punya jam. Ia jatuh ke tahun masa pelatihannya, sehingga poster 2026 dibaca 2024 | Tanggal hari ini kini dikirim di dalam instruksi, ditambah aturan: hasil yang jatuh di masa lalu tanpa tahun tertulis berarti tahunnya keliru |
| `link` | 1 dari 20 | Poster menulis `contoh.or.id` tanpa `https://` | Instruksi ditambah: tulis alamat lengkap |

Delapan kolom lainnya — judul, penyelenggara, kategori, deskripsi, biaya, nominal, tingkat, syarat — **tidak pernah sekali pun dikoreksi** dalam 20 poster.

Siklus **ukur → temukan pola → perbaiki** ini berjalan dari data sistem sendiri, bukan dari perkiraan.

### Penghematan panggilan AI

Skor kecocokan disimpan per pasangan pengguna–peluang, dijaga batasan unik di basis data. Permintaan berikutnya dilayani dari hasil tersimpan tanpa memanggil AI, dan tetap dicatat dengan status `dari_cache` — sehingga persentase penghematan dapat dihitung dan ditampilkan di Dasbor Admin.

Manfaat lainnya: skor tidak berubah-ubah setiap halaman disegarkan.

---

## Teknologi

| | |
|---|---|
| Kerangka kerja | Laravel 13.8 · PHP 8.3 |
| Basis data | MySQL 8 · 15 tabel |
| Tampilan | Blade · Tailwind CSS 4 · Vite 8 |
| AI | Google Gemini (paket gratis) |
| Perapi kode | Laravel Pint |

Proyek ini **tidak memakai paket autentikasi siap pakai**. Halaman masuk, daftar, dan penjagaan peran ditulis sendiri di atas lapisan keamanan bawaan Laravel, agar setiap baris dapat dipertanggungjawabkan.

---

## Menjalankan di komputer sendiri

**Prasyarat:** PHP 8.3+, Composer, MySQL, Node.js 20+.

```bash
# 1. Ambil kode dan pasang ketergantungan
git clone https://github.com/syakirafara/studenthub-ai.git
cd studenthub-ai
composer install
npm install

# 2. Siapkan berkas pengaturan
cp .env.example .env
php artisan key:generate

# 3. Isi bagian database dan kunci AI di .env
#    GEMINI_API_KEY diambil gratis di https://aistudio.google.com
#    (Get API key -- cukup akun Google, tanpa kartu kredit)

# 4. Buat tabel dan isi data contoh
php artisan migrate --seed

# 5. Sambungkan folder penyimpanan poster
php artisan storage:link

# 6. Jalankan (dua terminal)
php artisan serve      # terminal 1
npm run dev            # terminal 2
```

Buka `http://127.0.0.1:8000`, lalu masuk dengan akun demo di atas.

Data contoh berisi **15 pengguna**, **19 peluang** dengan sebaran deadline dan status yang beragam, **46 simpanan**, dan **79 skor kecocokan** — sehingga seluruh halaman langsung terisi tanpa perlu memasukkan data manual.

Untuk memastikan semuanya berjalan sebagaimana mestinya:

```bash
php artisan test
```

---

## Struktur basis data

Tujuh tabel inti di luar bawaan Laravel:

| Tabel | Isinya |
|---|---|
| `users` | Akun dan peran (`mahasiswa` \| `admin`) |
| `profiles` | Data akademik, minat, kemampuan, preferensi |
| `opportunities` | Peluang hasil pembacaan AI |
| `opportunity_matches` | Skor kecocokan tersimpan — sekaligus penghemat panggilan AI |
| `saved_items` | Peluang yang ditandai mahasiswa |
| `ai_logs` | Catatan tiap panggilan AI: model, token, durasi, status |
| `extraction_reviews` | Bacaan AI vs hasil akhir admin — dasar pengukuran akurasi |

Dua tabel terakhir tidak dibutuhkan untuk menjalankan fitur, tetapi ada dengan sengaja: **tanpa keduanya, klaim efisiensi dan akurasi hanya bisa diucapkan, tidak bisa dibuktikan.**

---

## Pengujian

```bash
php artisan test
```

**46 pengujian, 88 pemeriksaan, seluruhnya lolos.** Dijalankan di atas SQLite di memori, sehingga tidak menyentuh basis data pengembangan dan tidak memerlukan penyiapan apa pun.

| Berkas | Yang dijaga |
|---|---|
| `tests/Feature/AlurUtamaTest.php` | Alur produk dari ujung ke ujung: katalog, penyaringan, penyimpanan, pendaftaran akun, dan verifikasi admin |
| `tests/Feature/HalamanTergambarTest.php` | Setiap halaman benar-benar tergambar tanpa galat, termasuk pada keadaan kosong dan keadaan tepi |
| `tests/Unit/PerkiraanWaktuTest.php` | Perkiraan lama panggilan AI hanya disebut terukur setelah sampelnya cukup |
| `tests/Unit/PembandingAkurasiTest.php` | Tanggal yang sama tidak boleh terhitung sebagai koreksi hanya karena beda bentuk simpanan |

Beberapa pengujian sengaja menjaga bug yang **pernah benar-benar terjadi**, supaya tidak kembali diam-diam:

- Peluang tanpa deadline **tidak boleh** hilang dari katalog — penyaring deadline pernah ikut membuang baris yang tanggalnya kosong
- Kolom `role` **tidak boleh** bisa diisi dari formulir pendaftaran — kalau bisa, siapa pun dapat mengangkat dirinya jadi admin
- Peluang yang belum disetujui **harus** menolak dengan 404 bagi mahasiswa, tetapi tetap terbuka bagi admin
- Halaman beranda **harus** tetap tergambar walau akunnya belum punya profil
- Menyimpan peluang dua kali **tidak boleh** menggandakan baris

> Pengujian di sini bukan untuk mengejar angka cakupan, melainkan untuk mengunci perilaku yang sudah terbukti pernah salah. Setiap pengujian yang menjaga bug lama diberi komentar yang menerangkan bug apa yang dijaganya.

---

## Keputusan teknis yang perlu diketahui

| Keputusan | Alasannya |
|---|---|
| **Katalog terbuka tanpa login** | Siapa pun bisa langsung melihat isinya. Yang membutuhkan akun hanya skor kecocokan — dan justru itu yang membuat orang mau mendaftar |
| **Deadline kosong dianggap masih buka** | Banyak poster tidak mencantumkan tanggal. Menyembunyikannya berarti membuang peluang yang sebenarnya masih berlaku |
| **Peran ditulis paksa di kode, tidak pernah dari formulir** | Mencegah siapa pun menjadi admin hanya dengan menambahkan kolom pada kiriman formulir |
| **Pesan galat masuk sengaja disamarkan** | Membedakan "email tidak terdaftar" dan "kata sandi salah" membuat formulir masuk dapat dipakai memeriksa email mana yang terdaftar |
| **Peluang belum terverifikasi menolak dengan 404, bukan 403** | 403 membocorkan bahwa nomor tersebut ada |
| **Poster diperkecil ke 1280 piksel sebelum dikirim ke AI** | Tulisan tetap terbaca, tetapi token jauh berkurang. Yang disimpan pun versi kecilnya |
| **Kunci API dibaca lewat `config`, tidak pernah `env()` langsung** | Setelah `config:cache` dijalankan saat penyebaran, `env()` di luar folder config mengembalikan null tanpa galat — fitur AI mati diam-diam |

---

## Keterbatasan yang kami akui

Disebutkan terang-terangan karena pengguna dan penilai berhak tahu.

1. **Data pada paket gratis Gemini dipakai penyedia untuk mengembangkan produk mereka.** Pada paket berbayar tidak. Untuk purwarupa ini datanya berupa poster publik, bukan data pribadi. Pemanggilan AI sudah dipisahkan ke satu lapisan layanan, sehingga berpindah ke paket berbayar tidak mengubah kode fitur.
2. **Belum ada pengingat deadline otomatis.** Kebutuhan ini muncul di analisis masalah dan diakui nyata, tetapi memerlukan antrean dan layanan pengiriman yang tidak sebanding dengan waktu pengembangan. Sebagai gantinya tersedia hitung mundur di setiap kartu dan daftar deadline terdekat di Beranda.
3. **Deteksi kemiripan judul dihitung di sisi PHP.** Ringan untuk ratusan baris; pada puluhan ribu baris langkah berikutnya adalah indeks pencarian teks penuh.
4. **Penyuntingan syarat oleh admin belum tersedia.** Bila hasil bacaan syarat keliru berat, jalan yang tersedia saat ini adalah menolak unggahan disertai alasan.
5. **Kuota paket gratis Gemini adalah batas yang jebol paling awal — dan ini sudah dialami, bukan diperkirakan.** Pada percobaan pengumpulan data, kuota harian model penglihatan habis setelah **21 panggilan dalam satu hari**, dan sisa unggahan harus menunggu kuota disetel ulang keesokan harinya.

   Ini menjawab pertanyaan *"apa yang jebol duluan?"* dengan angka nyata dari catatan sistem, bukan dengan perkiraan. Ketika itu terjadi, sistem tidak rusak: galat aslinya disimpan di `ai_logs` untuk diagnosis, dan pengguna menerima kalimat *"Kuota AI hari ini sudah habis. Coba lagi besok."* dalam bahasa Indonesia.

   Tiga cara menaikkannya, berurutan dari yang paling murah: menyimpan hasil agar poster yang sama tidak diproses ulang (sudah berjalan), membatasi unggahan per pengguna per hari, lalu berpindah ke paket berbayar. Karena seluruh pemanggilan AI terkumpul di satu lapisan layanan, perpindahan itu tidak mengubah kode fitur mana pun.

---

## Rencana pengembangan

- Pengingat deadline lewat surel atau WhatsApp
- Penyuntingan syarat oleh admin dan oleh pengunggah
- Audit lapangan sebagai pengganti hipotesis masalah
- Pencarian teks penuh untuk deteksi duplikat pada data besar
- Penilaian mutu unggahan per kontributor

---

## Penulis

**Syakira Fara Salsabila** — Universitas Dian Nuswantoro

Dikembangkan untuk TCC Vibe Code 2026 dengan bantuan AI sebagai pendamping pengembangan, sesuai semangat lomba *vibe coding*. Seluruh keputusan rancangan, pengujian, dan verifikasi dilakukan sendiri dan terdokumentasi di riwayat commit.
