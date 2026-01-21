<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Konser Musik',
            'Seminar',
            'Workshop',
            'Festival',
            'Olahraga',
            'Pameran',
            'Teater',
            'Komedi',
        ];

        foreach ($categories as $name) {
            Kategori::updateOrCreate(
                ['nama' => $name],
                ['nama' => $name]
            );
        }
    }
}
