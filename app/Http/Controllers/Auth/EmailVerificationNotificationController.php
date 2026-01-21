<?php

/**
 * ==========================================================
 * JUDUL  : EmailVerificationNotificationController
 * LOKASI : app/Http/Controllers/Auth/EmailVerificationNotificationController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini menangani pengiriman ulang email
 * verifikasi akun kepada user.
 *
 * TUJUAN:
 * - Memastikan user melakukan verifikasi email
 * - Mengizinkan user meminta ulang email verifikasi
 * - Mendukung keamanan & validitas akun
 *
 * KAMUS:
 * - Email Verification : Proses memastikan email user valid
 * - RedirectResponse   : Respon pengalihan halaman
 * - Session Status     : Pesan status sementara (flash)
 */

namespace App\Http\Controllers\Auth;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Digunakan untuk menangani request HTTP
use Illuminate\Http\Request;

// Digunakan untuk tipe response redirect
use Illuminate\Http\RedirectResponse;

class EmailVerificationNotificationController extends Controller
{
    /**
     * ==================================================
     * [METHOD] store(Request $request)
     * ==================================================
     * FUNGSI:
     * - Mengirim ulang email verifikasi ke user
     *
     * TUJUAN:
     * - Digunakan saat user menekan tombol
     *   "Kirim ulang email verifikasi"
     *
     * TIPE:
     * - AUTHENTICATION / VERIFICATION PROCESS
     */
    public function store(Request $request): RedirectResponse
    {
        /**
         * ----------------------------------------------
         * CEK STATUS VERIFIKASI EMAIL
         * ----------------------------------------------
         * Jika email user sudah diverifikasi,
         * tidak perlu mengirim ulang email
         */
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                route('dashboard', absolute: false)
            );
        }

        /**
         * ----------------------------------------------
         * KIRIM EMAIL VERIFIKASI
         * ----------------------------------------------
         * Laravel akan mengirim email berisi link
         * untuk verifikasi akun
         */
        $request->user()->sendEmailVerificationNotification();

        /**
         * ----------------------------------------------
         * KEMBALI KE HALAMAN SEBELUMNYA
         * ----------------------------------------------
         * Menyertakan status agar bisa ditampilkan
         * sebagai notifikasi di UI
         */
        return back()->with(
            'status',
            'verification-link-sent'
        );
    }
}
