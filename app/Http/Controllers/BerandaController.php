<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BerandaController extends Controller
{
    /**
     * Beranda mahasiswa: rekomendasi berdasarkan skor kecocokan,
     * ditambah pengingat deadline dari peluang yang sudah disimpan.
     */
    public function index(Request $request): View
    {
        $pengguna = $request->user();
        $profil = $pengguna->profile;

        // Peluang dengan skor tertinggi yang masih boleh didaftar.
        // Kueri ini persis yang dilayani indeks gabungan user_id + skor
        // pada tabel opportunity_matches, dirancang di Sesi 03.
        $rekomendasi = $pengguna->matches()
            ->with('opportunity')
            ->whereHas('opportunity', fn ($q) => $q->disetujui()->masihBuka())
            ->orderByDesc('skor')
            ->take(6)
            ->get();

        // Yang sudah disimpan tapi deadline-nya paling dekat.
        // Menjawab temuan riset tema nomor 7: "sering menyimpan poster,
        // tetapi lupa membacanya lagi".
        $segeraBerakhir = $pengguna->peluangTersimpan()
            ->disetujui()
            ->masihBuka()
            ->whereNotNull('deadline')
            ->orderBy('deadline')
            ->take(3)
            ->get();

        return view('beranda', [
            'profil' => $profil,
            'kelengkapan' => $profil?->kelengkapan() ?? 0,
            'rekomendasi' => $rekomendasi,
            'segeraBerakhir' => $segeraBerakhir,
            'jumlah' => [
                'tersimpan' => $pengguna->savedItems()->count(),
                'dinilai' => $pengguna->matches()->count(),
                'tersedia' => Opportunity::disetujui()->masihBuka()->count(),
            ],
        ]);
    }
}
