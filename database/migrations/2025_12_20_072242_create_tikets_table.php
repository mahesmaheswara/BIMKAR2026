<?php

/**
 * ==========================================================
 * JUDUL  : Migration Tabel Tikets
 * LOKASI : database/migrations/2025_12_20_072242_create_tikets_table.php
 * ==========================================================
 *
 * FUNGSI:
 * Migration ini digunakan untuk membuat tabel `tikets`
 * yang menyimpan data tiket untuk setiap event.
 *
 * TUJUAN:
 * - Menyimpan berbagai tipe tiket dalam satu event
 * - Menyimpan harga tiket
 * - Menyimpan stok tiket
 * - Menjadi dasar transaksi pemesanan
 *
 * HUBUNGAN DENGAN TABEL LAIN:
 * - events.id → tikets.event_id
 *
 * KAMUS UMUM:
 * - enum       : Pilihan nilai terbatas
 * - foreignId  : Relasi ke tabel lain
 * - cascade    : Data anak ikut terhapus
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
     * - Membuat tabel `tikets`
     * - Dieksekusi saat `php artisan migrate`
     */
    public function up(): void
    {
        Schema::create('tikets', function (Blueprint $table) {

            // Primary key tiket
            $table->id();

            /**
             * ==========================================
             * RELASI KE EVENT
             * ==========================================
             * Setiap tiket pasti milik satu event
             *
             * onDelete('cascade'):
             * - Jika event dihapus, tiket ikut terhapus
             */
            $table->foreignId('event_id')
                  ->constrained()
                  ->onDelete('cascade');

            /**
             * ==========================================
             * TIPE TIKET
             * ==========================================
             * enum digunakan untuk membatasi pilihan tipe
             *
             * Contoh:
             * - reguler
             * - premium
             * - vip
             * - vvip
             * - student
             * - early_bird
             */
            $table->enum('tipe', [
                'reguler',
                'premium',
                'vip',
                'vvip',
                'student',
                'early_bird',
            ]);

            /**
             * ==========================================
             * HARGA TIKET
             * ==========================================
             * decimal(10,2):
             * - Maks 10 digit
             * - 2 digit desimal
             */
            $table->decimal('harga', 10, 2);

            /**
             * ==========================================
             * STOK TIKET
             * ==========================================
             * Menentukan jumlah tiket tersedia
             */
            $table->integer('stok');

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
     * - Menghapus tabel `tikets`
     * - Digunakan saat rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('tikets');
    }
};
