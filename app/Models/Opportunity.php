<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model
{
    protected $fillable = [
        'judul', 'penyelenggara', 'kategori', 'deskripsi', 'deadline',
        'biaya', 'nominal_biaya', 'tingkat', 'link', 'poster_path',
        'syarat', 'status', 'catatan_admin',
        'submitted_by', 'verified_by', 'verified_at',
    ];

    protected $casts = [
        'syarat' => 'array',
        'deadline' => 'date',
        'verified_at' => 'datetime',
    ];

    public function pengunggah()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function matches()
    {
        return $this->hasMany(OpportunityMatch::class);
    }

    public function review()
    {
        return $this->hasOne(ExtractionReview::class);
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    /**
     * Peluang yang masih boleh didaftar.
     *
     * Deadline kosong ikut dihitung masih buka, karena banyak poster tidak
     * mencantumkan tanggal. Menyembunyikannya berarti menghilangkan peluang
     * yang sebenarnya masih berlaku.
     */
    public function scopeMasihBuka($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('deadline')
                ->orWhereDate('deadline', '>=', now());
        });
    }

    /**
     * Sisa hari menuju deadline.
     * Positif berarti masih ada waktu, negatif berarti sudah lewat,
     * null berarti deadline tidak disebutkan.
     */
    public function sisaHari(): ?int
    {
        if (! $this->deadline) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->deadline->startOfDay(), false);
    }

    /**
     * Golongan deadline, dipakai untuk menentukan warna lencana.
     */
    public function statusDeadline(): string
    {
        $sisa = $this->sisaHari();

        return match (true) {
            $sisa === null => 'tidak_disebutkan',
            $sisa < 0 => 'lewat',
            $sisa === 0 => 'hari_ini',
            $sisa <= 7 => 'mepet',
            default => 'aman',
        };
    }

    /**
     * Kalimat hitung mundur yang dibaca pengguna.
     */
    public function teksDeadline(): string
    {
        $sisa = $this->sisaHari();

        return match (true) {
            $sisa === null => 'Deadline tidak disebutkan',
            $sisa < 0 => 'Sudah berakhir',
            $sisa === 0 => 'Berakhir hari ini',
            $sisa === 1 => 'Tinggal 1 hari',
            default => "Tinggal {$sisa} hari",
        };
    }
}
