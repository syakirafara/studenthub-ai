<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    /**
     * Daftar minat yang boleh dipilih.
     *
     * Sengaja dikunci, bukan isian bebas. Alasannya: nilai inilah yang nanti
     * dibandingkan AI dengan syarat peluang. Kalau bebas, "UI/UX", "ui ux",
     * dan "desain antarmuka" jadi tiga hal berbeda dan kecocokan meleset.
     */
    public const MINAT = [
        'web' => 'Pengembangan Web',
        'mobile' => 'Aplikasi Mobile',
        'data' => 'Data & Analitik',
        'ui-ux' => 'Desain UI/UX',
        'desain' => 'Desain Grafis',
        'bisnis' => 'Bisnis & Kewirausahaan',
        'penelitian' => 'Penelitian & Karya Tulis',
        'sosial' => 'Sosial & Pengabdian',
    ];

    /**
     * Daftar kemampuan yang boleh dipilih.
     */
    public const SKILL = [
        'html-css' => 'HTML & CSS',
        'javascript' => 'JavaScript',
        'php' => 'PHP',
        'python' => 'Python',
        'figma' => 'Figma',
        'excel' => 'Excel / Spreadsheet',
        'public-speaking' => 'Public Speaking',
        'menulis' => 'Menulis',
    ];

    protected $fillable = [
        'user_id', 'universitas', 'jurusan', 'semester',
        'minat', 'skill', 'preferensi',
    ];

    protected $casts = [
        'minat' => 'array',
        'skill' => 'array',
        'preferensi' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Seberapa lengkap profil ini, dalam persen.
     *
     * Dipakai untuk mendorong mahasiswa melengkapi profilnya, karena skor
     * kecocokan hanya seakurat data yang tersedia.
     */
    public function kelengkapan(): int
    {
        $bagian = [
            (bool) $this->universitas,
            (bool) $this->jurusan,
            (bool) $this->semester,
            ! empty($this->minat),
            ! empty($this->skill),
            ! empty($this->preferensi),
        ];

        $terisi = count(array_filter($bagian));

        return (int) round($terisi / count($bagian) * 100);
    }

    /**
     * Nama minat dalam bahasa manusia, bukan kode.
     */
    public function namaMinat(): array
    {
        return array_values(array_intersect_key(self::MINAT, array_flip($this->minat ?? [])));
    }

    /**
     * Nama kemampuan dalam bahasa manusia, bukan kode.
     */
    public function namaSkill(): array
    {
        return array_values(array_intersect_key(self::SKILL, array_flip($this->skill ?? [])));
    }
}
