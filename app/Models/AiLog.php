<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiLog extends Model
{
    /**
     | Di bawah jumlah ini, rata-rata belum berarti apa-apa: satu panggilan
     | yang kebetulan lambat akan menarik seluruh angka.
     */
    private const SAMPEL_MINIMUM = 3;

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

    /**
     * Perkiraan lama satu jenis panggilan, DIHITUNG dari catatan yang sudah
     * terjadi -- bukan angka yang dihafal di dalam tampilan.
     *
     * Tampilan sempat menjanjikan "10-20 detik" sementara panggilan yang
     * benar-benar tercatat memakan 30 detik. Janji yang meleset di layar
     * sendiri jauh lebih merusak kepercayaan daripada angka besar yang benar
     * -- apalagi kalau melesetnya terjadi saat ditonton juri.
     *
     * Selama sampelnya belum cukup, yang dikembalikan adalah kalimat cadangan
     * yang jujur disebut sebagai perkiraan, bukan rata-rata palsu dari satu
     * dua panggilan.
     *
     * @param  string  $jenis  'ekstraksi_poster' atau 'skor_kecocokan'
     * @param  string  $cadangan  dipakai selama sampel belum cukup
     * @return array{terukur: bool, jumlah: int, teks: string}
     */
    public static function perkiraan(string $jenis, string $cadangan): array
    {
        $dasar = static::query()
            ->where('jenis', $jenis)
            ->where('status', 'berhasil');

        $jumlah = (clone $dasar)->count();

        if ($jumlah < self::SAMPEL_MINIMUM) {
            return ['terukur' => false, 'jumlah' => $jumlah, 'teks' => $cadangan];
        }

        $rata = (int) round((float) (clone $dasar)->avg('durasi_ms') / 1000);
        $paling = (int) round((float) (clone $dasar)->max('durasi_ms') / 1000);

        // Kalau rata-rata dan yang terlama hampir sama, menyebut keduanya
        // justru bertele-tele.
        $teks = $paling > $rata + 1
            ? "sekitar {$rata} detik, paling lama {$paling} detik"
            : "sekitar {$rata} detik";

        return ['terukur' => true, 'jumlah' => $jumlah, 'teks' => $teks];
    }
}
