<x-app-layout>
    <div class="max-w-lg mx-auto py-10 px-4">

        <h1 class="text-2xl font-bold mb-6">
            Pemesanan Tiket
        </h1>

        {{-- Info Tiket --}}
        <div class="card bg-base-100 shadow mb-6">
            <div class="card-body">
                <h2 class="card-title">{{ $tiket->event->judul }}</h2>

                <p class="text-sm text-gray-600">
                    Tipe Tiket: <span class="font-medium">{{ ucfirst($tiket->tipe) }}</span>
                </p>

                <p class="text-sm text-gray-600">
                    Harga per tiket:
                    <span class="font-semibold">
                        Rp {{ number_format($tiket->harga, 0, ',', '.') }}
                    </span>
                </p>

                @if (!is_null($tiket->stok))
                    <p class="text-sm text-gray-600">
                        Stok tersedia: {{ $tiket->stok }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Form Pemesanan --}}
        <form method="POST" action="{{ route('pemesanan.store') }}">
            @csrf

            <input type="hidden" name="tiket_id" value="{{ $tiket->id }}">

            {{-- Jumlah --}}
            <div class="mb-4">
                <label class="label">
                    <span class="label-text">Jumlah Tiket</span>
                </label>

                <input
                    id="qty"
                    type="number"
                    name="qty"
                    min="1"
                    max="{{ $tiket->stok ?? 99 }}"
                    value="1"
                    class="input input-bordered w-full"
                    required
                >

                @error('qty')
                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Total --}}
            <div class="border rounded-lg p-4 mb-6">
                <div class="flex justify-between text-sm">
                    <span>Harga per tiket</span>
                    <span id="priceText"></span>
                </div>

                <div class="flex justify-between font-semibold text-lg mt-2">
                    <span>Total</span>
                    <span id="totalText"></span>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn btn-primary w-full">
                Konfirmasi Pemesanan
            </button>
        </form>
    </div>

    {{-- Script hitung total --}}
    <script>
    (function () {
        const price = {{ (int) $tiket->harga }};
        const qtyInput = document.getElementById('qty');
        const priceText = document.getElementById('priceText');
        const totalText = document.getElementById('totalText');

        const formatIDR = (n) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(n);

        const update = () => {
            let qty = parseInt(qtyInput.value || 1, 10);
            if (qty < 1) qty = 1;
            if (qtyInput.max && qty > qtyInput.max) qty = qtyInput.max;
            qtyInput.value = qty;

            priceText.textContent = formatIDR(price);
            totalText.textContent = formatIDR(price * qty);
        };

        qtyInput.addEventListener('input', update);
        update();
    })();
    </script>
</x-app-layout>