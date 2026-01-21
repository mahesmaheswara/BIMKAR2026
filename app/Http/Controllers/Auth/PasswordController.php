<?php

/**
 * ==========================================================
 * JUDUL  : PasswordController (Update Password User)
 * LOKASI : app/Http/Controllers/Auth/PasswordController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini menangani proses perubahan password
 * untuk user yang SUDAH login.
 *
 * TUJUAN:
 * - Memungkinkan user mengganti password lama
 * - Menjaga keamanan akun dengan validasi password lama
 * - Memastikan password baru memenuhi standar keamanan
 *
 * PERBEDAAN DENGAN RESET PASSWORD:
 * - Reset password → user LUPA password (pakai email & token)
 * - Update password → user MASIH login (pakai password lama)
 *
 * KAMUS:
 * - current_password : Password lama user
 * - Password::defaults() : Aturan keamanan password Laravel
 * - validateWithBag() : Validasi dengan error bag khusus
 * - Hash::make() : Enkripsi password sebelum disimpan
 */

namespace App\Http\Controllers\Auth;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Digunakan untuk tipe response redirect
use Illuminate\Http\RedirectResponse;

// Request standar Laravel
use Illuminate\Http\Request;

// Digunakan untuk hashing password
use Illuminate\Support\Facades\Hash;

// Digunakan untuk aturan validasi password
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * ==================================================
     * [METHOD] update(Request $request)
     * ==================================================
     * FUNGSI:
     * - Memperbarui password user yang sedang login
     *
     * TUJUAN:
     * - User dapat mengganti password lama dengan yang baru
     * - Sistem memastikan password lama benar
     *
     * TIPE:
     * - UPDATE (U dalam konteks data user)
     */
    public function update(Request $request): RedirectResponse
    {
        /**
         * ----------------------------------------------
         * VALIDASI INPUT PASSWORD
         * ----------------------------------------------
         * validateWithBag() digunakan agar error validasi
         * disimpan di error bag khusus bernama:
         * "updatePassword"
         *
         * Ini berguna jika dalam satu halaman terdapat
         * beberapa form (misalnya: update profil & password)
         */
        $validated = $request->validateWithBag(
            'updatePassword',
            [
                // Password lama wajib diisi dan harus benar
                'current_password' => [
                    'required',
                    'current_password'
                ],

                // Password baru wajib diisi, aman, dan dikonfirmasi
                'password' => [
                    'required',
                    Password::defaults(),
                    'confirmed'
                ],
            ]
        );

        /**
         * ----------------------------------------------
         * UPDATE PASSWORD USER
         * ----------------------------------------------
         * Password baru akan di-hash sebelum disimpan
         * ke database demi keamanan
         */
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        /**
         * ----------------------------------------------
         * KEMBALI KE HALAMAN SEBELUMNYA
         * ----------------------------------------------
         * Menyertakan status agar bisa ditampilkan
         * sebagai notifikasi sukses
         */
        return back()->with(
            'status',
            'password-updated'
        );
    }
}
