<?php

/**
 * ==========================================================
 * JUDUL  : RegisteredUserController (Registrasi User)
 * LOKASI : app/Http/Controllers/Auth/RegisteredUserController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini menangani proses pendaftaran (registrasi)
 * user baru ke dalam sistem ticketing.
 *
 * TUJUAN:
 * - Memungkinkan user membuat akun baru
 * - Menyimpan data user ke database
 * - Memberikan role default sebagai "user"
 * - Melakukan login otomatis setelah registrasi
 *
 * ALUR REGISTRASI:
 * 1. User membuka halaman register
 * 2. User mengisi form (nama, email, password)
 * 3. Sistem memvalidasi data
 * 4. Data user disimpan ke database
 * 5. Event "Registered" dipicu
 * 6. User langsung login
 * 7. User diarahkan ke halaman home
 *
 * KAMUS UMUM:
 * - Validation        : Proses pengecekan input user
 * - Hash::make()      : Enkripsi password
 * - Auth::login()     : Login user secara otomatis
 * - Event Registered  : Event bawaan Laravel setelah registrasi
 * - Role              : Hak akses user (admin / user)
 */

namespace App\Http\Controllers\Auth;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Model User (tabel users)
use App\Models\User;

// Event yang dipicu setelah user berhasil registrasi
use Illuminate\Auth\Events\Registered;

// Digunakan untuk redirect response
use Illuminate\Http\RedirectResponse;

// Request standar Laravel
use Illuminate\Http\Request;

// Facade Auth untuk login user
use Illuminate\Support\Facades\Auth;

// Digunakan untuk hashing password
use Illuminate\Support\Facades\Hash;

// Digunakan untuk aturan validasi password
use Illuminate\Validation\Rules;

// Digunakan untuk tipe return View
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * ==================================================
     * [METHOD] create()
     * ==================================================
     * FUNGSI:
     * - Menampilkan halaman registrasi user
     *
     * TUJUAN:
     * - User dapat mengisi form pendaftaran akun
     *
     * TIPE:
     * - READ (menampilkan halaman)
     */
    public function create(): View
    {
        /**
         * Menampilkan view:
         * resources/views/auth/register.blade.php
         */
        return view('auth.register');
    }

    /**
     * ==================================================
     * [METHOD] store(Request $request)
     * ==================================================
     * FUNGSI:
     * - Menyimpan data user baru ke database
     *
     * TUJUAN:
     * - Membuat akun user baru
     * - Mengamankan data user
     * - Login otomatis setelah registrasi
     *
     * TIPE:
     * - USER REGISTRATION PROCESS
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        /**
         * ----------------------------------------------
         * VALIDASI INPUT REGISTRASI
         * ----------------------------------------------
         * - name  : wajib, string, max 255
         * - email : wajib, format email, unik
         * - password :
         *     - wajib
         *     - harus dikonfirmasi
         *     - mengikuti standar keamanan Laravel
         */
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults()
            ],
        ]);

        /**
         * ----------------------------------------------
         * SIMPAN DATA USER KE DATABASE
         * ----------------------------------------------
         * - Password di-hash untuk keamanan
         * - Role default diset sebagai "user"
         */
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => 'user',
            'password' => Hash::make($request->password),
        ]);

        /**
         * ----------------------------------------------
         * TRIGGER EVENT REGISTERED
         * ----------------------------------------------
         * Event ini digunakan Laravel untuk:
         * - Mengirim email verifikasi (jika diaktifkan)
         * - Logging atau listener lain
         */
        event(new Registered($user));

        /**
         * ----------------------------------------------
         * LOGIN OTOMATIS USER
         * ----------------------------------------------
         * Setelah registrasi berhasil, user langsung
         * login tanpa perlu login ulang
         */
        Auth::login($user);

        /**
         * ----------------------------------------------
         * REDIRECT KE HALAMAN HOME
         * ----------------------------------------------
         * absolute: false → menggunakan relative URL
         */
        return redirect(
            route('home', absolute: false)
        );
    }
}
