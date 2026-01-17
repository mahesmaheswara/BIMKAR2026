<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Tiket;
use App\Models\Order;
use App\Models\DetailOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserEventController extends Controller
{
    /**
     * Display the specified event.
     */
    // show event details
    public function show(Event $event)
    {
        // Load the event with its relationships
        $event->load(['kategori', 'tikets']);

        return view('events.show', [
            'event' => $event,
        ]);
    }

    public function store(Request $request, $event, $tiket)
    {
        // Kalau route-model-binding dipakai, ubah signature jadi (Event $event, Tiket $tiket)
        // dan hapus findOrFail di bawah.

        // 1) Validasi input qty
        $validated = $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $qty = (int) $validated['qty'];
        $userId = Auth::user()->id;

        try {
            $order = DB::transaction(function () use ($qty, $userId, $event, $tiket) {

                // 2) Ambil tiket + LOCK barisnya biar aman dari pembelian bersamaan
                $tiketModel = Tiket::where('id', $tiket)
                    ->lockForUpdate()
                    ->firstOrFail();

                // 3) Pastikan tiket ini memang milik event yang dibeli
                if ((int) $tiketModel->id_event !== (int) $event) {
                    abort(404, 'Tiket tidak sesuai event.');
                }

                // 4) Cek stok (kalau stok dipakai). stok null = unlimited (sesuaikan aturanmu)
                if (!is_null($tiketModel->stok) && $tiketModel->stok < $qty) {
                    return back()->withErrors([
                        'qty' => 'Stok tidak mencukupi. Sisa stok: ' . $tiketModel->stok,
                    ])->throwResponse();
                }

                // 5) Hitung subtotal & total
                $subtotal = (int) $tiketModel->harga * $qty;

                // 6) Buat order
                $order = Order::create([
                    'id_user'     => $userId,
                    'id_event'    => $tiketModel->id_event,
                    'order_date'  => now(),
                    'total_price' => $subtotal,
                ]);

                // 7) Buat detail order
                DetailOrder::create([
                    'id_order' => $order->id,
                    'id_tiket' => $tiketModel->id,
                    'jumlah'   => $qty,
                    'subtotal' => $subtotal,
                ]);

                // 8) Kurangi stok
                if (!is_null($tiketModel->stok)) {
                    $tiketModel->stok = $tiketModel->stok - $qty;
                    $tiketModel->save();
                }

                return $order;
            });

            // 9) Redirect sukses (ubah ke route yang kamu punya)
            return redirect()
                ->route('user.orders.show', $order->id)
                ->with('success', 'Pesanan berhasil dibuat.');
        } catch (\Throwable $e) {
            // Kalau error lain
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan.');
        }
    }
}
