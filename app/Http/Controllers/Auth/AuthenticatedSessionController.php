<?php

/**
 * ==========================================================
 * JUDUL  : AuthenticatedSessionController (Login & Logout)
 * LOKASI : app/Http/Controllers/Auth/AuthenticatedSessionController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini menangani proses autentikasi user,
 * yaitu login dan logout pada sistem ticketing.
 *
 * TUJUAN:
 * - Menampilkan halaman login
 * - Memproses login user
 * - Mengatur sesi autentikasi
 * - Mengarahkan user sesuai dengan role (admin / user)
 *
 * KAMUS:
 * - Authentication : Proses verifikasi identitas user
 * - Session        : Penyimpanan data login sementara
 * - Guard          : Mekanisme autentikasi Laravel
 * - Role           : Hak akses user (admin atau user)
 */

namespace App\Http\Controllers\Auth;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Request khusus login (berisi validasi & autentikasi)
use App\Http\Requests\Auth\LoginRequest;

// Digunakan untuk redirect setelah login / logout
use Illuminate\Http\RedirectResponse;

// Request standar Laravel
use Illuminate\Http\Request;

// Facade Auth untuk autentikasi user
use Illuminate\Support\Facades\Auth;

// Digunakan untuk tipe return View
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * ==================================================
     * [METHOD] create()
     * ==================================================
     * FUNGSI:
     * - Menampilkan halaman login
     *
     * TUJUAN:
     * - User dapat memasukkan email dan password
     *
     * TIPE:
     * - READ (hanya menampilkan halaman)
     */
    public function create(): View
    {
        // Menampilkan view login
        // resources/views/auth/login.blade.php
        return view('auth.login');
    }

    /**
     * ==================================================
     * [METHOD] store(LoginRequest $request)
     * ==================================================
     * FUNGSI:
     * - Memproses permintaan login user
     *
     * TUJUAN:
     * - Melakukan autentikasi user
     * - Membuat sesi login
     * - Mengarahkan user sesuai role
     *
     * TIPE:
     * - AUTHENTICATION PROCESS
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        /**
         * ----------------------------------------------
         * PROSES AUTENTIKASI USER
         * ----------------------------------------------
         * Method authenticate() akan:
         * - Mengecek email & password
         * - Gagal → kembali ke halaman login
         * - Berhasil → user dianggap login
         */
        $request->authenticate();

        /**
         * ----------------------------------------------
         * REGENERATE SESSION
         * ----------------------------------------------
         * Mencegah serangan session fixation
         * (keamanan setelah login)
         */
        $request->session()->regenerate();

        /**
         * ----------------------------------------------
         * CEK ROLE USER
         * ----------------------------------------------
         * Digunakan untuk membedakan arah login:
         * - Admin → Dashboard Admin
         * - User  → Homepage
         */
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Jika user adalah admin, arahkan ke dashboard admin
            return redirect()->intended('/admin');
        } else {
            // Jika user adalah user biasa, arahkan ke homepage
            return redirect()->intended('/');
        }
    }

    /**
     * ==================================================
     * [METHOD] destroy(Request $request)
     * ==================================================
     * FUNGSI:
     * - Logout user dari sistem
     *
     * TUJUAN:
     * - Mengakhiri sesi login user
     * - Menghapus data autentikasi
     *
     * TIPE:
     * - LOGOUT PROCESS
     */
    public function destroy(Request $request): RedirectResponse
    {
        /**
         * ----------------------------------------------
         * LOGOUT USER
         * ----------------------------------------------
         * Menghapus autentikasi user dari guard web
         */
        Auth::guard('web')->logout();

        /**
         * ----------------------------------------------
         * INVALIDATE SESSION
         * ----------------------------------------------
         * Menghapus seluruh data sesi yang tersimpan
         */
        $request->session()->invalidate();

        /**
         * ----------------------------------------------
         * REGENERATE CSRF TOKEN
         * ----------------------------------------------
         * Mencegah penyalahgunaan token lama
         */
        $request->session()->regenerateToken();

        /**
         * ----------------------------------------------
         * REDIRECT KE HALAMAN AWAL
         * ----------------------------------------------
         */
        return redirect('/');
    }
}
