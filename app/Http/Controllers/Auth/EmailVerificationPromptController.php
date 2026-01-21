<?php

/**
 * ==========================================================
 * JUDUL  : EmailVerificationPromptController
 * LOKASI : app/Http/Controllers/Auth/EmailVerificationPromptController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini bertugas menampilkan halaman prompt
 * verifikasi email kepada user yang belum melakukan
 * verifikasi email.
 *
 * TUJUAN:
 * - Mengingatkan user untuk memverifikasi email
 * - Mencegah akses ke fitur tertentu sebelum email valid
 * - Mengarahkan user yang sudah verifikasi ke dashboard
 *
 * CATATAN PENTING:
 * - Controller ini menggunakan method __invoke()
 * - Artinya controller ini hanya memiliki SATU aksi
 * - Biasanya dipanggil langsung lewat route tanpa method
 *
 * KAMUS:
 * - Email Verification : Proses memastikan email user valid
 * - __invoke()         : Method khusus agar controller
 *                        bisa dipanggil seperti fungsi
 * - RedirectResponse   : Respon pengalihan halaman
 * - View               : Tampilan halaman (Blade)
 */

namespace App\Http\Controllers\Auth;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Request standar Laravel
use Illuminate\Http\Request;

// Digunakan untuk tipe response redirect
use Illuminate\Http\RedirectResponse;

// Digunakan untuk tipe return View
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * ==================================================
     * [METHOD] __invoke(Request $request)
     * ==================================================
     * FUNGSI:
     * - Menentukan apakah user perlu melihat halaman
     *   verifikasi email atau tidak
     *
     * TUJUAN:
     * - Jika email sudah diverifikasi → lanjut ke dashboard
     * - Jika belum diverifikasi → tampilkan halaman verifikasi
     *
     * TIPE:
     * - AUTHENTICATION / VERIFICATION CHECK
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        /**
         * ----------------------------------------------
         * CEK STATUS VERIFIKASI EMAIL USER
         * ----------------------------------------------
         * hasVerifiedEmail() akan mengembalikan:
         * - true  → email sudah diverifikasi
         * - false → email belum diverifikasi
         */
        return $request->user()->hasVerifiedEmail()

            /**
             * ------------------------------------------
             * JIKA SUDAH DIVERIFIKASI
             * ------------------------------------------
             * User diarahkan ke halaman dashboard
             */
            ? redirect()->intended(
                route('dashboard', absolute: false)
            )

            /**
             * ------------------------------------------
             * JIKA BELUM DIVERIFIKASI
             * ------------------------------------------
             * Tampilkan halaman verifikasi email
             */
            : view('auth.verify-email');
    }
}
