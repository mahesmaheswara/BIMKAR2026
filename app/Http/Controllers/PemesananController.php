<?php

/**
 * ==========================================================
 * JUDUL  : PemesananController (Pemesanan Tiket - User)
 * LOKASI : app/Http/Controllers/PemesananController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini menangani proses pemesanan tiket oleh user,
 * mulai dari:
 * - Menampilkan halaman pemesanan
 * - Menyimpan pesanan
 * - Menampilkan riwayat pemesanan
 * - Menampilkan detail pemesanan
 *
 * TUJUAN:
 * - Memastikan pemesanan tiket aman dan konsisten
 * - Menghindari overselling tiket
 * - Memberikan user akses ke riwayat & detail pesanan
 *
 * CATATAN PENTING:
 * - Menggunakan Database Transaction
 * - Menggunakan lockForUpdate() untuk mencegah race condition
 * - Memisahkan error bisnis dan error teknis
 *
 * KAMUS UMUM:
 * - Order        : Data utama pemesanan
 * - DetailOrder  : Rincian tiket dalam satu order
 * - Tiket        : Tiket event (harga & stok)
 * - Transaction  : Proses database atomik (all or nothing)
 */

namespace App\Http\Controllers;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Model Order (tabel orders)
use App\Models\Order;

// Model DetailOrder (tabel detail_orders)
use App\Models\DetailOrder;

// Model Tiket (tabel tikets)
use App\Models\Tiket;

// Request standar Laravel
use Illuminate\Http\Request;

// Facade Auth untuk user login
use Illuminate\Support\Facades\Auth;

// Facade DB untuk database transaction
use Illuminate\Support\Facades\DB;

class PemesananController extends Controller
{
    /**
     * ==================================================
     * [METHOD] index(Request $request)
     * ==================================================
     * FUNGSI:
     * - Menampilkan halaman pemesanan tiket
     *
     * TUJUAN:
     * - User dapat melihat detail tiket sebelum memesan
     *
     * TIPE:
     * - READ (menampilkan halaman)
     */
    public function index(Request $request)
    {
        /**
         * ----------------------------------------------
         * AMBIL DATA TIKET BERDASARKAN ID
         * ----------------------------------------------
         * with('event'):
         * - Memuat data event terkait
         * findOrFail():
         * - Jika tiket tidak ditemukan → 404
         */
        $tiket = Tiket::with('event')
            ->findOrFail($request->tiket_id);

        /**
         * ----------------------------------------------
         * KIRIM DATA KE VIEW
         * ----------------------------------------------
         * View:
         * resources/views/pemesanan/index.blade.php
         */
        return view('pemesanan.index', compact('tiket'));
    }

    /**
     * ==================================================
     * [METHOD] store(Request $request)
     * ==================================================
     * FUNGSI:
     * - Menyimpan pemesanan tiket
     *
     * TUJUAN:
     * - Membuat order & detail order
     * - Mengurangi stok tiket
     *
     * TIPE:
     * - CREATE (C dalam CRUD)
     */
    public function store(Request $request)
    {
        /**
         * ----------------------------------------------
         * VALIDASI INPUT PEMESANAN
         * ----------------------------------------------
         * - tiket_id wajib ada
         * - qty minimal 1
         */
        $validated = $request->validate([
            'tiket_id' => ['required', 'integer', 'exists:tikets,id'],
            'qty'      => ['required', 'integer', 'min:1'],
        ]);

        // Ambil ID user yang sedang login
        $userId  = Auth::id();

        // Casting ke integer untuk keamanan
        $tiketId = (int) $validated['tiket_id'];
        $qty     = (int) $validated['qty'];

        try {
            /**
             * ==========================================
             * DATABASE TRANSACTION
             * ==========================================
             * Semua proses di dalam transaction:
             * - Jika satu gagal → rollback semua
             */
            $order = DB::transaction(function () use ($userId, $tiketId, $qty) {

                /**
                 * --------------------------------------
                 * LOCK DATA TIKET
                 * --------------------------------------
                 * lockForUpdate():
                 * - Mengunci baris tiket
                 * - Mencegah oversell
                 */
                $tiket = Tiket::with('event')
                    ->where('id', $tiketId)
                    ->lockForUpdate()
                    ->firstOrFail();

                /**
                 * --------------------------------------
                 * VALIDASI STOK
                 * --------------------------------------
                 * Jika stok tidak cukup, lempar exception
                 * agar transaksi di-rollback
                 */
                if ($tiket->stok < $qty) {
                    throw new \RuntimeException(
                        'Stok tiket tidak mencukupi.'
                    );
                }

                /**
                 * --------------------------------------
                 * HITUNG SUBTOTAL
                 * --------------------------------------
                 */
                $subtotal = (float) $tiket->harga * $qty;

                /**
                 * --------------------------------------
                 * SIMPAN DATA ORDER
                 * --------------------------------------
                 */
                $order = Order::create([
                    'user_id'     => $userId,
                    'event_id'    => $tiket->event_id,
                    'order_date'  => now(),
                    'total_harga' => $subtotal,
                ]);

                /**
                 * --------------------------------------
                 * SIMPAN DETAIL ORDER
                 * --------------------------------------
                 */
                DetailOrder::create([
                    'order_id'       => $order->id,
                    'tiket_id'       => $tiket->id,
                    'jumlah'         => $qty,
                    'subtotal_harga' => $subtotal,
                ]);

                /**
                 * --------------------------------------
                 * KURANGI STOK TIKET
                 * --------------------------------------
                 * decrement():
                 * - Operasi atomik di database
                 */
                $tiket->decrement('stok', $qty);

                return $order;
            });

            /**
             * ----------------------------------------------
             * REDIRECT BERHASIL
             * ----------------------------------------------
             */
            return redirect()
                ->route('home')
                ->with(
                    'success',
                    'Pemesanan berhasil! ID Order: ' . $order->id
                );

        } catch (\RuntimeException $e) {
            /**
             * ----------------------------------------------
             * ERROR BISNIS (STOK, VALIDASI LOGIKA)
             * ----------------------------------------------
             */
            return back()
                ->withInput()
                ->with('error', $e->getMessage());

        } catch (\Throwable $e) {
            /**
             * ----------------------------------------------
             * ERROR TEKNIS TAK TERDUGA
             * ----------------------------------------------
             */
            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Terjadi kesalahan. Silakan coba lagi.'
                );
        }
    }

    /**
     * ==================================================
     * [METHOD] riwayat(Request $request)
     * ==================================================
     * FUNGSI:
     * - Menampilkan riwayat pemesanan user
     *
     * TUJUAN:
     * - User dapat melihat semua order miliknya
     *
     * TIPE:
     * - READ (R dalam CRUD)
     */
    public function riwayat(Request $request)
    {
        /**
         * ----------------------------------------------
         * AMBIL DATA ORDER USER
         * ----------------------------------------------
         * - with() → eager loading relasi
         * - latest() → urutkan terbaru
         * - paginate() → pagination
         */
        $orders = Order::with([
                'event',
                'detailOrders'
            ])
            ->where(
                'user_id',
                Auth::user()->id
            )
            ->latest()
            ->paginate(10);

        /**
         * ----------------------------------------------
         * KIRIM DATA KE VIEW
         * ----------------------------------------------
         * View:
         * resources/views/pemesanan/riwayat.blade.php
         */
        return view(
            'pemesanan.riwayat',
            compact('orders')
        );
    }

    /**
     * ==================================================
     * [METHOD] detail(Order $order)
     * ==================================================
     * FUNGSI:
     * - Menampilkan detail satu order
     *
     * TUJUAN:
     * - User dapat melihat rincian tiket yang dibeli
     * - Mencegah user mengakses order orang lain
     *
     * TIPE:
     * - READ (R dalam CRUD)
     */
    public function detail(Order $order)
    {
        /**
         * ----------------------------------------------
         * KEAMANAN AKSES DATA
         * ----------------------------------------------
         * abort_if():
         * - Jika user mencoba membuka order orang lain
         * - Langsung tampilkan error 403 (Forbidden)
         */
        abort_if(
            $order->user_id !== Auth::user()->id,
            403
        );

        /**
         * ----------------------------------------------
         * LOAD RELASI ORDER
         * ----------------------------------------------
         */
        $order->load([
            'event',
            'detailOrders'
        ]);

        /**
         * ----------------------------------------------
         * KIRIM DATA KE VIEW
         * ----------------------------------------------
         * View:
         * resources/views/pemesanan/detail.blade.php
         */
        return view(
            'pemesanan.detail',
            compact('order')
        );
    }
}
