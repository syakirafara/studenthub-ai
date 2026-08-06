<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpportunityMatch extends Model
{
    protected $fillable = [
        'user_id', 'opportunity_id', 'skor',
        'terpenuhi', 'belum_terpenuhi', 'saran', 'dihitung_pada',
    ];

    protected $casts = [
        'terpenuhi' => 'array',
        'belum_terpenuhi' => 'array',
        'dihitung_pada' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    /**
     * Menggolongkan skor menjadi tiga tingkat.
     *
     * Batasnya sengaja dipusatkan di sini, bukan disebar di tampilan --
     * kalau suatu saat batasnya diubah, cukup satu tempat.
     */
    public function golongan(): string
    {
        return match (true) {
            $this->skor >= 75 => 'tinggi',
            $this->skor >= 50 => 'sedang',
            default => 'rendah',
        };
    }

    /**
     * Kalimat yang dibaca mahasiswa, bukan sekadar angka.
     */
    public function keterangan(): string
    {
        return match ($this->golongan()) {
            'tinggi' => 'Sangat cocok untukmu',
            'sedang' => 'Cukup cocok, ada yang perlu disiapkan',
            default => 'Kurang cocok untuk saat ini',
        };
    }

    /**
     * Nama warna dari sistem desain, ditetapkan di app.css pada Sesi 07.
     */
    public function warna(): string
    {
        return match ($this->golongan()) {
            'tinggi' => 'utama',
            'sedang' => 'waspada',
            default => 'abu',
        };
    }
}
