<?php

/**
 * ==========================================================
 * JUDUL  : UserEventController (Detail Event & Pemesanan User)
 * LOKASI : app/Http/Controllers/UserEventController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini menangani interaksi USER terhadap EVENT,
 * khususnya:
 * - Menampilkan detail event
 * - Melakukan pemesanan tiket untuk event tertentu
 *
 * TUJUAN:
 * - Menyediakan halaman detail event ke user
 * - Memproses pembelian tiket dengan aman
 * - Menjaga konsistensi stok tiket
 *
 * CATATAN PENTING:
 * - Menggunakan Database Transaction
 * - Menggunakan lockForUpdate() untuk mencegah overselling
 * - Validasi hubungan antara event dan tiket
 *
 * KAMUS UMUM:
 * - Event        : Acara yang ditampilkan ke user
 * - Tiket        : Tiket yang dibeli user
 * - Order        : Data pemesanan utama
 * - DetailOrder  : Rincian tiket dalam satu order
 * - Transaction  : Proses database atomik (all or nothing)
 */

namespace App\Http\Controllers;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Model Event
use App\Models\Event;

// Model Tiket
use App\Models\Tiket;

// Model Order
use App\Models\Order;

// Model DetailOrder
use App\Models\DetailOrder;

// Request standar Laravel
use Illuminate\Http\Request;

// Facade DB untuk transaksi database
use Illuminate\Support\Facades\DB;

// Facade Auth untuk user login
use Illuminate\Support\Facades\Auth;

class UserEventController extends Controller
{
    /**
     * ==================================================
     * [METHOD] show(Event $event)
     * ==================================================
     * FUNGSI:
     * - Menampilkan detail event ke user
     *
     * TUJUAN:
     * - User dapat melihat informasi event
     * - User dapat melihat daftar tiket event
     *
     * TIPE:
     * - READ (R dalam CRUD)
     *
     * CATATAN:
     * - Menggunakan Route Model Binding
     */
    public function show(Event $event)
    {
        /**
         * ----------------------------------------------
         * LOAD RELASI EVENT
         * ----------------------------------------------
         * - kategori : kategori event
         * - tikets   : daftar tiket event
         */
        $event->load([
            'kategori',
            'tikets'
        ]);

        /**
         * ----------------------------------------------
         * KIRIM DATA KE VIEW
         * ----------------------------------------------
         * View:
         * resources/views/events/show.blade.php
         */
        return view('events.show', [
            'event' => $event,
        ]);
    }

    /**
     * ==================================================
     * [METHOD] store(Request $request, $event, $tiket)
     * ==================================================
     * FUNGSI:
     * - Menyimpan pemesanan tiket dari halaman event
     *
     * TUJUAN:
     * - Membuat order & detail order
     * - Mengurangi stok tiket
     *
     * TIPE:
     * - CREATE (C dalam CRUD)
     *
     * CATATAN:
     * - Belum menggunakan route-model-binding
     * - $event dan $tiket masih berupa ID
     */
    public function store(Request $request, $event, $tiket)
    {
        /**
         * ----------------------------------------------
         * VALIDASI INPUT JUMLAH TIKET
         * ----------------------------------------------
         */
        $validated = $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        // Casting untuk keamanan tipe data
        $qty    = (int) $validated['qty'];
        $userId = Auth::user()->id;

        try {
            /**
             * ==========================================
             * DATABASE TRANSACTION
             * ==========================================
             * Menjamin:
             * - Order
             * - DetailOrder
             * - Update stok
             * berjalan atomik
             */
            $order = DB::transaction(function () use ($qty, $userId, $event, $tiket) {

                /**
                 * --------------------------------------
                 * AMBIL & LOCK DATA TIKET
                 * --------------------------------------
                 * lockForUpdate():
                 * - Mencegah pembelian bersamaan
                 * - Menghindari stok minus
                 */
                $tiketModel = Tiket::where('id', $tiket)
                    ->lockForUpdate()
                    ->firstOrFail();

                /**
                 * --------------------------------------
                 * VALIDASI RELASI EVENT & TIKET
                 * --------------------------------------
                 * Pastikan tiket memang milik event
                 * yang sedang dibeli
                 */
                if ((int) $tiketModel->id_event !== (int) $event) {
                    abort(
                        404,
                        'Tiket tidak sesuai event.'
                    );
                }

                /**
                 * --------------------------------------
                 * VALIDASI STOK
                 * --------------------------------------
                 * Aturan:
                 * - stok NULL → tiket unlimited
                 * - stok < qty → gagal
                 */
                if (
                    !is_null($tiketModel->stok)
                    && $tiketModel->stok < $qty
                ) {
                    return back()
                        ->withErrors([
                            'qty' =>
                                'Stok tidak mencukupi. Sisa stok: '
                                . $tiketModel->stok,
                        ])
                        ->throwResponse();
                }

                /**
                 * --------------------------------------
                 * HITUNG SUBTOTAL & TOTAL
                 * --------------------------------------
                 */
                $subtotal = (int) $tiketModel->harga * $qty;

                /**
                 * --------------------------------------
                 * SIMPAN DATA ORDER
                 * --------------------------------------
                 */
                $order = Order::create([
                    'id_user'     => $userId,
                    'id_event'    => $tiketModel->id_event,
                    'order_date'  => now(),
                    'total_price' => $subtotal,
                ]);

                /**
                 * --------------------------------------
                 * SIMPAN DETAIL ORDER
                 * --------------------------------------
                 */
                DetailOrder::create([
                    'id_order' => $order->id,
                    'id_tiket' => $tiketModel->id,
                    'jumlah'   => $qty,
                    'subtotal' => $subtotal,
                ]);

                /**
                 * --------------------------------------
                 * KURANGI STOK TIKET
                 * --------------------------------------
                 * Dilakukan hanya jika stok tidak NULL
                 */
                if (!is_null($tiketModel->stok)) {
                    $tiketModel->stok =
                        $tiketModel->stok - $qty;
                    $tiketModel->save();
                }

                return $order;
            });

            /**
             * ----------------------------------------------
             * REDIRECT JIKA BERHASIL
             * ----------------------------------------------
             */
            return redirect()
                ->route(
                    'user.orders.show',
                    $order->id
                )
                ->with(
                    'success',
                    'Pesanan berhasil dibuat.'
                );

        } catch (\Throwable $e) {
            /**
             * ----------------------------------------------
             * HANDLE ERROR TAK TERDUGA
             * ----------------------------------------------
             */
            return back()->with(
                'error',
                'Terjadi kesalahan saat memproses pesanan.'
            );
        }
    }
}
