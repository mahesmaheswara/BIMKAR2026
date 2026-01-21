<?php

/**
 * ==========================================================
 * JUDUL  : Tiket Model (Tiket Event)
 * LOKASI : app/Models/Tiket.php
 * ==========================================================
 *
 * FUNGSI:
 * Model ini merepresentasikan tabel `tikets`
 * yang menyimpan data tiket untuk setiap event.
 *
 * TUJUAN:
 * - Menyimpan tipe tiket (Reguler, VIP, dll)
 * - Menyimpan harga tiket
 * - Menyimpan stok tiket
 * - Menjadi penghubung antara Event dan Order
 *
 * PERAN DALAM SISTEM:
 * - Satu Event memiliki banyak Tiket
 * - Satu Tiket bisa dibeli dalam banyak Order
 * - Tiket adalah objek utama dalam transaksi
 *
 * KAMUS UMUM:
 * - Model         : Representasi tabel database
 * - $fillable     : Field yang boleh diisi mass-assignment
 * - belongsTo()   : Relasi many-to-one
 * - hasMany()     : Relasi one-to-many
 * - belongsToMany(): Relasi many-to-many
 */

namespace App\Models;

// Model dasar Eloquent
use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    /**
     * ==================================================
     * MASS ASSIGNMENT
     * ==================================================
     * Field yang diizinkan untuk diisi menggunakan:
     * - Tiket::create()
     * - $tiket->update()
     */
    protected $fillable = [
    'event_id',
    'tipe_tiket_id',
    'harga',
    'stok',
];

public function tipeTiket()
{
    return $this->belongsTo(TipeTiket::class);
}

    /**
     * ==================================================
     * RELASI: Tiket → Event
     * ==================================================
     * FUNGSI:
     * - Mengetahui tiket ini milik event apa
     *
     * CONTOH:
     * $tiket->event
     */
    public function event()
    {
        return $this->belongsTo(
            Event::class
        );
    }

    /**
     * ==================================================
     * RELASI: Tiket → DetailOrder
     * ==================================================
     * FUNGSI:
     * - Mengambil detail transaksi tiket ini
     *
     * CONTOH:
     * $tiket->detailOrders
     */
    public function detailOrders()
    {
        return $this->hasMany(
            DetailOrder::class
        );
    }

    /**
     * ==================================================
     * RELASI: Tiket ↔ Order (Many-to-Many)
     * ==================================================
     * FUNGSI:
     * - Mengetahui tiket ini pernah dibeli
     *   dalam order apa saja
     *
     * PENJELASAN:
     * - Menggunakan tabel pivot `detail_orders`
     * - Pivot menyimpan data tambahan:
     *   - jumlah
     *   - subtotal_harga
     *
     * CONTOH:
     * $tiket->orders
     * $order->pivot->jumlah
     */
    public function orders()
    {
        return $this->belongsToMany(
            Order::class,
            'detail_orders'
        )->withPivot(
            'jumlah',
            'subtotal_harga'
        );
    }
}
