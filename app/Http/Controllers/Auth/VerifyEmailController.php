<?php

/**
 * ==========================================================
 * JUDUL  : VerifyEmailController (Verifikasi Email User)
 * LOKASI : app/Http/Controllers/Auth/VerifyEmailController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini menangani proses verifikasi email user
 * ketika user mengklik link verifikasi dari email.
 *
 * TUJUAN:
 * - Menandai email user sebagai "terverifikasi"
 * - Mengamankan akun user
 * - Mengizinkan akses penuh ke fitur aplikasi
 *
 * ALUR VERIFIKASI EMAIL:
 * 1. User registrasi akun
 * 2. Sistem mengirim email verifikasi
 * 3. User klik link verifikasi
 * 4. Sistem menandai email sebagai terverifikasi
 * 5. User diarahkan ke dashboard
 *
 * CATATAN PENTING:
 * - Controller ini menggunakan method __invoke()
 * - Dipanggil langsung melalui route
 * - Menggunakan EmailVerificationRequest (request khusus Laravel)
 *
 * KAMUS UMUM:
 * - EmailVerificationRequest : Request khusus untuk verifikasi email
 * - Verified                 : Event setelah email diverifikasi
 * - hasVerifiedEmail()       : Cek status verifikasi email
 * - markEmailAsVerified()    : Menandai email sebagai terverifikasi
 */

namespace App\Http\Controllers\Auth;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Event yang dipicu setelah email berhasil diverifikasi
use Illuminate\Auth\Events\Verified;

// Request khusus Laravel untuk verifikasi email
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Digunakan untuk tipe response redirect
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * ==================================================
     * [METHOD] __invoke(EmailVerificationRequest $request)
     * ==================================================
     * FUNGSI:
     * - Memproses verifikasi email user
     *
     * TUJUAN:
     * - Menandai email user sebagai terverifikasi
     * - Mencegah verifikasi ulang yang tidak perlu
     *
     * TIPE:
     * - EMAIL VERIFICATION PROCESS
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        /**
         * ----------------------------------------------
         * CEK STATUS VERIFIKASI EMAIL
         * ----------------------------------------------
         * Jika email user sudah diverifikasi sebelumnya,
         * langsung arahkan ke dashboard
         */
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                route('dashboard', absolute: false) . '?verified=1'
            );
        }

        /**
         * ----------------------------------------------
         * TANDAI EMAIL SEBAGAI TERVERIFIKASI
         * ----------------------------------------------
         * markEmailAsVerified() akan:
         * - Mengubah kolom email_verified_at
         * - Mengembalikan true jika berhasil
         */
        if ($request->user()->markEmailAsVerified()) {

            /**
             * ------------------------------------------
             * TRIGGER EVENT VERIFIED
             * ------------------------------------------
             * Digunakan Laravel untuk:
             * - Listener tambahan
             * - Logging
             * - Notifikasi
             */
            event(
                new Verified(
                    $request->user()
                )
            );
        }

        /**
         * ----------------------------------------------
         * REDIRECT KE DASHBOARD
         * ----------------------------------------------
         * Parameter ?verified=1 bisa digunakan
         * untuk menampilkan notifikasi sukses
         */
        return redirect()->intended(
            route('dashboard', absolute: false) . '?verified=1'
        );
    }
}
