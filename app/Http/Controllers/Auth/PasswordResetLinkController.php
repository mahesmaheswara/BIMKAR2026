<?php

/**
 * ==========================================================
 * JUDUL  : PasswordResetLinkController (Permintaan Reset Password)
 * LOKASI : app/Http/Controllers/Auth/PasswordResetLinkController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini menangani proses permintaan reset password,
 * yaitu saat user memasukkan email untuk mendapatkan
 * link reset password.
 *
 * TUJUAN:
 * - Memberikan user akses untuk mereset password yang lupa
 * - Mengirim email berisi link reset password
 * - Menjaga keamanan proses reset password
 *
 * ALUR:
 * 1. User membuka halaman "Lupa Password"
 * 2. User memasukkan email
 * 3. Sistem mengirim email reset password
 * 4. User klik link di email
 *
 * KAMUS UMUM:
 * - back()        : Kembali ke halaman sebelumnya
 * - status        : Pesan sukses sementara (flash session)
 * - withErrors()  : Mengirim pesan error ke view
 * - Password      : Fitur bawaan Laravel untuk reset password
 */

namespace App\Http\Controllers\Auth;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Digunakan untuk redirect response
use Illuminate\Http\RedirectResponse;

// Request standar Laravel
use Illuminate\Http\Request;

// Facade Password untuk reset password
use Illuminate\Support\Facades\Password;

// Digunakan untuk tipe return View
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * ==================================================
     * [METHOD] create()
     * ==================================================
     * FUNGSI:
     * - Menampilkan halaman permintaan reset password
     *
     * TUJUAN:
     * - User dapat memasukkan email untuk reset password
     *
     * TIPE:
     * - READ (menampilkan halaman)
     */
    public function create(): View
    {
        /**
         * Menampilkan view:
         * resources/views/auth/forgot-password.blade.php
         */
        return view('auth.forgot-password');
    }

    /**
     * ==================================================
     * [METHOD] store(Request $request)
     * ==================================================
     * FUNGSI:
     * - Memproses permintaan reset password
     *
     * TUJUAN:
     * - Mengirim email reset password ke user
     * - Menampilkan feedback berhasil / gagal
     *
     * TIPE:
     * - PASSWORD RESET REQUEST PROCESS
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        /**
         * ----------------------------------------------
         * VALIDASI EMAIL
         * ----------------------------------------------
         * - Email wajib diisi
         * - Email harus dalam format yang valid
         */
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        /**
         * ----------------------------------------------
         * KIRIM LINK RESET PASSWORD
         * ----------------------------------------------
         * Password::sendResetLink() akan:
         * - Mengecek apakah email terdaftar
         * - Jika terdaftar → kirim email reset password
         * - Jika tidak → kembalikan status error
         */
        $status = Password::sendResetLink(
            $request->only('email')
        );

        /**
         * ----------------------------------------------
         * HANDLE RESPONSE PENGIRIMAN EMAIL
         * ----------------------------------------------
         *
         * CASE 1:
         * - Email berhasil dikirim
         * - back() → kembali ke halaman sebelumnya
         * - with('status') → kirim pesan sukses
         *
         * CASE 2:
         * - Email gagal dikirim (email tidak terdaftar)
         * - withInput() → email tetap terisi di form
         * - withErrors() → tampilkan pesan error
         */
        return $status == Password::RESET_LINK_SENT

            // Jika berhasil kirim email
            ? back()->with(
                'status',
                __($status) // pesan sukses bawaan Laravel
            )

            // Jika gagal kirim email
            : back()
                ->withInput(
                    $request->only('email')
                )
                ->withErrors([
                    'email' => __($status)
                ]);
    }
}
