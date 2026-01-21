<?php

/**
 * ==========================================================
 * JUDUL  : AppLayout Component (Layout Utama Aplikasi)
 * LOKASI : app/View/Components/AppLayout.php
 * ==========================================================
 *
 * FUNGSI:
 * View Component ini digunakan sebagai layout utama
 * untuk halaman yang diakses oleh user yang sudah login.
 *
 * TUJUAN:
 * - Menyatukan struktur halaman (navbar, footer, dll)
 * - Menghindari duplikasi kode layout di setiap view
 * - Menjaga konsistensi tampilan aplikasi
 *
 * DIGUNAKAN OLEH:
 * - <x-layouts.app>
 * - Halaman home
 * - Halaman order
 * - Halaman profile
 *
 * KAMUS UMUM:
 * - View Component : Komponen reusable untuk tampilan
 * - Layout         : Kerangka utama halaman
 */

namespace App\View\Components;

// Base class untuk View Component
use Illuminate\View\Component;

// Tipe return View
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * ==================================================
     * [METHOD] render()
     * ==================================================
     * FUNGSI:
     * - Menentukan file blade yang digunakan
     *   sebagai layout
     *
     * RETURN:
     * - components/layouts/app.blade.php
     *
     * CARA PAKAI DI VIEW:
     * <x-layouts.app>
     *    konten halaman
     * </x-layouts.app>
     */
    public function render(): View
    {
        return view(
            'components.layouts.app'
        );
    }
}
