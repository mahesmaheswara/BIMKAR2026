<?php

/**
 * ==========================================================
 * JUDUL  : AdminMiddleware (Pembatas Akses Admin)
 * LOKASI : app/Http/Middleware/AdminMiddleware.php
 * ==========================================================
 *
 * FUNGSI:
 * Middleware ini digunakan untuk membatasi akses
 * ke halaman tertentu agar hanya bisa diakses
 * oleh user dengan role "admin".
 *
 * TUJUAN:
 * - Melindungi halaman admin dari akses user biasa
 * - Menjaga keamanan sistem
 * - Mengontrol hak akses berdasarkan role
 *
 * CARA KERJA MIDDLEWARE:
 * 1. Request masuk ke aplikasi
 * 2. Middleware dijalankan sebelum controller
 * 3. Role user dicek
 * 4. Jika admin → lanjut ke controller
 * 5. Jika bukan admin → ditolak
 *
 * KAMUS UMUM:
 * - Middleware : Filter request sebelum ke controller
 * - Auth::check() : Mengecek apakah user sudah login
 * - Role       : Hak akses user
 */

namespace App\Http\Middleware;

// Closure digunakan untuk melanjutkan request
use Closure;

// Request HTTP
use Illuminate\Http\Request;

// Tipe response
use Symfony\Component\HttpFoundation\Response;

// Facade Auth untuk autentikasi
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * ==================================================
     * [METHOD] handle(Request $request, Closure $next)
     * ==================================================
     * FUNGSI:
     * - Menyaring request yang masuk ke route admin
     *
     * TUJUAN:
     * - Memastikan hanya admin yang bisa mengakses
     *
     * TIPE:
     * - AUTHORIZATION PROCESS
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * ----------------------------------------------
         * CEK STATUS LOGIN & ROLE USER
         * ----------------------------------------------
         * Auth::check():
         * - Memastikan user sudah login
         *
         * Auth::user()->role === 'admin':
         * - Memastikan user memiliki role admin
         */
        if (
            Auth::check()
            && Auth::user()->role === 'admin'
        ) {
            /**
             * ------------------------------------------
             * JIKA ADMIN
             * ------------------------------------------
             * $next($request):
             * - Melanjutkan request ke controller
             */
            return $next($request);
        }

        /**
         * ----------------------------------------------
         * JIKA BUKAN ADMIN
         * ----------------------------------------------
         * Logout user untuk keamanan
         * dan mencegah akses tidak sah
         */
        Auth::logout();

        /**
         * ----------------------------------------------
         * REDIRECT KE HALAMAN LOGIN
         * ----------------------------------------------
         */
        return redirect('/login');
    }
}
