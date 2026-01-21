<?php

/**
 * ==========================================================
 * JUDUL  : Alter Tabel Tikets - Tambah Relasi Tipe Tiket
 * LOKASI : database/migrations/xxxx_xx_xx_alter_tikets_add_tipe_tiket_id.php
 * ==========================================================
 *
 * FUNGSI:
 * Migration ini digunakan untuk:
 * - Menghapus kolom ENUM `tipe` pada tabel `tikets`
 * - Menggantinya dengan foreign key `tipe_tiket_id`
 *
 * TUJUAN:
 * - Mendukung fitur CRUD Tipe Tiket oleh admin
 * - Menghilangkan keterbatasan ENUM
 * - Membuat sistem ticket lebih fleksibel
 *
 * HUBUNGAN BARU:
 * - tipe_tikets.id → tikets.tipe_tiket_id
 *
 * CATATAN PENTING:
 * - Migration ini diasumsikan dijalankan pada database BARU
 *   atau data lama ENUM sudah dikosongkan.
 *
 * KAMUS UMUM:
 * - alter table : Mengubah struktur tabel
 * - foreign key : Relasi antar tabel
 * - cascade     : Data ikut terhapus
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
     * - Mengubah struktur tabel `tikets`
     * - Menambahkan relasi ke `tipe_tikets`
     */
    public function up(): void
    {
        Schema::table('tikets', function (Blueprint $table) {

            /**
             * ==========================================
             * TAMBAH KOLOM FK TIPE TIKET
             * ==========================================
             */
            $table->foreignId('tipe_tiket_id')
                  ->after('event_id')
                  ->constrained('tipe_tikets')
                  ->onDelete('cascade');

            /**
             * ==========================================
             * HAPUS KOLOM ENUM LAMA
             * ==========================================
             * Kolom `tipe` sebelumnya menggunakan ENUM
             * dan tidak lagi digunakan
             */
            $table->dropColumn('tipe');
        });
    }

    /**
     * ==================================================
     * [METHOD] down()
     * ==================================================
     * FUNGSI:
     * - Mengembalikan struktur tabel `tikets`
     * - Dipakai saat rollback
     */
    public function down(): void
    {
        Schema::table('tikets', function (Blueprint $table) {

            /**
             * ==========================================
             * KEMBALIKAN KOLOM ENUM `tipe`
             * ==========================================
             * (Disesuaikan dengan enum lama)
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
             * HAPUS RELASI TIPE TIKET
             * ==========================================
             */
            $table->dropForeign(['tipe_tiket_id']);
            $table->dropColumn('tipe_tiket_id');
        });
    }
};
