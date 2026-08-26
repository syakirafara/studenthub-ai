# Deskripsi Karya — untuk PDF pengumpulan

> Berkas ini adalah **naskah**. Salin isinya ke Word atau Google Docs, lalu ekspor
> sebagai PDF dengan nama:
> `SyakiraFaraSalsabila_UniversitasDianNuswantoro_StudentHubAI.pdf`

---

## Naskah utama — 137 kata

**StudentHub AI — semua peluang mahasiswa dalam satu tempat**

Informasi lomba, beasiswa, dan magang tersebar di puluhan akun dan grup. Mahasiswa harus memantau banyak sumber setiap hari, sering baru tahu setelah pendaftaran tutup, dan ragu mendaftar karena syaratnya tidak jelas.

StudentHub AI menjawabnya dengan dua fitur AI. Pertama, mahasiswa cukup mengunggah screenshot poster; AI membacanya menjadi data terstruktur — judul, deadline, biaya, dan syarat peserta — tanpa mengetik ulang. Kedua, AI membandingkan syarat itu dengan profil akademik pengguna dan menghasilkan skor kecocokan 0–100, lengkap dengan syarat yang sudah dan belum terpenuhi, serta saran yang bisa langsung dikerjakan.

Setiap panggilan AI dicatat, dan hasil bacaannya dibandingkan dengan koreksi admin — sehingga akurasi terukur, bukan diklaim. Dari 20 poster Instagram sungguhan, akurasinya 97,5%; enam belas di antaranya dibaca sempurna tanpa koreksi. Angka itu beserta instruksi AI yang dipakai terbuka di halaman /transparansi.

Dibangun dengan Laravel 13, MySQL, dan Google Gemini.

---

## Cadangan pendek — 94 kata

*(bila format PDF menuntut lebih ringkas)*

**StudentHub AI — semua peluang mahasiswa dalam satu tempat**

Informasi lomba, beasiswa, dan magang tersebar di puluhan akun. Mahasiswa sering terlambat tahu, dan ragu mendaftar karena syaratnya tidak jelas.

StudentHub AI memakai AI untuk dua hal. Poster cukup diunggah sebagai screenshot; AI membacanya menjadi data terstruktur tanpa perlu diketik ulang. Lalu AI membandingkan syarat peluang dengan profil akademik pengguna, menghasilkan skor kecocokan 0–100 beserta syarat yang belum terpenuhi dan saran yang bisa dikerjakan.

Akurasi pembacaan diukur dari koreksi admin, bukan diklaim: 97,5% dari 20 poster sungguhan.

Dibangun dengan Laravel 13, MySQL, dan Google Gemini.

---

## Catatan penyusunan

| Hal | Alasannya |
|---|---|
| Kalimat pembuka langsung ke masalah, bukan ke teknologi | Juri membaca puluhan naskah; masalah yang dikenali membuat mereka terus membaca |
| Angka 97,5% disebut beserta jumlah sampelnya | Persentase tanpa "dari berapa" mudah dicurigai. "97,5% dari 20 poster" bisa diperiksa; "akurat" tidak |
| Halaman /transparansi disebut | Mengubah klaim menjadi sesuatu yang bisa dibuka juri sendiri, tanpa akun |
| Kata "diukur, bukan diklaim" | Membedakan dari peserta yang hanya menyebut memakai AI |
| Teknologi ditaruh di kalimat terakhir | Bukan nilai jualnya, tetapi tetap perlu ada |
| Tidak menyebut "survei" atau angka responden | Riset lapangan belum dilakukan; menyebutnya akan menjadi klaim yang tidak bisa dibuktikan |

## Yang harus diperbarui sebelum dikirim

- [x] ~~Angka akurasi, bila sudah lebih dari satu poster diverifikasi~~ — 26 Agustus: 97,5% dari 20 poster
- [ ] Tambahkan tautan demo bila sudah di-deploy
- [ ] Ganti hipotesis masalah dengan angka audit riset, bila sudah dikerjakan
- [ ] Pastikan repositori sudah publik sebelum tautannya dikirim
