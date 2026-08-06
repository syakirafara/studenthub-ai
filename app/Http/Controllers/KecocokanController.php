<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Models\OpportunityMatch;
use App\Services\LayananAI;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class KecocokanController extends Controller
{
    /**
     * Menghitung skor kecocokan satu mahasiswa dengan satu peluang.
     */
    public function hitung(Request $request, Opportunity $peluang, LayananAI $ai): RedirectResponse
    {
        abort_unless($peluang->status === 'disetujui', 404);

        $pengguna = $request->user();
        $profil = $pengguna->profile;

        // Tanpa minat dan kemampuan, AI tidak punya bahan apa pun untuk
        // dibandingkan. Lebih baik diarahkan melengkapi profil daripada
        // menghasilkan skor yang tidak bermakna.
        if (! $profil || empty($profil->minat) || empty($profil->skill)) {
            return redirect()
                ->route('profil.edit')
                ->with('gagal', 'Lengkapi minat dan kemampuanmu dulu supaya skor kecocokan bisa dihitung.');
        }

        // Sudah pernah dihitung? Pakai yang tersimpan, jangan panggil AI lagi.
        // Batasan unique pada pasangan user_id + opportunity_id di database
        // memastikan hanya ada satu hasil per pasangan.
        $tersimpan = OpportunityMatch::where('user_id', $pengguna->id)
            ->where('opportunity_id', $peluang->id)
            ->first();

        if ($tersimpan) {
            $ai->catatDariCache('skor_kecocokan', config('services.gemini.ringan'), $pengguna->id);

            return back()->with('sukses', 'Skor kecocokan diambil dari hasil yang tersimpan.');
        }

        try {
            $hasil = $ai->hitungKecocokan(
                profil: [
                    'jurusan' => $profil->jurusan,
                    'semester' => $profil->semester,
                    'universitas' => $profil->universitas,
                    'minat' => $profil->namaMinat(),
                    'kemampuan' => $profil->namaSkill(),
                    'preferensi' => $profil->preferensi ?? [],
                ],
                syarat: $peluang->syarat ?? [],
                userId: $pengguna->id,
            );
        } catch (Throwable $e) {
            Log::warning('Gagal menghitung kecocokan', [
                'peluang' => $peluang->id,
                'pesan' => $e->getMessage(),
            ]);

            return back()->with('gagal', $e->getMessage());
        }

        OpportunityMatch::create([
            'user_id' => $pengguna->id,
            'opportunity_id' => $peluang->id,
            'skor' => $hasil['skor'],
            'terpenuhi' => $hasil['terpenuhi'] ?? [],
            'belum_terpenuhi' => $hasil['belum_terpenuhi'] ?? [],
            'saran' => $hasil['saran'] ?? null,
            'dihitung_pada' => now(),
        ]);

        return back()->with('sukses', 'Skor kecocokan berhasil dihitung.');
    }
}
