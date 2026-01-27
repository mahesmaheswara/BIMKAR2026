<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DetailOrder;
use App\Models\Order;
use App\Models\Tiket;
use App\Models\PaymentType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * ==================================================
     * INDEX
     * ==================================================
     * Menampilkan riwayat pesanan user
     */
    public function index()
    {
        $user = Auth::user() ?? \App\Models\User::first();

        $orders = Order::where('user_id', $user->id)
            ->with(['event', 'paymentType'])
            ->orderByDesc('created_at')
            ->get();

        return view('orders.index', compact('orders'));
    }

    /**
     * ==================================================
     * CREATE (CHECKOUT PAGE)
     * ==================================================
     * Menampilkan halaman checkout
     */
    public function create()
    {
        $paymentTypes = PaymentType::orderBy('nama')->get();

        return view('orders.checkout', compact('paymentTypes'));
    }

    /**
     * ==================================================
     * SHOW
     * ==================================================
     * Menampilkan detail satu pesanan
     */
    public function show(Order $order)
    {
        $order->load(
            'detailOrders.tiket',
            'event',
            'paymentType'
        );

        return view('orders.show', compact('order'));
    }

    /**
     * ==================================================
     * STORE (AJAX CHECKOUT)
     * ==================================================
     * Menyimpan pesanan baru
     */
    public function store(Request $request)
    {
        // 1. Validasi data dari checkout
        $data = $request->validate([
            'event_id'        => 'required|exists:events,id',
            'payment_type_id' => 'required|exists:payment_types,id',
        ]);

        // 2. Ambil data checkout dari session
        $checkout = session('checkout');

        if (
            !$checkout ||
            empty($checkout['items']) ||
            $checkout['event_id'] != $data['event_id']
        ) {
            return response()->json([
                'ok' => false,
                'message' => 'Data checkout tidak valid atau sudah kedaluwarsa.'
            ], 422);
        }

        $items = $checkout['items'];
        $user  = auth()->user();

        try {
            $order = \DB::transaction(function () use ($items, $data, $user) {

                $total = 0;

                // 3. Validasi stok + hitung total
                foreach ($items as $it) {
                    $tiket = \App\Models\Tiket::lockForUpdate()
                        ->findOrFail($it['tiket_id']);

                    if ($tiket->stok < $it['jumlah']) {
                        throw new \Exception(
                            "Stok tidak cukup untuk tiket {$tiket->tipe}"
                        );
                    }

                    $total += ($tiket->harga ?? 0) * $it['jumlah'];
                }

                // 4. Buat order
                $order = \App\Models\Order::create([
                    'user_id'         => $user->id,
                    'event_id'        => $data['event_id'],
                    'payment_type_id' => $data['payment_type_id'],
                    'order_date'      => now(),
                    'total_harga'     => $total,
                ]);

                // 5. Simpan detail order + kurangi stok
                foreach ($items as $it) {
                    $tiket = \App\Models\Tiket::findOrFail($it['tiket_id']);

                    \App\Models\DetailOrder::create([
                        'order_id'       => $order->id,
                        'tiket_id'       => $tiket->id,
                        'jumlah'         => $it['jumlah'],
                        'subtotal_harga' => ($tiket->harga ?? 0) * $it['jumlah'],
                    ]);

                    $tiket->stok -= $it['jumlah'];
                    $tiket->save();
                }

                return $order;
            });

            // 6. HAPUS SESSION CHECKOUT (PENTING)
            session()->forget('checkout');

            return response()->json([
                'ok'       => true,
                'order_id' => $order->id,
                'redirect' => route('orders.index'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok'      => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

}
