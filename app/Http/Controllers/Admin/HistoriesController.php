<?php

/**
 * ==========================================================
 * JUDUL  : HistoriesController (Riwayat Pesanan - Admin)
 * LOKASI : app/Http/Controllers/Admin/HistoriesController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini digunakan oleh Admin untuk melihat
 * seluruh riwayat pemesanan tiket yang dilakukan oleh user.
 *
 * TUJUAN:
 * - Memberikan admin akses untuk memantau seluruh transaksi
 * - Menampilkan daftar order yang masuk ke sistem
 * - Melihat detail dari setiap pesanan tiket
 *
 * KAMUS:
 * - Order        : Data pemesanan tiket oleh user
 * - History      : Riwayat transaksi/pesanan
 * - Controller   : Pengatur alur logika aplikasi
 * - latest()     : Mengurutkan data dari yang terbaru
 * - findOrFail() : Mengambil data atau error jika tidak ditemukan
 */

namespace App\Http\Controllers\Admin;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Model Order (tabel orders)
use App\Models\Order;

// Request (disiapkan jika nantinya diperlukan)
use Illuminate\Http\Request;

class HistoriesController extends Controller
{
    /**
     * ==================================================
     * [METHOD] index()
     * ==================================================
     * FUNGSI:
     * - Menampilkan seluruh riwayat pemesanan tiket
     *
     * TUJUAN:
     * - Admin dapat melihat semua transaksi user
     * - Data diurutkan dari pesanan terbaru
     *
     * TIPE:
     * - READ (R dalam CRUD)
     */
    public function index()
    {
        /**
         * ----------------------------------------------
         * MENGAMBIL DATA ORDER TERBARU
         * ----------------------------------------------
         * Menggunakan latest() untuk mengurutkan
         * berdasarkan waktu pembuatan (created_at)
         */
        $histories = Order::latest()->get();

        /**
         * ----------------------------------------------
         * KIRIM DATA KE VIEW
         * ----------------------------------------------
         * View:
         * resources/views/admin/history/index.blade.php
         */
        return view('admin.history.index', compact('histories'));
    }

    /**
     * ==================================================
     * [METHOD] show(string $history)
     * ==================================================
     * FUNGSI:
     * - Menampilkan detail satu riwayat pesanan
     *
     * TUJUAN:
     * - Admin dapat melihat detail order tertentu
     * - Digunakan saat admin klik "Detail"
     *
     * TIPE:
     * - READ (R dalam CRUD)
     */
    public function show(string $history)
    {
        /**
         * ----------------------------------------------
         * AMBIL DATA ORDER BERDASARKAN ID
         * ----------------------------------------------
         * Jika data tidak ditemukan, otomatis error 404
         */
        $order = Order::findOrFail($history);

        /**
         * ----------------------------------------------
         * KIRIM DATA KE VIEW DETAIL RIWAYAT
         * ----------------------------------------------
         * View:
         * resources/views/admin/history/show.blade.php
         */
        return view('admin.history.show', compact('order'));
    }
}
