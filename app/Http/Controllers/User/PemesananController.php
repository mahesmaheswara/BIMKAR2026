<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Http\Request;

class PemesananController extends Controller
{
    /**
     * Simpan data pemesanan awal ke session
     * lalu redirect ke checkout
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tiket_id' => 'required|exists:tikets,id',
            'qty' => 'required|integer|min:1',
        ]);

        session([
            'checkout' => [
                'tiket_id' => $data['tiket_id'],
                'qty' => $data['qty'],
            ]
        ]);

        return redirect()->route('checkout.index');
    }

}
