<?php

/**
 * ==========================================================
 * JUDUL  : Migration Tabel Events
 * LOKASI : database/migrations/2025_12_20_072235_create_events_table.php
 * ==========================================================
 *
 * FUNGSI:
 * Migration ini digunakan untuk membuat tabel `events`
 * yang menyimpan data utama sebuah event/acara.
 *
 * TUJUAN:
 * - Menyimpan informasi event yang dibuat oleh admin
 * - Menghubungkan event dengan:
 *   - User (pembuat event)
 *   - Kategori event
 *
 * HUBUNGAN DENGAN TABEL LAIN:
 * - users.id     → events.user_id
 * - kategoris.id → events.kategori_id
 *
 * KAMUS UMUM:
 * - foreignId    : Kolom relasi ke tabel lain
 * - constrained  : Otomatis membuat foreign key
 * - cascade      : Data ikut terhapus jika parent dihapus
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * ==================================================
     * [METHOD] up()
     * ==================================================
     * FUNGSI:
     * - Membuat tabel `events`
     * - Dieksekusi saat `php artisan migrate`
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {

            // Primary key (ID event)
            $table->id();

            /**
             * ==========================================
             * RELASI KE USER
             * ==========================================
             * Menyimpan ID user (admin) pembuat event
             *
             * onDelete('cascade'):
             * - Jika user dihapus, event ikut terhapus
             */
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            /**
             * ==========================================
             * RELASI KE KATEGORI
             * ==========================================
             * Menyimpan kategori event
             *
             * onDelete('cascade'):
             * - Jika kategori dihapus, event ikut terhapus
             */
            $table->foreignId('kategori_id')
                  ->constrained()
                  ->onDelete('cascade');

            /**
             * ==========================================
             * DATA UTAMA EVENT
             * ==========================================
             */

            // Judul event
            $table->string('judul');

            // Deskripsi lengkap event
            $table->text('deskripsi');

            // Lokasi event
            $table->string('lokasi');

            // Path / nama file gambar event
            $table->string('gambar');

            /**
             * Waktu pelaksanaan event
             * Disimpan sebagai datetime
             */
            $table->dateTime('tanggal_waktu');

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
     * - Menghapus tabel `events`
     * - Digunakan saat rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
