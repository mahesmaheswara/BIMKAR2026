<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\PaymentType;
use App\Models\Tiket;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Tampilkan halaman checkout
     */
    public function index()
    {
        $checkout = session('checkout');

        if (!$checkout) {
            return redirect()->back()->withErrors('Data checkout tidak valid');
        }

        $tiket = Tiket::with('event')->findOrFail($checkout['tiket_id']);
        $paymentTypes = PaymentType::all();

        return view('checkout.index', compact(
            'tiket',
            'paymentTypes',
            'checkout'
        ));
    }

}
