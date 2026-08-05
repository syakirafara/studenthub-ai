<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman pendaftaran.
     */
    public function formDaftar(): View
    {
        return view('auth.daftar');
    }

    /**
     * Memproses pendaftaran mahasiswa baru.
     */
    public function daftar(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'    => ['required', 'confirmed', Password::min(8)],
            'universitas' => ['required', 'string', 'max:255'],
            'jurusan'     => ['required', 'string', 'max:255'],
            'semester'    => ['required', 'integer', 'between:1,14'],
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],
                'role'     => 'mahasiswa',
            ]);

            $user->profile()->create([
                'universitas' => $data['universitas'],
                'jurusan'     => $data['jurusan'],
                'semester'    => $data['semester'],
                'minat'       => [],
                'skill'       => [],
                'preferensi'  => [],
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('beranda')
            ->with('sukses', "Akun berhasil dibuat. Selamat datang, {$user->name}!");
    }

    /**
     * Menampilkan halaman masuk.
     */
    public function formMasuk(): View
    {
        return view('auth.masuk');
    }

    /**
     * Memproses percobaan masuk.
     */
    public function masuk(Request $request): RedirectResponse
    {
        $kredensial = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $kunci = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($kunci, 5)) {
            $detik = RateLimiter::availableIn($kunci);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan masuk. Coba lagi dalam {$detik} detik.",
            ]);
        }

        if (! Auth::attempt($kredensial, $request->boolean('ingat'))) {
            RateLimiter::hit($kunci, 60);

            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi salah.',
            ]);
        }

        RateLimiter::clear($kunci);
        $request->session()->regenerate();

        $tujuan = Auth::user()->isAdmin() ? 'admin.dasbor' : 'beranda';

        return redirect()->intended(route($tujuan))
            ->with('sukses', 'Berhasil masuk. Halo, '.Auth::user()->name.'!');
    }

    /**
     * Mengeluarkan pengguna dan membersihkan sesi.
     */
    public function keluar(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('masuk')->with('sukses', 'Kamu sudah keluar.');
    }
}
