<?php

/**
 * ==========================================================
 * JUDUL  : Kategori Model (Kategori Event)
 * LOKASI : app/Models/Kategori.php
 * ==========================================================
 *
 * FUNGSI:
 * Model ini merepresentasikan tabel `kategoris`
 * yang digunakan untuk mengelompokkan event.
 *
 * TUJUAN:
 * - Mengelompokkan event berdasarkan kategori
 * - Memudahkan filter dan pencarian event
 * - Menjaga struktur data tetap rapi & terorganisir
 *
 * PERAN DALAM SISTEM:
 * - Satu kategori dapat memiliki banyak event
 * - Satu event hanya memiliki satu kategori
 *
 * KAMUS UMUM:
 * - Model      : Representasi tabel database
 * - $fillable  : Field yang boleh diisi mass-assignment
 * - hasMany()  : Relasi one-to-many
 */

namespace App\Models;

// Model dasar Eloquent
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    /**
     * ==================================================
     * MASS ASSIGNMENT
     * ==================================================
     * Field yang diizinkan untuk diisi menggunakan:
     * - Kategori::create()
     * - $kategori->update()
     */
    protected $fillable = [
        'nama', // Nama kategori (misal: Konser, Seminar)
    ];

    /**
     * ==================================================
     * RELASI: Kategori → Event
     * ==================================================
     * FUNGSI:
     * - Mengetahui event apa saja dalam kategori ini
     *
     * CONTOH PENGGUNAAN:
     * $kategori->events
     */
    public function events()
    {
        return $this->hasMany(
            Event::class
        );
    }
}
