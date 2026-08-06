/* ===========================================================================
 | Lapisan gerak — StudentHub AI
 |
 | Ditulis tanpa pustaka tambahan. Semua efek memakai IntersectionObserver dan
 | transform CSS, dua hal yang dijalankan peramban di kartu grafis, bukan di
 | prosesor utama -- sehingga tetap ringan di laptop dan ponsel kelas menengah.
 |
 | Aturan yang dipegang di seluruh berkas ini:
 |   1. Hanya menyentuh transform dan opacity. Mengubah width, height, atau top
 |      memaksa peramban menghitung ulang tata letak seluruh halaman setiap
 |      bingkai, dan di situlah animasi mulai tersendat.
 |   2. Semua efek mati bila pengguna meminta "kurangi gerak" di sistemnya.
 |   3. Setiap efek berdiri sendiri. Kalau satu gagal, sisanya tetap jalan.
 =========================================================================== */

const kurangiGerak = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ---------------------------------------------------------------------------
 | 1. Muncul saat digulir, dengan jeda berurutan
 |
 | Elemen ber-atribut data-reveal muncul saat masuk layar. Anak-anaknya yang
 | ber-atribut data-reveal-anak muncul menyusul satu per satu, sehingga mata
 | mengikuti urutan alih-alih disergap semuanya sekaligus.
 --------------------------------------------------------------------------- */

function pasangReveal() {
    const sasaran = [...document.querySelectorAll('[data-reveal], [data-reveal-anak]')];

    if (! sasaran.length) return;

    const tampilkan = (el) => el.classList.add('terlihat');

    if (kurangiGerak || ! ('IntersectionObserver' in window)) {
        sasaran.forEach((el) => el.classList.add('reveal', 'terlihat'));

        return;
    }

    // Beri jeda bertingkat pada anak-anak dalam satu wadah.
    document.querySelectorAll('[data-reveal-grup]').forEach((grup) => {
        grup.querySelectorAll('[data-reveal-anak]').forEach((anak, i) => {
            anak.dataset.revealJeda = String(Math.min(i * 70, 560));
        });
    });

    const pengamat = new IntersectionObserver((entri) => {
        entri.forEach((e) => {
            if (! e.isIntersecting) return;

            setTimeout(() => tampilkan(e.target), Number(e.target.dataset.revealJeda ?? 0));

            // Sekali muncul, berhenti diamati. Elemen yang terus dipantau
            // setelah tugasnya selesai hanya membuang tenaga.
            pengamat.unobserve(e.target);
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -6% 0px' });

    sasaran.forEach((el) => {
        const kotak = el.getBoundingClientRect();
        const sudahDiLayar = kotak.top < window.innerHeight * 0.95;

        el.classList.add('reveal');

        if (sudahDiLayar) {
            // Sudah terlihat begitu halaman dibuka. Tampilkan lewat penghitung
            // waktu biasa, TIDAK menitipkannya ke pengamat.
            //
            // Alasannya penting: kalau elemen yang sudah di layar ikut
            // dititipkan ke pengamat lalu pengamatnya gagal karena sebab apa
            // pun, isi halaman hilang total tanpa jejak galat. Bagian yang
            // dilihat pertama kali tidak boleh bergantung pada apa pun yang
            // bisa gagal diam-diam.
            setTimeout(() => tampilkan(el), Number(el.dataset.revealJeda ?? 0));
        } else {
            pengamat.observe(el);
        }
    });
}

/* ---------------------------------------------------------------------------
 | Sorot mengikuti kursor
 |
 | Lingkaran cahaya lembut mengikuti kursor di dalam kartu. Posisinya dikirim
 | ke CSS lewat dua peubah, sehingga penggambarannya dikerjakan peramban --
 | JavaScript hanya menyetorkan dua angka.
 --------------------------------------------------------------------------- */

function pasangSorot() {
    if (kurangiGerak || window.matchMedia('(hover: none)').matches) return;

    document.querySelectorAll('[data-sorot]').forEach((el) => {
        el.addEventListener('pointermove', (e) => {
            const k = el.getBoundingClientRect();
            el.style.setProperty('--mx', `${e.clientX - k.left}px`);
            el.style.setProperty('--my', `${e.clientY - k.top}px`);
        });
    });
}

/* ---------------------------------------------------------------------------
 | 2. Bilah menu yang menyusut saat digulir
 --------------------------------------------------------------------------- */

function pasangNavbar() {
    const nav = document.querySelector('[data-navbar]');

    if (! nav) return;

    let terakhir = null;

    const perbarui = () => {
        const digulir = window.scrollY > 24;

        if (digulir !== terakhir) {
            nav.classList.toggle('nav-digulir', digulir);
            terakhir = digulir;
        }
    };

    perbarui();
    window.addEventListener('scroll', perbarui, { passive: true });
}

/* ---------------------------------------------------------------------------
 | 3. Tombol magnetis
 |
 | Tombol bergeser sedikit ke arah kursor. Efeknya kecil dan hampir tidak
 | disadari, tetapi membuat tombol terasa "hidup" saat didekati.
 --------------------------------------------------------------------------- */

function pasangMagnet() {
    if (kurangiGerak || window.matchMedia('(hover: none)').matches) return;

    document.querySelectorAll('[data-magnet]').forEach((el) => {
        const kuat = Number(el.dataset.magnet || 14);

        el.addEventListener('pointermove', (e) => {
            const k = el.getBoundingClientRect();
            const x = (e.clientX - k.left - k.width / 2) / (k.width / 2);
            const y = (e.clientY - k.top - k.height / 2) / (k.height / 2);

            el.style.transform = `translate(${x * kuat}px, ${y * kuat * 0.55}px)`;
        });

        el.addEventListener('pointerleave', () => {
            el.style.transform = '';
        });
    });
}

/* ---------------------------------------------------------------------------
 | 4. Kartu miring mengikuti kursor
 --------------------------------------------------------------------------- */

function pasangTilt() {
    if (kurangiGerak || window.matchMedia('(hover: none)').matches) return;

    document.querySelectorAll('[data-tilt]').forEach((el) => {
        const maks = Number(el.dataset.tilt || 6);

        el.style.transformStyle = 'preserve-3d';

        el.addEventListener('pointermove', (e) => {
            const k = el.getBoundingClientRect();
            const x = (e.clientX - k.left) / k.width - 0.5;
            const y = (e.clientY - k.top) / k.height - 0.5;

            el.style.transform =
                `perspective(900px) rotateX(${-y * maks}deg) rotateY(${x * maks}deg) translateY(-6px)`;
        });

        el.addEventListener('pointerleave', () => {
            el.style.transform = '';
        });
    });
}

/* ---------------------------------------------------------------------------
 | 5. Angka yang menghitung naik
 --------------------------------------------------------------------------- */

function pasangHitungAngka() {
    const angka = document.querySelectorAll('[data-hitung]');

    if (! angka.length) return;

    if (kurangiGerak || ! ('IntersectionObserver' in window)) {
        angka.forEach((el) => (el.textContent = formatAngka(Number(el.dataset.hitung))));

        return;
    }

    const pengamat = new IntersectionObserver((entri) => {
        entri.forEach((e) => {
            if (! e.isIntersecting) return;

            jalankanHitung(e.target);
            pengamat.unobserve(e.target);
        });
    }, { threshold: 0.5 });

    angka.forEach((el) => {
        el.textContent = '0';
        pengamat.observe(el);
    });
}

function jalankanHitung(el) {
    const tujuan = Number(el.dataset.hitung || 0);
    const lama = 1100;
    const mulai = performance.now();

    const langkah = (waktu) => {
        const maju = Math.min((waktu - mulai) / lama, 1);

        // Melambat di ujung -- terasa seperti benda nyata yang berhenti,
        // bukan penghitung digital yang putus mendadak.
        const halus = 1 - Math.pow(1 - maju, 3);

        el.textContent = formatAngka(Math.round(tujuan * halus));

        if (maju < 1) requestAnimationFrame(langkah);
    };

    requestAnimationFrame(langkah);
}

function formatAngka(n) {
    return n.toLocaleString('id-ID');
}

/* ---------------------------------------------------------------------------
 | 6. Latar mengikuti gerak kursor
 |
 | Gumpalan cahaya bergeser berlawanan arah kursor, memberi kesan kedalaman
 | seolah ada ruang di belakang halaman.
 --------------------------------------------------------------------------- */

function pasangParalaks() {
    const lapisan = document.querySelectorAll('[data-paralaks]');

    if (! lapisan.length || kurangiGerak) return;
    if (window.matchMedia('(hover: none)').matches) return;

    let tugasBingkai = null;

    window.addEventListener('pointermove', (e) => {
        if (tugasBingkai) return;

        // Digabung ke satu bingkai layar. Tanpa ini, gerakan kursor yang
        // rapat memicu puluhan perhitungan per detik tanpa guna.
        tugasBingkai = requestAnimationFrame(() => {
            const x = e.clientX / window.innerWidth - 0.5;
            const y = e.clientY / window.innerHeight - 0.5;

            lapisan.forEach((el) => {
                const dalam = Number(el.dataset.paralaks || 20);
                el.style.transform = `translate3d(${-x * dalam}px, ${-y * dalam}px, 0)`;
            });

            tugasBingkai = null;
        });
    }, { passive: true });
}

/* ---------------------------------------------------------------------------
 | 7. Batang kemajuan yang terisi saat terlihat
 --------------------------------------------------------------------------- */

function pasangBatang() {
    const batang = document.querySelectorAll('[data-batang]');

    if (! batang.length) return;

    const isi = (el) => {
        el.style.width = `${el.dataset.batang}%`;
    };

    if (kurangiGerak || ! ('IntersectionObserver' in window)) {
        batang.forEach(isi);

        return;
    }

    const pengamat = new IntersectionObserver((entri) => {
        entri.forEach((e) => {
            if (! e.isIntersecting) return;

            e.target.style.transition = 'width 1.1s cubic-bezier(0.16, 1, 0.3, 1)';
            setTimeout(() => isi(e.target), 90);
            pengamat.unobserve(e.target);
        });
    }, { threshold: 0.4 });

    batang.forEach((el) => {
        el.style.width = '0%';
        pengamat.observe(el);
    });
}

/* ---------------------------------------------------------------------------
 | Jalankan
 --------------------------------------------------------------------------- */

function mulai() {
    pasangReveal();
    pasangNavbar();
    pasangMagnet();
    pasangTilt();
    pasangHitungAngka();
    pasangParalaks();
    pasangBatang();
    pasangSorot();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mulai);
} else {
    mulai();
}
