<?php

/**
 * ==========================================================
 * JUDUL  : Migration Tabel Kategoris
 * LOKASI : database/migrations/2025_12_20_072223_create_kategoris_table.php
 * ==========================================================
 *
 * FUNGSI:
 * Migration ini digunakan untuk membuat tabel `kategoris`
 * yang menyimpan data kategori event.
 *
 * TUJUAN:
 * - Mengelompokkan event berdasarkan kategori
 * - Mendukung fitur filter & pencarian event
 * - Menjaga struktur data tetap terorganisir
 *
 * HUBUNGAN DENGAN TABEL LAIN:
 * - Satu kategori dapat memiliki banyak event
 * - Relasi: kategoris.id → events.kategori_id
 *
 * KAMUS UMUM:
 * - Migration : Versi struktur database
 * - Blueprint : Definisi kolom tabel
 * - up()      : Proses pembuatan tabel
 * - down()    : Proses penghapusan tabel (rollback)
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
     * - Membuat tabel `kategoris`
     * - Dieksekusi saat `php artisan migrate`
     */
    public function up(): void
    {
        Schema::create('kategoris', function (Blueprint $table) {

            // Primary key (auto increment)
            $table->id();

            /**
             * Nama kategori
             * - Harus unik
             * - Contoh: Konser, Seminar, Workshop
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
     * - Menghapus tabel `kategoris`
     * - Digunakan saat rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('kategoris');
    }
};
