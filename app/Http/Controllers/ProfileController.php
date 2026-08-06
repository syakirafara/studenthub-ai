<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Halaman ubah profil akademik.
     */
    public function edit(Request $request): View
    {
        return view('profil.edit', [
            'profil' => $request->user()->profile,
        ]);
    }

    /**
     * Menyimpan perubahan profil.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'universitas' => ['required', 'string', 'max:255'],
            'jurusan' => ['required', 'string', 'max:255'],
            'semester' => ['required', 'integer', 'between:1,14'],

            'minat' => ['nullable', 'array', 'max:3'],
            'minat.*' => [Rule::in(array_keys(Profile::MINAT))],

            'skill' => ['nullable', 'array', 'max:8'],
            'skill.*' => [Rule::in(array_keys(Profile::SKILL))],

            'preferensi.format' => ['nullable', Rule::in(['online', 'offline', 'keduanya'])],
            'preferensi.biaya' => ['nullable', Rule::in(['gratis', 'keduanya'])],
        ], [
            'minat.max' => 'Pilih paling banyak 3 minat supaya rekomendasinya tetap fokus.',
        ]);

        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'universitas' => $data['universitas'],
                'jurusan' => $data['jurusan'],
                'semester' => $data['semester'],
                'minat' => $data['minat'] ?? [],
                'skill' => $data['skill'] ?? [],
                'preferensi' => $data['preferensi'] ?? [],
            ]
        );

        return redirect()
            ->route('profil.edit')
            ->with('sukses', 'Profil berhasil diperbarui.');
    }
}
