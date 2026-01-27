<x-layouts.app>
<section class="max-w-3xl mx-auto py-12 px-6">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    <form id="checkoutForm" class="space-y-6">
        @csrf

        {{-- Event Info --}}
        <div class="card bg-base-100 shadow p-6 space-y-2">
            <h2 class="font-semibold text-lg">
                {{ $event->judul }}
            </h2>

            <p class="text-sm text-gray-500">
                {{ $event->location?->nama_lokasi ?? '-' }}
            </p>
        </div>

        {{-- Hidden Event ID --}}
        <input type="hidden" name="event_id" value="{{ $event->id }}">

        {{-- Metode Pembayaran --}}
        <div class="card bg-base-100 shadow p-6 space-y-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold">
                        Metode Pembayaran
                    </span>
                </label>

                <select name="payment_type_id"
                        id="payment_type_id"
                        class="select select-bordered w-full"
                        required>
                    <option value="">Pilih metode pembayaran</option>
                    @foreach ($paymentTypes as $pt)
                        <option value="{{ $pt->id }}">
                            {{ $pt->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                    id="btnCheckout"
                    class="btn btn-primary w-full text-white">
                Bayar & Buat Pesanan
            </button>
        </div>
    </form>
</section>

{{-- Script Checkout --}}
<script>
document.getElementById('checkoutForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = e.target;
    const btn  = document.getElementById('btnCheckout');

    btn.disabled = true;
    btn.textContent = 'Memproses...';

    const formData = new FormData(form);

    try {
        const res = await fetch("{{ route('orders.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData,
        });

        const data = await res.json();

        if (!res.ok || !data.ok) {
            throw new Error(data.message || 'Gagal membuat pesanan');
        }

        window.location.href = data.redirect;

    } catch (err) {
        alert(err.message);
        btn.disabled = false;
        btn.textContent = 'Bayar & Buat Pesanan';
    }
});
</script>
</x-layouts.app>
