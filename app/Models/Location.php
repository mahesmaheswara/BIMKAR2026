<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['nama_lokasi', 'alamat'];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}

