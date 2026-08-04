<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiLog extends Model
{
    protected $fillable = [
        'jenis', 'model', 'user_id',
        'token_masuk', 'token_keluar', 'durasi_ms',
        'status', 'pesan_error',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDariCache($query)
    {
        return $query->where('status', 'dari_cache');
    }
}
