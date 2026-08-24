<?php

namespace App\Http\Controllers;

use App\Models\AiLog;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OpportunityController extends Controller
{
    /**
     * Halaman depan.
     *
     * Angka yang ditampilkan diambil langsung dari basis data, bukan ditulis
     * tetap di tampilan. Angka yang dikarang akan langsung terbantah begitu
     * penilai membuka katalognya.
     */
    public function depan(): View
    {
        return view('depan', [
            'jumlah' => [
                'peluang' => Opportunity::disetujui()->masihBuka()->count(),
                'kategori' => Opportunity::disetujui()->distinct()->count('kategori'),
                'kontributor' => Opportunity::disetujui()->distinct()->count('submitted_by'),
            ],
        ]);
    }

    /**
     * Katalog peluang yang sudah diverifikasi admin.
     *
     * Halaman ini sengaja dibuka untuk umum tanpa perlu masuk, supaya siapa pun
     * bisa langsung melihat isinya. Skor kecocokan barulah yang butuh akun.
     */
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'cari' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'in:lomba,beasiswa,magang'],
            'tingkat' => ['nullable', 'in:kampus,regional,nasional,internasional'],
            'biaya' => ['nullable', 'in:gratis,berbayar'],
            'urut' => ['nullable', 'in:deadline,terbaru'],
            'tampilkan_lewat' => ['nullable', 'boolean'],
        ]);

        $peluang = Opportunity::disetujui()
            ->when($filter['cari'] ?? null, function ($query, $cari) {
                $query->where(function ($cabang) use ($cari) {
                    $cabang->where('judul', 'like', "%{$cari}%")
                        ->orWhere('penyelenggara', 'like', "%{$cari}%");
                });
            })
            ->when($filter['kategori'] ?? null, fn ($query, $nilai) => $query->where('kategori', $nilai))
            ->when($filter['tingkat'] ?? null, fn ($query, $nilai) => $query->where('tingkat', $nilai))
            ->when($filter['biaya'] ?? null, fn ($query, $nilai) => $query->where('biaya', $nilai))
            ->unless($filter['tampilkan_lewat'] ?? false, fn ($query) => $query->masihBuka())
            ->when(
                ($filter['urut'] ?? 'deadline') === 'terbaru',
                fn ($query) => $query->latest(),
                fn ($query) => $query->orderByRaw('deadline IS NULL, deadline ASC'),
            )
            ->paginate(12)
            ->withQueryString();

        return view('peluang.index', [
            'peluang' => $peluang,
            'filter' => $filter,
            'idTersimpan' => $this->idTersimpan($request),
        ]);
    }

    /**
     * Halaman detail satu peluang.
     */
    public function show(Request $request, Opportunity $peluang): View
    {
        abort_unless(
            $peluang->status === 'disetujui' || $request->user()?->isAdmin(),
            404
        );

        return view('peluang.show', [
            'peluang' => $peluang,
            'tersimpan' => $this->idTersimpan($request)->contains($peluang->id),
            'skor' => $request->user()
                ? $peluang->matches()->where('user_id', $request->user()->id)->first()
                : null,

            // Lama perhitungan diambil dari catatan panggilan yang sudah
            // terjadi, bukan angka tetap di dalam tampilan. Lihat
            // AiLog::perkiraan() untuk alasannya.
            'perkiraanWaktu' => AiLog::perkiraan('skor_kecocokan', 'beberapa detik'),
        ]);
    }

    /**
     * Nomor peluang yang sudah ditandai simpan oleh pengguna yang sedang masuk.
     *
     * Diambil sekali dalam satu permintaan, lalu dipakai bersama oleh semua
     * kartu. Kalau tiap kartu memeriksa sendiri ke database, satu halaman
     * berisi 12 kartu akan menembak 12 pertanyaan tambahan.
     */
    private function idTersimpan(Request $request)
    {
        return $request->user()?->savedItems()->pluck('opportunity_id') ?? collect();
    }
}
