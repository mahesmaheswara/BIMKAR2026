<?php

/**
 * ==========================================================
 * JUDUL  : DetailOrder Model (Detail Pemesanan Tiket)
 * LOKASI : app/Models/DetailOrder.php
 * ==========================================================
 *
 * FUNGSI:
 * Model ini merepresentasikan tabel `detail_orders`
 * yang menyimpan rincian tiket dalam satu pesanan.
 *
 * TUJUAN:
 * - Menyimpan tiket apa saja yang dibeli dalam satu order
 * - Menyimpan jumlah tiket per tipe
 * - Menyimpan subtotal harga per tiket
 *
 * HUBUNGAN DATA:
 * - Satu Order memiliki banyak DetailOrder
 * - Satu DetailOrder hanya milik satu Order
 * - Satu DetailOrder hanya mengacu ke satu Tiket
 *
 * KAMUS UMUM:
 * - Model            : Representasi tabel database
 * - $fillable        : Field yang boleh diisi mass-assignment
 * - belongsTo()      : Relasi many-to-one
 */

namespace App\Models;

// Model dasar Eloquent
use Illuminate\Database\Eloquent\Model;

class DetailOrder extends Model
{
    /**
     * ==================================================
     * MASS ASSIGNMENT
     * ==================================================
     * Field yang diizinkan untuk diisi menggunakan:
     * - create()
     * - update()
     */
    protected $fillable = [
        'order_id',        // Relasi ke tabel orders
        'tiket_id',        // Relasi ke tabel tikets
        'jumlah',          // Jumlah tiket yang dibeli
        'subtotal_harga',  // Harga subtotal tiket
    ];

    /**
     * ==================================================
     * RELASI: DetailOrder → Order
     * ==================================================
     * FUNGSI:
     * - Menghubungkan detail order ke order induknya
     *
     * CONTOH PENGGUNAAN:
     * $detailOrder->order
     */
    public function order()
    {
        return $this->belongsTo(
            Order::class
        );
    }

    /**
     * ==================================================
     * RELASI: DetailOrder → Tiket
     * ==================================================
     * FUNGSI:
     * - Menghubungkan detail order ke tiket yang dibeli
     *
     * CONTOH PENGGUNAAN:
     * $detailOrder->tiket
     */
    public function tiket()
    {
        return $this->belongsTo(
            Tiket::class
        );
    }
}
