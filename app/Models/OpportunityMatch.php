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
}
