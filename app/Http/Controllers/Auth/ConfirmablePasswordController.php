<?php

/**
 * ==========================================================
 * JUDUL  : ConfirmablePasswordController (Konfirmasi Password)
 * LOKASI : app/Http/Controllers/Auth/ConfirmablePasswordController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini menangani proses konfirmasi ulang password
 * user untuk aksi-aksi sensitif dalam sistem.
 *
 * TUJUAN:
 * - Memastikan bahwa user yang sedang login benar-benar
 *   pemilik akun saat melakukan aksi penting
 * - Meningkatkan keamanan aplikasi
 *
 * CONTOH PENGGUNAAN:
 * - Akses halaman profil
 * - Mengubah data penting
 * - Mengakses halaman tertentu yang dilindungi
 *
 * KAMUS:
 * - Authentication        : Proses verifikasi identitas user
 * - Password Confirmation: Validasi ulang password
 * - Guard                 : Sistem autentikasi Laravel
 * - Session               : Penyimpanan data sementara user
 * - ValidationException   : Error validasi dari Laravel
 */

namespace App\Http\Controllers\Auth;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Digunakan untuk redirect response
use Illuminate\Http\RedirectResponse;

// Request standar Laravel
use Illuminate\Http\Request;

// Facade Auth untuk autentikasi
use Illuminate\Support\Facades\Auth;

// Digunakan untuk melempar error validasi
use Illuminate\Validation\ValidationException;

// Digunakan untuk return tipe View
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    /**
     * ==================================================
     * [METHOD] show()
     * ==================================================
     * FUNGSI:
     * - Menampilkan halaman konfirmasi password
     *
     * TUJUAN:
     * - User diminta memasukkan ulang password
     *   sebelum melanjutkan ke halaman sensitif
     *
     * TIPE:
     * - READ (hanya menampilkan halaman)
     */
    public function show(): View
    {
        /**
         * Menampilkan view:
         * resources/views/auth/confirm-password.blade.php
         */
        return view('auth.confirm-password');
    }

    /**
     * ==================================================
     * [METHOD] store(Request $request)
     * ==================================================
     * FUNGSI:
     * - Memvalidasi password user
     *
     * TUJUAN:
     * - Memastikan password yang dimasukkan benar
     * - Jika benar, user diizinkan melanjutkan
     *
     * TIPE:
     * - SECURITY VALIDATION PROCESS
     */
    public function store(Request $request): RedirectResponse
    {
        /**
         * ----------------------------------------------
         * VALIDASI PASSWORD USER
         * ----------------------------------------------
         * Mengecek apakah password yang dimasukkan
         * sesuai dengan email user yang sedang login
         */
        if (! Auth::guard('web')->validate([
            'email'    => $request->user()->email,
            'password' => $request->password,
        ])) {

            /**
             * ------------------------------------------
             * JIKA PASSWORD SALAH
             * ------------------------------------------
             * Lempar error validasi dan tampilkan
             * pesan error pada form
             */
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        /**
         * ----------------------------------------------
         * SIMPAN STATUS KONFIRMASI DI SESSION
         * ----------------------------------------------
         * Digunakan sebagai penanda bahwa user telah
         * mengonfirmasi password dalam waktu tertentu
         */
        $request->session()->put(
            'auth.password_confirmed_at',
            time()
        );

        /**
         * ----------------------------------------------
         * REDIRECT KE HALAMAN TUJUAN
         * ----------------------------------------------
         * User diarahkan kembali ke halaman yang
         * sebelumnya ingin diakses
         */
        return redirect()->intended(
            route('dashboard', absolute: false)
        );
    }
}
