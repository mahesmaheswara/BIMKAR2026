<?php

/**
 * ==========================================================
 * JUDUL  : ProfileController (Manajemen Profil User)
 * LOKASI : app/Http/Controllers/ProfileController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini menangani pengelolaan profil user,
 * meliputi:
 * - Menampilkan halaman edit profil
 * - Memperbarui data profil user
 * - Menghapus akun user
 *
 * TUJUAN:
 * - Memberikan user kontrol atas data pribadinya
 * - Menjaga keamanan perubahan data penting
 * - Mengelola lifecycle akun user
 *
 * CATATAN PENTING:
 * - Menggunakan Form Request (ProfileUpdateRequest)
 * - Menggunakan error bag khusus untuk hapus akun
 * - Menangani logout & session dengan aman
 *
 * KAMUS UMUM:
 * - ProfileUpdateRequest : Request khusus berisi validasi profil
 * - isDirty()            : Mengecek apakah field berubah
 * - Redirect             : Helper pengalihan halaman
 * - Error Bag            : Kumpulan error terpisah untuk form tertentu
 */

namespace App\Http\Controllers;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Form Request khusus untuk update profil
use App\Http\Requests\ProfileUpdateRequest;

// Digunakan untuk tipe response redirect
use Illuminate\Http\RedirectResponse;

// Request standar Laravel
use Illuminate\Http\Request;

// Facade Auth untuk autentikasi
use Illuminate\Support\Facades\Auth;

// Facade Redirect untuk pengalihan halaman
use Illuminate\Support\Facades\Redirect;

// Digunakan untuk tipe return View
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * ==================================================
     * [METHOD] edit(Request $request)
     * ==================================================
     * FUNGSI:
     * - Menampilkan halaman edit profil user
     *
     * TUJUAN:
     * - User dapat melihat dan mengubah data profil
     *
     * TIPE:
     * - READ (menampilkan data)
     */
    public function edit(Request $request): View
    {
        /**
         * ----------------------------------------------
         * KIRIM DATA USER KE VIEW
         * ----------------------------------------------
         * $request->user():
         * - Mengambil user yang sedang login
         */
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * ==================================================
     * [METHOD] update(ProfileUpdateRequest $request)
     * ==================================================
     * FUNGSI:
     * - Memperbarui data profil user
     *
     * TUJUAN:
     * - Menyimpan perubahan nama / email user
     * - Mengatur ulang status verifikasi email jika perlu
     *
     * TIPE:
     * - UPDATE (U dalam CRUD)
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        /**
         * ----------------------------------------------
         * ISI DATA USER DENGAN DATA TERVALIDASI
         * ----------------------------------------------
         * fill():
         * - Mengisi atribut user tanpa langsung menyimpan
         * validated():
         * - Data sudah dipastikan valid
         */
        $request->user()->fill(
            $request->validated()
        );

        /**
         * ----------------------------------------------
         * CEK PERUBAHAN EMAIL
         * ----------------------------------------------
         * isDirty('email'):
         * - Mengecek apakah field email berubah
         *
         * Jika berubah:
         * - Status verifikasi email di-reset
         */
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        /**
         * ----------------------------------------------
         * SIMPAN PERUBAHAN KE DATABASE
         * ----------------------------------------------
         */
        $request->user()->save();

        /**
         * ----------------------------------------------
         * REDIRECT KE HALAMAN EDIT PROFIL
         * ----------------------------------------------
         * with('status'):
         * - Digunakan untuk notifikasi sukses
         */
        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * ==================================================
     * [METHOD] destroy(Request $request)
     * ==================================================
     * FUNGSI:
     * - Menghapus akun user
     *
     * TUJUAN:
     * - Memberikan user opsi menghapus akunnya sendiri
     * - Menjaga keamanan sebelum penghapusan
     *
     * TIPE:
     * - DELETE (D dalam CRUD)
     */
    public function destroy(Request $request): RedirectResponse
    {
        /**
         * ----------------------------------------------
         * VALIDASI PASSWORD SEBELUM HAPUS AKUN
         * ----------------------------------------------
         * validateWithBag():
         * - Menggunakan error bag khusus "userDeletion"
         * - Agar tidak tercampur dengan error form lain
         */
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        // Simpan user sementara sebelum logout
        $user = $request->user();

        /**
         * ----------------------------------------------
         * LOGOUT USER
         * ----------------------------------------------
         * Penting agar session dibersihkan
         */
        Auth::logout();

        /**
         * ----------------------------------------------
         * HAPUS AKUN USER
         * ----------------------------------------------
         */
        $user->delete();

        /**
         * ----------------------------------------------
         * BERSIHKAN SESSION
         * ----------------------------------------------
         * invalidate()     : hapus semua session
         * regenerateToken(): buat CSRF token baru
         */
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        /**
         * ----------------------------------------------
         * REDIRECT KE HALAMAN AWAL
         * ----------------------------------------------
         */
        return Redirect::to('/');
    }
}
