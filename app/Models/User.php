<?php

/**
 * ==========================================================
 * JUDUL  : User Model (Data Pengguna Sistem)
 * LOKASI : app/Models/User.php
 * ==========================================================
 *
 * FUNGSI:
 * Model ini merepresentasikan tabel `users`
 * yang menyimpan seluruh data pengguna sistem.
 *
 * TUJUAN:
 * - Menyimpan data user (nama, email, role, dll)
 * - Digunakan dalam proses autentikasi & otorisasi
 * - Menjadi pusat relasi antara user dan sistem
 *
 * PERAN DALAM SISTEM:
 * - User dapat memiliki role (admin / user)
 * - User dapat membuat event (admin)
 * - User dapat melakukan pemesanan tiket
 *
 * KAMUS UMUM:
 * - Authenticatable : Model khusus untuk autentikasi
 * - HasFactory      : Digunakan untuk seeding & testing
 * - Notifiable      : Mendukung notifikasi (email, dll)
 * - Mass Assignment : Pengisian data massal yang aman
 */

namespace App\Models;

// Trait untuk factory (testing / seeding)
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Base model khusus user (auth)
use Illuminate\Foundation\Auth\User as Authenticatable;

// Trait untuk notifikasi (email reset password, dll)
use Illuminate\Notifications\Notifiable;

// Trait untuk API token (opsional, API auth)
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /**
     * ==================================================
     * TRAIT YANG DIGUNAKAN
     * ==================================================
     * - HasFactory  : Untuk factory & seeder
     * - Notifiable : Untuk sistem notifikasi
     */
    use HasFactory, Notifiable;

    /**
     * ==================================================
     * MASS ASSIGNMENT
     * ==================================================
     * Kolom yang BOLEH diisi menggunakan:
     * - User::create()
     * - $user->update()
     *
     * Tujuan:
     * - Keamanan data
     * - Mencegah pengisian kolom sensitif
     */
    protected $fillable = [
        'name',      // Nama lengkap user
        'email',     // Email user (unik)
        'role',      // Role user (admin / user)
        'no_hp',     // Nomor HP user
        'password',  // Password (akan di-hash)
    ];

    /**
     * ==================================================
     * HIDDEN ATRIBUTES
     * ==================================================
     * Kolom yang DISMBUNYIKAN saat:
     * - JSON response
     * - API response
     *
     * Tujuan:
     * - Keamanan data sensitif
     */
    protected $hidden = [
        'password',        // Password terenkripsi
        'remember_token',  // Token login
    ];

    /**
     * ==================================================
     * CASTING ATRIBUT
     * ==================================================
     * Mengubah tipe data secara otomatis
     */
    protected $casts = [
        // Email verification otomatis jadi datetime
        'email_verified_at' => 'datetime',

        /**
         * 'hashed' artinya:
         * - Password akan otomatis di-hash
         * - Tidak perlu Hash::make() manual
         */
        'password' => 'hashed',
    ];

    /**
     * ==================================================
     * (OPSIONAL) RELASI USER
     * ==================================================
     * Catatan:
     * Relasi user ke event / order TIDAK WAJIB
     * ditulis di sini jika sudah cukup dari sisi lain.
     *
     * Namun jika ingin:
     *
     * public function events() {
     *     return $this->hasMany(Event::class);
     * }
     *
     * public function orders() {
     *     return $this->hasMany(Order::class);
     * }
     */
}
