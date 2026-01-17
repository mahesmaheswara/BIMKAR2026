<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\DetailOrder;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PemesananController extends Controller
{
    public function index(Request $request)
    {
        // Ambil tiket berdasarkan ID
        $tiket = Tiket::with('event')->findOrFail($request->tiket_id);

        return view('pemesanan.index', compact('tiket'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'tiket_id' => ['required', 'integer', 'exists:tikets,id'],
            'qty'      => ['required', 'integer', 'min:1'],
        ]);

        $userId  = Auth::id();
        $tiketId = (int) $validated['tiket_id'];
        $qty     = (int) $validated['qty'];

        try {
            $order = DB::transaction(function () use ($userId, $tiketId, $qty) {

                // Lock baris tiket agar aman dari race condition (oversell)
                $tiket = Tiket::with('event')
                    ->where('id', $tiketId)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Validasi stok
                if ($tiket->stok < $qty) {
                    // lempar exception supaya transaksi rollback
                    throw new \RuntimeException('Stok tiket tidak mencukupi.');
                }

                // Hitung harga
                $subtotal = (float) $tiket->harga * $qty;

                // Menyimpan order
                $order = Order::create([
                    'user_id'     => $userId,
                    'event_id'    => $tiket->event_id,
                    'order_date'  => now(),
                    'total_harga' => $subtotal,
                ]);

                // Menyimpan detail order
                DetailOrder::create([
                    'order_id'       => $order->id,
                    'tiket_id'       => $tiket->id,
                    'jumlah'         => $qty,
                    'subtotal_harga' => $subtotal,
                ]);

                // Kurangi stok tiket
                $tiket->decrement('stok', $qty);

                return $order;
            });

            return redirect()
                ->route('home') // ganti ke halaman yang kamu mau
                ->with('success', 'Pemesanan berhasil! ID Order: ' . $order->id);
        } catch (\RuntimeException $e) {
            // untuk error bisnis (mis. stok tidak cukup)
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            // untuk error tak terduga
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    public function riwayat(Request $request)
    {
        $orders = Order::with(['event', 'detailOrders'])
            ->where('user_id', Auth::user()->id)
            ->latest()
            ->paginate(10);

        return view('pemesanan.riwayat', compact('orders'));
    }

    public function detail(Order $order)
    {
        // Biar user gak bisa buka order orang lain
        abort_if($order->user_id !== Auth::user()->id, 403);

        $order->load(['event', 'detailOrders']);

        return view('pemesanan.detail', compact('order'));
    }
}
