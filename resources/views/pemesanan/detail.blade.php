<x-app-layout>
  <div class="max-w-3xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold mb-6">Detail Order #{{ $order->id }}</h1>

    <div class="card bg-base-100 shadow mb-6">
      <div class="card-body">
        <p><span class="font-semibold">Event:</span> {{ $order->event->judul ?? '-' }}</p>
        <p><span class="font-semibold">Tanggal:</span> {{ $order->order_date->format('d M Y H:i') }}</p>
        <p><span class="font-semibold">Total:</span> Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
      </div>
    </div>

    <div class="card bg-base-100 shadow">
      <div class="card-body">
        <h2 class="font-bold mb-3">Item Tiket</h2>

        <div class="overflow-x-auto">
          <table class="table">
            <thead>
              <tr>
                <th>Tipe</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @foreach($order->detailOrders as $d)
                <tr>
                  <td>{{ ucfirst($d->tiket->tipe ?? '-') }}</td>
                  <td>Rp {{ number_format($d->subtotal_harga / max($d->jumlah,1), 0, ',', '.') }}</td>
                  <td>{{ $d->jumlah }}</td>
                  <td>Rp {{ number_format($d->subtotal_harga, 0, ',', '.') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</x-app-layout>
