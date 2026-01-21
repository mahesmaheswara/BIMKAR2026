<?php

/**
 * ==========================================================
 * JUDUL  : Migration Tabel Detail Orders
 * LOKASI : database/migrations/2025_12_20_072255_create_detail_orders_table.php
 * ==========================================================
 *
 * FUNGSI:
 * Migration ini digunakan untuk membuat tabel `detail_orders`
 * yang menyimpan rincian tiket dalam setiap pemesanan.
 *
 * TUJUAN:
 * - Menyimpan tiket apa saja yang dibeli dalam satu order
 * - Menyimpan jumlah tiket per tipe
 * - Menyimpan subtotal harga per tiket
 *
 * PERAN DALAM SISTEM:
 * - Sebagai tabel pivot antara orders dan tikets
 * - Mengimplementasikan relasi many-to-many
 *
 * HUBUNGAN DENGAN TABEL LAIN:
 * - orders.id → detail_orders.order_id
 * - tikets.id → detail_orders.tiket_id
 *
 * KAMUS UMUM:
 * - Pivot Table : Tabel penghubung many-to-many
 * - Cascade    : Data ikut terhapus
 * - Subtotal   : Harga per item sebelum dijumlahkan
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
     * - Membuat tabel `detail_orders`
     * - Dieksekusi saat `php artisan migrate`
     */
    public function up(): void
    {
        Schema::create('detail_orders', function (Blueprint $table) {

            // Primary key detail order
            $table->id();

            /**
             * ==========================================
             * RELASI KE ORDER
             * ==========================================
             * Setiap detail order pasti milik satu order
             *
             * onDelete('cascade'):
             * - Jika order dihapus, detail ikut terhapus
             */
            $table->foreignId('order_id')
                  ->constrained()
                  ->onDelete('cascade');

            /**
             * ==========================================
             * RELASI KE TIKET
             * ==========================================
             * Tiket yang dibeli dalam order
             *
             * onDelete('cascade'):
             * - Jika tiket dihapus, detail ikut terhapus
             */
            $table->foreignId('tiket_id')
                  ->constrained()
                  ->onDelete('cascade');

            /**
             * ==========================================
             * JUMLAH TIKET
             * ==========================================
             * Menyimpan jumlah tiket yang dibeli
             */
            $table->integer('jumlah');

            /**
             * ==========================================
             * SUBTOTAL HARGA
             * ==========================================
             * Harga tiket × jumlah
             *
             * decimal(10,2):
             * - Presisi aman untuk nilai uang
             */
            $table->decimal('subtotal_harga', 10, 2);

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
     * - Menghapus tabel `detail_orders`
     * - Digunakan saat rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_orders');
    }
};
