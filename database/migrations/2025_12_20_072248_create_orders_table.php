<?php

/**
 * ==========================================================
 * JUDUL  : Migration Tabel Orders
 * LOKASI : database/migrations/2025_12_20_072248_create_orders_table.php
 * ==========================================================
 *
 * FUNGSI:
 * Migration ini digunakan untuk membuat tabel `orders`
 * yang menyimpan data utama pemesanan tiket.
 *
 * TUJUAN:
 * - Menyimpan transaksi pembelian tiket
 * - Menghubungkan user dengan event
 * - Menyimpan total harga pesanan
 *
 * HUBUNGAN DENGAN TABEL LAIN:
 * - users.id  → orders.user_id
 * - events.id → orders.event_id
 *
 * KAMUS UMUM:
 * - foreignId  : Kolom relasi ke tabel lain
 * - cascade    : Data anak ikut terhapus
 * - order_date : Waktu transaksi dilakukan
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
     * - Membuat tabel `orders`
     * - Dieksekusi saat `php artisan migrate`
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            // Primary key (ID order)
            $table->id();

            /**
             * ==========================================
             * RELASI KE USER
             * ==========================================
             * User yang melakukan pemesanan
             *
             * onDelete('cascade'):
             * - Jika user dihapus, order ikut terhapus
             */
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            /**
             * ==========================================
             * RELASI KE EVENT
             * ==========================================
             * Event yang dipesan oleh user
             *
             * onDelete('cascade'):
             * - Jika event dihapus, order ikut terhapus
             */
            $table->foreignId('event_id')
                  ->constrained()
                  ->onDelete('cascade');

            /**
             * ==========================================
             * WAKTU PEMESANAN
             * ==========================================
             * Menyimpan tanggal & jam transaksi
             */
            $table->dateTime('order_date');

            /**
             * ==========================================
             * TOTAL HARGA
             * ==========================================
             * Total harga seluruh tiket dalam satu order
             *
             * decimal(10,2):
             * - Presisi aman untuk nilai uang
             */
            $table->decimal('total_harga', 10, 2);

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
     * - Menghapus tabel `orders`
     * - Digunakan saat rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
