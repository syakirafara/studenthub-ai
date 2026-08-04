<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtractionReview extends Model
{
    protected $fillable = [
        'opportunity_id', 'hasil_ai', 'hasil_final',
        'jumlah_koreksi', 'field_dikoreksi', 'reviewed_by',
    ];

    protected $casts = [
        'hasil_ai' => 'array',
        'hasil_final' => 'array',
        'field_dikoreksi' => 'array',
    ];

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
