<?php

/**
 * ==========================================================
 * JUDUL  : NewPasswordController (Reset Password Baru)
 * LOKASI : app/Http/Controllers/Auth/NewPasswordController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini menangani proses pembuatan password baru
 * setelah user melakukan permintaan reset password.
 *
 * TUJUAN:
 * - Memungkinkan user mengganti password yang lupa
 * - Mengamankan proses reset password menggunakan token
 * - Memastikan password baru memenuhi standar keamanan
 *
 * ALUR UMUM:
 * 1. User klik link reset password dari email
 * 2. Sistem menampilkan form reset password
 * 3. User memasukkan password baru
 * 4. Sistem menyimpan password baru ke database
 *
 * KAMUS:
 * - Password Reset : Proses mengganti password akun
 * - Token          : Kode unik untuk validasi reset password
 * - Hash           : Proses enkripsi password
 * - Event          : Notifikasi internal Laravel
 */

namespace App\Http\Controllers\Auth;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Model User (tabel users)
use App\Models\User;

// Event yang dipicu setelah password berhasil direset
use Illuminate\Auth\Events\PasswordReset;

// Digunakan untuk redirect response
use Illuminate\Http\RedirectResponse;

// Request standar Laravel
use Illuminate\Http\Request;

// Digunakan untuk hashing password
use Illuminate\Support\Facades\Hash;

// Facade Password untuk proses reset password Laravel
use Illuminate\Support\Facades\Password;

// Digunakan untuk generate token acak
use Illuminate\Support\Str;

// Digunakan untuk aturan validasi password
use Illuminate\Validation\Rules;

// Digunakan untuk tipe return View
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * ==================================================
     * [METHOD] create(Request $request)
     * ==================================================
     * FUNGSI:
     * - Menampilkan halaman form reset password
     *
     * TUJUAN:
     * - User dapat memasukkan password baru
     * - Token reset password ikut dikirim ke view
     *
     * TIPE:
     * - READ (menampilkan halaman)
     */
    public function create(Request $request): View
    {
        /**
         * Menampilkan view:
         * resources/views/auth/reset-password.blade.php
         *
         * Request dikirim agar token & email
         * bisa diakses di view
         */
        return view('auth.reset-password', [
            'request' => $request
        ]);
    }

    /**
     * ==================================================
     * [METHOD] store(Request $request)
     * ==================================================
     * FUNGSI:
     * - Menyimpan password baru user
     *
     * TUJUAN:
     * - Mengganti password lama dengan password baru
     * - Memvalidasi token reset password
     *
     * TIPE:
     * - PASSWORD RESET PROCESS
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        /**
         * ----------------------------------------------
         * VALIDASI INPUT RESET PASSWORD
         * ----------------------------------------------
         * - token wajib ada
         * - email harus valid
         * - password harus dikonfirmasi dan aman
         */
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults()
            ],
        ]);

        /**
         * ----------------------------------------------
         * PROSES RESET PASSWORD
         * ----------------------------------------------
         * Password::reset akan:
         * - Memvalidasi token
         * - Mengecek email user
         * - Menjalankan callback jika berhasil
         */
        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),

            /**
             * ------------------------------------------
             * CALLBACK JIKA RESET BERHASIL
             * ------------------------------------------
             * Password baru akan disimpan ke database
             */
            function (User $user) use ($request) {

                /**
                 * Mengisi password baru dengan hash
                 * dan mengganti remember_token
                 */
                $user->forceFill([
                    'password'       => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                /**
                 * Memicu event PasswordReset
                 * (digunakan oleh Laravel untuk logging / listener)
                 */
                event(new PasswordReset($user));
            }
        );

        /**
         * ----------------------------------------------
         * HANDLE HASIL RESET PASSWORD
         * ----------------------------------------------
         * Jika berhasil:
         * - Redirect ke halaman login
         *
         * Jika gagal:
         * - Kembali ke form dengan pesan error
         */
        return $status == Password::PASSWORD_RESET
            ? redirect()
                ->route('login')
                ->with('status', __($status))
            : back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => __($status)
                ]);
    }
}
