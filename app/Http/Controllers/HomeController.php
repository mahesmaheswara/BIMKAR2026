<?php

/**
 * ==========================================================
 * JUDUL  : HomeController (Homepage & Listing Event)
 * LOKASI : app/Http/Controllers/HomeController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini menangani tampilan halaman utama (homepage)
 * yang menampilkan daftar event kepada user.
 *
 * TUJUAN:
 * - Menampilkan seluruh event yang tersedia
 * - Menyediakan fitur filter berdasarkan kategori
 * - Menyediakan fitur pencarian event
 * - Menampilkan harga tiket termurah per event
 *
 * ALUR UMUM:
 * 1. User membuka homepage
 * 2. Sistem menampilkan semua event
 * 3. User dapat memfilter event berdasarkan kategori
 * 4. User dapat mencari event berdasarkan judul
 *
 * KAMUS UMUM:
 * - Event        : Acara yang tersedia di sistem
 * - Kategori     : Pengelompokan event
 * - Query Builder: Cara membangun query database secara dinamis
 * - Eager Loading: Memuat relasi sekaligus untuk efisiensi
 */

namespace App\Http\Controllers;

// Model Event (tabel events)
use App\Models\Event;

// Model Kategori (tabel kategoris)
use App\Models\Kategori;

// Request standar Laravel
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * ==================================================
     * [METHOD] index(Request $request)
     * ==================================================
     * FUNGSI:
     * - Menampilkan halaman homepage
     *
     * TUJUAN:
     * - Menyediakan daftar event yang bisa diakses user
     * - Mendukung filter kategori & pencarian
     *
     * TIPE:
     * - READ (menampilkan data)
     */
    public function index(Request $request)
    {
        /**
         * ----------------------------------------------
         * AMBIL SEMUA KATEGORI
         * ----------------------------------------------
         * Digunakan untuk:
         * - Filter kategori (pills / dropdown)
         * - Navigasi event berdasarkan kategori
         */
        $categories = Kategori::all();

        /**
         * ----------------------------------------------
         * BUAT QUERY EVENT
         * ----------------------------------------------
         * with(['kategori', 'tikets']):
         * - Eager loading relasi kategori
         * - Eager loading relasi tikets
         * Tujuan: mencegah N+1 Query Problem
         */
        $eventsQuery = Event::with([
            'kategori',
            'tikets'
        ]);

        /**
         * ----------------------------------------------
         * FILTER BERDASARKAN KATEGORI
         * ----------------------------------------------
         * $request->filled('kategori'):
         * - Mengecek apakah parameter kategori ada
         * - Biasanya dikirim lewat query string (?kategori=)
         */
        if ($request->filled('kategori')) {
            $eventsQuery->where(
                'kategori_id',
                $request->kategori
            );
        }

        /**
         * ----------------------------------------------
         * FILTER BERDASARKAN PENCARIAN
         * ----------------------------------------------
         * Digunakan untuk mencari event berdasarkan judul
         * Contoh: ?q=konser
         */
        if ($request->filled('q')) {
            $q = $request->q;

            $eventsQuery->where(function ($query) use ($q) {
                $query->where(
                    'judul',
                    'like',
                    "%{$q}%"
                );
            });
        }

        /**
         * ----------------------------------------------
         * AMBIL DATA EVENT
         * ----------------------------------------------
         * get()  : Eksekusi query ke database
         * map()  : Memodifikasi setiap data event
         */
        $events = $eventsQuery->get()->map(function ($event) {

            /**
             * ------------------------------------------
             * HITUNG HARGA TIKET TERMURAH
             * ------------------------------------------
             * min('harga'):
             * - Mengambil harga tiket paling murah
             * - Digunakan untuk preview harga di homepage
             *
             * ?? 0:
             * - Jika event belum memiliki tiket
             */
            $event->tikets_min_harga =
                $event->tikets->min('harga') ?? 0;

            return $event;
        });

        /**
         * ----------------------------------------------
         * KIRIM DATA KE VIEW
         * ----------------------------------------------
         * View:
         * resources/views/home.blade.php
         *
         * Data yang dikirim:
         * - categories : daftar kategori
         * - events     : daftar event + harga tiket termurah
         */
        return view('home', [
            'categories' => $categories,
            'events'     => $events,
        ]);
    }
}
