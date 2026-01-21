<?php

/**
 * ==========================================================
 * JUDUL  : GuestLayout Component (Layout Tamu)
 * LOKASI : app/View/Components/GuestLayout.php
 * ==========================================================
 *
 * FUNGSI:
 * View Component ini digunakan sebagai layout
 * untuk halaman yang diakses oleh user
 * yang BELUM login.
 *
 * TUJUAN:
 * - Menyediakan layout sederhana untuk halaman auth
 * - Memisahkan tampilan guest dan user login
 * - Menjaga keamanan & UX aplikasi
 *
 * DIGUNAKAN OLEH:
 * - Halaman login
 * - Halaman register
 * - Halaman reset password
 *
 * KAMUS UMUM:
 * - Guest : Pengunjung yang belum login
 * - Layout: Kerangka tampilan halaman
 */

namespace App\View\Components;

// Base class untuk View Component
use Illuminate\View\Component;

// Tipe return View
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * ==================================================
     * [METHOD] render()
     * ==================================================
     * FUNGSI:
     * - Menentukan file blade layout untuk guest
     *
     * RETURN:
     * - components/layouts/guest.blade.php
     *
     * CARA PAKAI DI VIEW:
     * <x-layouts.guest>
     *    konten halaman auth
     * </x-layouts.guest>
     */
    public function render(): View
    {
        return view(
            'components.layouts.guest'
        );
    }
}
