<?php

/**
 * ==========================================================
 * JUDUL  : Event Model (Data Event / Acara)
 * LOKASI : app/Models/Event.php
 * ==========================================================
 *
 * FUNGSI:
 * Model ini merepresentasikan tabel `events`
 * yang menyimpan data utama sebuah acara.
 *
 * TUJUAN:
 * - Menyimpan informasi event (judul, deskripsi, lokasi, dll)
 * - Menghubungkan event dengan:
 *   - Kategori
 *   - Tiket
 *   - User (pembuat event)
 *   - Order (pesanan)
 *
 * PERAN DALAM SISTEM:
 * Event adalah pusat dari sistem ticketing:
 * - Event memiliki banyak tiket
 * - Event bisa dipesan oleh banyak user
 *
 * KAMUS UMUM:
 * - Model         : Representasi tabel database
 * - $fillable     : Field yang boleh diisi mass-assignment
 * - $casts        : Konversi tipe data otomatis
 * - Relasi        : Hubungan antar tabel
 */

namespace App\Models;

// Trait untuk factory (testing / seeding)
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Model dasar Eloquent
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * ==================================================
     * MASS ASSIGNMENT
     * ==================================================
     * Field yang boleh diisi melalui:
     * - Event::create()
     * - $event->update()
     */
    protected $fillable = [
        'user_id',        // Admin / user pembuat event
        'kategori_id',    // Kategori event
        'judul',          // Judul event
        'deskripsi',      // Deskripsi event
        'lokasi',         // Lokasi event
        'gambar',         // Gambar event
        'tanggal_waktu',  // Waktu pelaksanaan event
    ];

    /**
     * ==================================================
     * CASTING ATRIBUT
     * ==================================================
     * tanggal_waktu otomatis dikonversi menjadi
     * objek Carbon (datetime)
     *
     * Manfaat:
     * - Bisa pakai format()
     * - Bisa pakai diffForHumans()
     */
    protected $casts = [
        'tanggal_waktu' => 'datetime',
    ];

    /**
     * ==================================================
     * RELASI: Event → Tiket
     * ==================================================
     * FUNGSI:
     * - Satu event memiliki banyak tiket
     *
     * CONTOH:
     * $event->tikets
     */
    public function tikets()
    {
        return $this->hasMany(
            Tiket::class
        );
    }

    /**
     * ==================================================
     * RELASI: Event → Kategori
     * ==================================================
     * FUNGSI:
     * - Event berada dalam satu kategori
     *
     * CONTOH:
     * $event->kategori
     */
    public function kategori()
    {
        return $this->belongsTo(
            Kategori::class
        );
    }

    /**
     * ==================================================
     * RELASI: Event → User
     * ==================================================
     * FUNGSI:
     * - Mengetahui siapa pembuat event
     *
     * CONTOH:
     * $event->user
     */
    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    /**
     * ==================================================
     * RELASI: Event → Order
     * ==================================================
     * FUNGSI:
     * - Satu event bisa memiliki banyak order
     *
     * CONTOH:
     * $event->orders
     */
    public function orders()
    {
        return $this->hasMany(
            Order::class
        );
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

}
