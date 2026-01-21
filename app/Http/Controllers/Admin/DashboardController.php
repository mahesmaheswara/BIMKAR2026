<?php

/**
 * ==========================================================
 * JUDUL  : DashboardController (Dashboard Admin)
 * LOKASI : app/Http/Controllers/Admin/DashboardController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini bertugas untuk menampilkan halaman dashboard
 * khusus Admin yang berisi ringkasan data utama sistem.
 *
 * TUJUAN:
 * - Memberikan gambaran cepat kondisi sistem kepada admin
 * - Menampilkan data statistik penting dalam bentuk angka
 * - Membantu admin memantau aktivitas aplikasi ticketing
 *
 * KAMUS:
 * - Dashboard        : Halaman ringkasan informasi utama
 * - Controller       : Penghubung antara Model dan View
 * - Model            : Representasi tabel database
 * - count()          : Menghitung jumlah data dalam tabel
 * - compact()        : Mengirim variabel ke view
 */

namespace App\Http\Controllers\Admin;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Model Event (tabel events)
use App\Models\Event;

// Model Order (tabel orders)
use App\Models\Order;

// Model Tiket (digunakan di sistem ticketing, meskipun tidak dipakai langsung di method ini)
use App\Models\Tiket;

// Request (disiapkan jika nanti dashboard membutuhkan input/request)
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * ==================================================
     * [METHOD] index()
     * ==================================================
     * FUNGSI:
     * - Menampilkan halaman dashboard admin
     *
     * TUJUAN:
     * - Menyediakan ringkasan data sistem dalam bentuk angka
     * - Data ini biasanya ditampilkan sebagai card/statistik
     *
     * TIPE METHOD:
     * - READ (hanya membaca data, tidak mengubah database)
     */
    public function index()
    {
        /**
         * ----------------------------------------------
         * MENGHITUNG TOTAL EVENT
         * ----------------------------------------------
         * Mengambil jumlah seluruh event yang terdaftar
         * di sistem ticketing
         */
        $totalEvents = Event::count();

        /**
         * ----------------------------------------------
         * MENGHITUNG TOTAL KATEGORI
         * ----------------------------------------------
         * Mengambil jumlah kategori event yang tersedia
         * Menggunakan pemanggilan model secara langsung
         */
        $totalCategories = \App\Models\Kategori::count();

        /**
         * ----------------------------------------------
         * MENGHITUNG TOTAL ORDER
         * ----------------------------------------------
         * Mengambil jumlah seluruh pesanan tiket
         * yang pernah dibuat oleh user
         */
        $totalOrders = Order::count();

        /**
         * ----------------------------------------------
         * KIRIM DATA KE VIEW DASHBOARD
         * ----------------------------------------------
         * Data dikirim ke:
         * resources/views/admin/dashboard.blade.php
         *
         * Variabel yang dikirim:
         * - $totalEvents
         * - $totalCategories
         * - $totalOrders
         */
        return view(
            'admin.dashboard',
            compact('totalEvents', 'totalCategories', 'totalOrders')
        );
    }
}
