<x-app-layout>
    <div class="max-w-xl mx-auto py-10 px-4">
        <h1 class="text-2xl font-bold mb-6">
            Checkout
        </h1>

        {{-- Ringkasan Event --}}
        <div class="card bg-base-100 shadow mb-6">
            <div class="card-body">
                <h2 class="card-title">
                    {{ $event->judul }}
                </h2>

                <div class="text-sm text-gray-600 space-y-1">
                    <p>{{ $event->location?->nama_lokasi }}</p>
                    <p>
                        Jumlah tiket: {{ $checkout['qty'] }}
                    </p>
                </div>
            </div>
        </div>

        {{-- FORM CHECKOUT --}}
        <form method="POST" action="{{ route('checkout.store') }}">
            @csrf

            {{-- hidden payload --}}
            <input type="hidden" name="event_id" value="{{ $event->id }}">
            <input type="hidden" name="tiket_id" value="{{ $checkout['tiket_id'] }}">
            <input type="hidden" name="qty" value="{{ $checkout['qty'] }}">

            {{-- Metode Pembayaran --}}
            <div class="card bg-base-100 shadow mb-6">
                <div class="card-body">
                    <label class="label">
                        <span class="label-text font-semibold">
                            Metode Pembayaran
                        </span>
                    </label>

                    <select
                        name="payment_type_id"
                        class="select select-bordered w-full"
                        required
                    >
                        <option value="">Pilih Metode Pembayaran</option>
                        @foreach ($paymentTypes as $pt)
                            <option value="{{ $pt->id }}">
                                {{ $pt->nama }}
                            </option>
                        @endforeach
                    </select>

                    @error('payment_type_id')
                        <p class="text-error text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Total --}}
            <div class="border rounded-lg p-4 mb-6">
                <div class="flex justify-between font-semibold text-lg">
                    <span>Total</span>
                    <span>
                        Rp {{ number_format($checkout['total'], 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Action --}}
            <button class="btn btn-primary w-full">
                Bayar & Buat Pesanan
            </button>
        </form>
    </div>
</x-app-layout>
