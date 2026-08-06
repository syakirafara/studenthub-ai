<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavedItemController extends Controller
{
    /**
     * Daftar peluang yang ditandai simpan.
     */
    public function index(Request $request): View
    {
        $peluang = $request->user()
            ->peluangTersimpan()
            ->orderByRaw('deadline IS NULL, deadline ASC')
            ->paginate(12);

        return view('tersimpan.index', [
            'peluang' => $peluang,
            'idTersimpan' => $peluang->pluck('id'),
        ]);
    }

    /**
     * Menandai satu peluang untuk disimpan.
     */
    public function store(Request $request, Opportunity $peluang): RedirectResponse
    {
        abort_unless($peluang->status === 'disetujui', 404);

        // firstOrCreate, bukan create. Kalau tombolnya terklik dua kali karena
        // jaringan lambat, barisnya tidak jadi ganda. Batasan unique di
        // database menjadi lapis pengaman kedua.
        $request->user()->savedItems()->firstOrCreate([
            'opportunity_id' => $peluang->id,
        ]);

        return back()->with('sukses', 'Peluang disimpan.');
    }

    /**
     * Menghapus satu peluang dari simpanan.
     */
    public function destroy(Request $request, Opportunity $peluang): RedirectResponse
    {
        // Dihapus lewat relasi milik pengguna yang sedang masuk, bukan lewat
        // SavedItem::where(...). Dengan begini seseorang tidak mungkin
        // menghapus simpanan milik orang lain walaupun menebak nomornya.
        $request->user()->savedItems()
            ->where('opportunity_id', $peluang->id)
            ->delete();

        return back()->with('sukses', 'Peluang dihapus dari simpanan.');
    }
}
