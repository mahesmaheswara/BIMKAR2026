<?php

/**
 * ==========================================================
 * JUDUL  : Order Model (Data Pemesanan)
 * LOKASI : app/Models/Order.php
 * ==========================================================
 *
 * FUNGSI:
 * Model ini merepresentasikan tabel `orders`
 * yang menyimpan data utama pemesanan tiket.
 *
 * TUJUAN:
 * - Menyimpan informasi transaksi pemesanan
 * - Menghubungkan user, event, dan tiket
 * - Menjadi induk dari DetailOrder
 *
 * PERAN DALAM SISTEM:
 * - Satu order dibuat oleh satu user
 * - Satu order terkait dengan satu event
 * - Satu order bisa berisi banyak tiket
 *
 * KAMUS UMUM:
 * - Model         : Representasi tabel database
 * - $fillable     : Field yang boleh diisi mass-assignment
 * - $casts        : Casting otomatis tipe data
 * - Pivot Table   : Tabel penghubung many-to-many
 */

namespace App\Models;

// Model dasar Eloquent
use Illuminate\Database\Eloquent\Model;
use App\Models\PaymentType;

class Order extends Model
{
    /**
     * ==================================================
     * MASS ASSIGNMENT
     * ==================================================
     * Field yang boleh diisi menggunakan:
     * - Order::create()
     * - $order->update()
     */
    protected $fillable = [
        'user_id',
        'event_id',
        'payment_type_id',
        'order_date',
        'total_harga',
    ];


    /**
     * ==================================================
     * CASTING ATRIBUT
     * ==================================================
     * order_date dikonversi otomatis menjadi
     * objek Carbon (datetime)
     */
    protected $casts = [
        'order_date' => 'datetime',
    ];

    /**
     * ==================================================
     * RELASI: Order → User
     * ==================================================
     * FUNGSI:
     * - Mengetahui siapa yang melakukan pemesanan
     *
     * CONTOH:
     * $order->user
     */
    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    /**
     * ==================================================
     * RELASI: Order ↔ Tiket (Many-to-Many)
     * ==================================================
     * FUNGSI:
     * - Mengambil semua tiket dalam satu order
     *
     * PENJELASAN:
     * - Relasi many-to-many
     * - Menggunakan tabel pivot `detail_orders`
     * - withPivot() untuk mengambil kolom tambahan
     *
     * CONTOH:
     * $order->tikets
     * $tiket->pivot->jumlah
     * $tiket->pivot->subtotal_harga
     */
    public function tikets()
    {
        return $this->belongsToMany(
            Tiket::class,
            'detail_orders'
        )->withPivot(
            'jumlah',
            'subtotal_harga'
        );
    }

    /**
     * ==================================================
     * RELASI: Order → Event
     * ==================================================
     * FUNGSI:
     * - Mengetahui event yang dipesan
     *
     * CONTOH:
     * $order->event
     */
    public function event()
    {
        return $this->belongsTo(
            Event::class
        );
    }

    /**
     * ==================================================
     * RELASI: Order → DetailOrder
     * ==================================================
     * FUNGSI:
     * - Mengambil detail tiket satu per satu
     *
     * PERBEDAAN DENGAN tikets():
     * - detailOrders → fokus ke data detail
     * - tikets()     → fokus ke relasi tiket
     *
     * CONTOH:
     * $order->detailOrders
     */
    public function detailOrders()
    {
        return $this->hasMany(
            DetailOrder::class
        );
    }

    public function paymentType()
{
    return $this->belongsTo(PaymentType::class);
}
}
