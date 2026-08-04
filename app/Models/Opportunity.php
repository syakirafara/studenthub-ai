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

    public function scopeMasihBuka($query)
    {
        return $query->whereDate('deadline', '>=', now());
    }
}
