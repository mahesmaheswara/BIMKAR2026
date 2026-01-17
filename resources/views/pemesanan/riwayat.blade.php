<x-app-layout>
  <div class="max-w-5xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold mb-6">Riwayat Pemesanan</h1>

    @if($orders->count() === 0)
      <div class="p-4 rounded-lg bg-base-200">
        Kamu belum punya pemesanan.
      </div>
    @else
      <div class="overflow-x-auto bg-base-100 shadow rounded-lg">
        <table class="table">
          <thead>
            <tr>
              <th>Event</th>
              <th>Tanggal</th>
              <th>Jumlah</th>
              <th>Total</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($orders as $order)
              <tr>
                <td class="font-semibold">{{ $order->event->judul ?? '-' }}</td>
                <td>{{ optional($order->order_date)->format('d M Y H:i') }}</td>
                <td>{{ $order->detailOrders->sum('jumlah') }}</td>
                <td>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                <td>
                  <a class="btn btn-sm btn-outline bg-blue-500 text-white"
                     href="{{ route('pemesanan.detail', $order) }}">
                    Detail
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-6">
        {{ $orders->links() }}
      </div>
    @endif
  </div>
</x-app-layout>
