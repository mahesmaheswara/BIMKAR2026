<?php

/**
 * ==========================================================
 * JUDUL  : OrderController (Pemesanan Tiket - User)
 * LOKASI : app/Http/Controllers/User/OrderController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini menangani seluruh proses pemesanan tiket
 * oleh user, mulai dari:
 * - Melihat riwayat pesanan
 * - Melihat detail pesanan
 * - Membuat pesanan baru (checkout via AJAX)
 *
 * TUJUAN:
 * - Memastikan proses order berjalan aman dan konsisten
 * - Mengurangi stok tiket secara real-time
 * - Menyimpan order dan detail order ke database
 *
 * CATATAN PENTING:
 * - Proses checkout menggunakan AJAX (fetch API)
 * - Menggunakan Database Transaction
 * - Menggunakan lockForUpdate() untuk mencegah bentrok stok
 *
 * KAMUS UMUM:
 * - Order        : Data utama pemesanan tiket
 * - DetailOrder  : Detail tiket dalam satu order
 * - Tiket        : Tiket event (stok & harga)
 * - Transaction  : Proses database atomik (all or nothing)
 */

namespace App\Http\Controllers\User;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Model DetailOrder (tabel detail_orders)
use App\Models\DetailOrder;

// Model Order (tabel orders)
use App\Models\Order;

// Model Tiket (tabel tikets)
use App\Models\Tiket;

// Carbon untuk manipulasi tanggal & waktu
use Carbon\Carbon;

// Request standar Laravel
use Illuminate\Http\Request;

// Facade Auth untuk mengambil user login
use Illuminate\Support\Facades\Auth;

// Facade DB untuk database transaction
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * ==================================================
     * [METHOD] index()
     * ==================================================
     * FUNGSI:
     * - Menampilkan riwayat pesanan user
     *
     * TUJUAN:
     * - User dapat melihat semua pesanan yang pernah dibuat
     *
     * TIPE:
     * - READ (R dalam CRUD)
     */
    public function index()
    {
        /**
         * ----------------------------------------------
         * AMBIL USER YANG LOGIN
         * ----------------------------------------------
         * Fallback ke user pertama digunakan hanya
         * untuk keperluan testing / development
         */
        $user = Auth::user() ?? \App\Models\User::first();

        /**
         * ----------------------------------------------
         * AMBIL DATA ORDER USER
         * ----------------------------------------------
         * - Filter berdasarkan user_id
         * - with('event') → eager loading relasi event
         * - orderBy desc → pesanan terbaru di atas
         */
        $orders = Order::where('user_id', $user->id)
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->get();

        /**
         * ----------------------------------------------
         * KIRIM DATA KE VIEW
         * ----------------------------------------------
         * View:
         * resources/views/orders/index.blade.php
         */
        return view('orders.index', compact('orders'));
    }

    /**
     * ==================================================
     * [METHOD] show(Order $order)
     * ==================================================
     * FUNGSI:
     * - Menampilkan detail satu pesanan
     *
     * TUJUAN:
     * - User dapat melihat tiket apa saja yang dibeli
     * - Menampilkan subtotal dan total harga
     *
     * TIPE:
     * - READ (R dalam CRUD)
     *
     * CATATAN:
     * - Menggunakan Route Model Binding
     */
    public function show(Order $order)
    {
        /**
         * ----------------------------------------------
         * LOAD RELASI ORDER
         * ----------------------------------------------
         * - detailOrders → daftar tiket dalam order
         * - tiket        → detail tiket
         * - event        → event terkait
         */
        $order->load(
            'detailOrders.tiket',
            'event'
        );

        /**
         * ----------------------------------------------
         * KIRIM DATA KE VIEW
         * ----------------------------------------------
         * View:
         * resources/views/orders/show.blade.php
         */
        return view('orders.show', compact('order'));
    }

    /**
     * ==================================================
     * [METHOD] store(Request $request)
     * ==================================================
     * FUNGSI:
     * - Menyimpan pesanan baru
     *
     * TUJUAN:
     * - Memproses checkout tiket dari frontend
     * - Mengurangi stok tiket
     *
     * TIPE:
     * - CREATE (C dalam CRUD)
     *
     * CATATAN:
     * - Dipanggil menggunakan AJAX (fetch API)
     * - Mengembalikan response JSON
     */
    public function store(Request $request)
    {
        /**
         * ----------------------------------------------
         * VALIDASI DATA CHECKOUT
         * ----------------------------------------------
         * Struktur data:
         * {
         *   event_id: number,
         *   items: [
         *     { tiket_id: number, jumlah: number }
         *   ]
         * }
         */
        $data = $request->validate([
            'event_id'         => 'required|exists:events,id',
            'items'            => 'required|array|min:1',
            'items.*.tiket_id' => 'required|integer|exists:tikets,id',
            'items.*.jumlah'   => 'required|integer|min:1',
        ]);

        /**
         * ----------------------------------------------
         * AMBIL USER YANG LOGIN
         * ----------------------------------------------
         */
        $user = Auth::user();

        try {
            /**
             * ==========================================
             * DATABASE TRANSACTION
             * ==========================================
             * Semua proses di dalam transaction:
             * - Jika satu gagal → semua dibatalkan
             * - Menjamin konsistensi data
             */
            $order = DB::transaction(function () use ($data, $user) {

                $total = 0;

                /**
                 * --------------------------------------
                 * VALIDASI STOK & HITUNG TOTAL
                 * --------------------------------------
                 * lockForUpdate():
                 * - Mengunci baris tiket
                 * - Mencegah dua user membeli stok sama
                 */
                foreach ($data['items'] as $it) {
                    $t = Tiket::lockForUpdate()
                        ->findOrFail($it['tiket_id']);

                    if ($t->stok < $it['jumlah']) {
                        throw new \Exception(
                            "Stok tidak cukup untuk tipe: {$t->tipe}"
                        );
                    }

                    // Hitung total harga
                    $total += ($t->harga ?? 0) * $it['jumlah'];
                }

                /**
                 * --------------------------------------
                 * BUAT DATA ORDER
                 * --------------------------------------
                 */
                $order = Order::create([
                    'user_id'      => $user->id,
                    'event_id'     => $data['event_id'],
                    'order_date'   => Carbon::now(),
                    'total_harga'  => $total,
                ]);

                /**
                 * --------------------------------------
                 * SIMPAN DETAIL ORDER & KURANGI STOK
                 * --------------------------------------
                 */
                foreach ($data['items'] as $it) {
                    $t = Tiket::findOrFail($it['tiket_id']);

                    $subtotal = ($t->harga ?? 0) * $it['jumlah'];

                    // Simpan detail order
                    DetailOrder::create([
                        'order_id'        => $order->id,
                        'tiket_id'        => $t->id,
                        'jumlah'          => $it['jumlah'],
                        'subtotal_harga'  => $subtotal,
                    ]);

                    /**
                     * Kurangi stok tiket
                     * max(0, ...) → mencegah stok minus
                     */
                    $t->stok = max(0, $t->stok - $it['jumlah']);
                    $t->save();
                }

                return $order;
            });

            /**
             * ----------------------------------------------
             * FLASH MESSAGE
             * ----------------------------------------------
             * Digunakan agar pesan sukses muncul
             * setelah redirect
             */
            session()->flash(
                'success',
                'Pesanan berhasil dibuat.'
            );

            /**
             * ----------------------------------------------
             * RESPONSE JSON (UNTUK AJAX)
             * ----------------------------------------------
             * - ok       : status berhasil
             * - order_id : ID pesanan
             * - redirect : URL tujuan setelah checkout
             */
            return response()->json([
                'ok'       => true,
                'order_id' => $order->id,
                'redirect' => route('orders.index'),
            ]);

        } catch (\Exception $e) {
            /**
             * ----------------------------------------------
             * HANDLE ERROR
             * ----------------------------------------------
             * Jika stok tidak cukup atau error lain,
             * frontend akan menerima pesan error
             */
            return response()->json([
                'ok'      => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
