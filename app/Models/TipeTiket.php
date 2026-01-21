<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipeTiket extends Model
{
    /**
     * ==================================================
     * FILLABLE
     * ==================================================
     * Kolom yang boleh diisi via mass assignment
     */
    protected $fillable = [
        'nama',
    ];

    /**
     * ==================================================
     * RELATION
     * ==================================================
     * Satu tipe tiket bisa dipakai banyak tiket
     */
    public function tikets()
    {
        return $this->hasMany(Tiket::class);
    }
}
