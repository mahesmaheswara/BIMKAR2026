<?php

/**
 * ==========================================================
 * JUDUL  : Migration Cache Tables
 * LOKASI : database/migrations/0001_01_01_0000001_create_cache_tables.php
 * ==========================================================
 *
 * FUNGSI:
 * Migration ini membuat tabel yang digunakan oleh
 * sistem CACHE Laravel berbasis database.
 *
 * TUJUAN:
 * - Menyimpan data cache aplikasi di database
 * - Mendukung mekanisme lock (mutex) untuk cache
 * - Digunakan saat driver cache = database
 *
 * CATATAN PENTING:
 * - Ini adalah migration BAWAAN Laravel
 * - Bukan fitur bisnis aplikasi ticketing
 * - Tetap penting untuk performa & concurrency
 *
 * KAMUS UMUM:
 * - Cache      : Penyimpanan data sementara
 * - Lock       : Mekanisme penguncian proses
 * - Expiration : Waktu kadaluarsa cache
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
     * - Membuat tabel-tabel cache
     * - Dipanggil saat `php artisan migrate`
     */
    public function up(): void
    {
        /**
         * ==============================================
         * TABEL: cache
         * ==============================================
         * Menyimpan data cache utama
         */
        Schema::create('cache', function (Blueprint $table) {

            /**
             * Key cache
             * - Primary key
             * - Unik untuk setiap item cache
             */
            $table->string('key')->primary();

            /**
             * Value cache
             * - Data cache dalam bentuk serialized
             */
            $table->mediumText('value');

            /**
             * Expiration time
             * - Waktu kadaluarsa cache (timestamp)
             */
            $table->integer('expiration');
        });

        /**
         * ==============================================
         * TABEL: cache_locks
         * ==============================================
         * Digunakan untuk mekanisme LOCK cache
         */
        Schema::create('cache_locks', function (Blueprint $table) {

            /**
             * Key lock
             * - Primary key
             * - Identifier proses yang dikunci
             */
            $table->string('key')->primary();

            /**
             * Owner lock
             * - Identifier proses / request
             * - Biasanya UUID
             */
            $table->string('owner');

            /**
             * Expiration lock
             * - Waktu lock berakhir
             */
            $table->integer('expiration');
        });
    }

    /**
     * ==================================================
     * [METHOD] down()
     * ==================================================
     * FUNGSI:
     * - Menghapus tabel cache
     * - Digunakan saat rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
