<?php

/**
 * ==========================================================
 * JUDUL  : Migration Tabel Tipe Tiket
 * LOKASI : database/migrations/xxxx_xx_xx_create_tipe_tikets_table.php
 * ==========================================================
 *
 * FUNGSI:
 * Migration ini digunakan untuk membuat tabel `tipe_tikets`
 * yang menyimpan daftar tipe tiket secara DINAMIS.
 *
 * TUJUAN:
 * - Menghilangkan penggunaan ENUM pada tabel tikets
 * - Memungkinkan admin melakukan CRUD tipe tiket
 * - Membuat sistem lebih fleksibel & scalable
 *
 * CONTOH DATA:
 * - VIP
 * - VVIP
 * - Early Bird
 * - Student
 *
 * HUBUNGAN DENGAN TABEL LAIN:
 * - tipe_tikets.id → tikets.tipe_tiket_id
 *
 * KAMUS UMUM:
 * - Migration : Versi struktur database
 * - Blueprint : Definisi kolom tabel
 * - up()      : Membuat tabel
 * - down()    : Menghapus tabel
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ==================================================
     * [METHOD] up()
     * ==================================================
     * FUNGSI:
     * - Membuat tabel `tipe_tikets`
     * - Dieksekusi saat `php artisan migrate`
     */
    public function up(): void
    {
        Schema::create('tipe_tikets', function (Blueprint $table) {

            // Primary key
            $table->id();

            /**
             * Nama tipe tiket
             * Contoh:
             * - VIP
             * - VVIP
             * - Early Bird
             */
            $table->string('nama')->unique();

            /**
             * Timestamps:
             * - created_at
             * - updated_at
             */
            $table->timestamps();
        });
    }

    /**
     * ==================================================
     * [METHOD] down()
     * ==================================================
     * FUNGSI:
     * - Menghapus tabel `tipe_tikets`
     * - Digunakan saat rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('tipe_tikets');
    }
};
