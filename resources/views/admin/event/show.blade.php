<x-layouts.admin title="Detail Event">
    <div class="container mx-auto p-10">

        {{-- FLASH SUCCESS --}}
        @if (session('success'))
            <div class="toast toast-bottom toast-center z-50">
                <div class="alert alert-success">
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            <script>
                setTimeout(() => document.querySelector('.toast')?.remove(), 3000);
            </script>
        @endif

        {{-- ================= DETAIL EVENT ================= --}}
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6">Detail Event</h2>

                <form class="space-y-4">
                    @csrf

                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Judul Event</span></label>
                        <input type="text" class="input input-bordered w-full" value="{{ $event->judul }}" disabled>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Deskripsi</span></label>
                        <textarea class="textarea textarea-bordered w-full" disabled>{{ $event->deskripsi }}</textarea>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Tanggal & Waktu</span></label>
                        <input type="datetime-local" class="input input-bordered w-full"
                            value="{{ $event->tanggal_waktu->format('Y-m-d\TH:i') }}" disabled>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Lokasi</span></label>
                        <input type="text" class="input input-bordered w-full" value="{{ $event->lokasi }}" disabled>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Kategori</span></label>
                        <select class="select select-bordered w-full" disabled>
                            @foreach ($categories as $category)
                                <option {{ $category->id == $event->kategori_id ? 'selected' : '' }}>
                                    {{ $category->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($event->gambar)
                        <div>
                            <label class="label"><span class="label-text font-semibold">Gambar Event</span></label>
                            <img src="{{ asset('images/events/' . $event->gambar) }}"
                                class="rounded-lg max-w-sm">
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- ================= LIST TICKET ================= --}}
        <div class="mt-10">
            <div class="flex items-center mb-4">
                <h1 class="text-3xl font-semibold">List Ticket</h1>
                <button onclick="add_ticket_modal.showModal()" class="btn btn-primary ml-auto">
                    Tambah Ticket
                </button>
            </div>

            <div class="overflow-x-auto bg-white p-5 rounded-box shadow-xs">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tipe Tiket</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tickets as $i => $ticket)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $ticket->tipeTiket->nama }}</td>
                                <td>Rp {{ number_format($ticket->harga, 0, ',', '.') }}</td>
                                <td>{{ $ticket->stok }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary"
                                        onclick="openEditModal(this)"
                                        data-id="{{ $ticket->id }}"
                                        data-tipe-tiket-id="{{ $ticket->tipe_tiket_id }}"
                                        data-harga="{{ $ticket->harga }}"
                                        data-stok="{{ $ticket->stok }}">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm bg-red-500 text-white"
                                        onclick="openDeleteModal(this)"
                                        data-id="{{ $ticket->id }}">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada ticket.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================= ADD MODAL ================= --}}
    <dialog id="add_ticket_modal" class="modal">
        <form method="POST" action="{{ route('admin.tickets.store') }}" class="modal-box">
            @csrf
            <h3 class="font-bold text-lg mb-4">Tambah Ticket</h3>

            <input type="hidden" name="event_id" value="{{ $event->id }}">

            <div class="form-control mb-3">
                <label class="label">Tipe Ticket</label>
                <select name="tipe_tiket_id" class="select select-bordered" required>
                    <option value="" disabled selected>Pilih Tipe</option>
                    @foreach ($tipeTikets as $tipe)
                        <option value="{{ $tipe->id }}">{{ $tipe->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-control mb-3">
                <label class="label">Harga</label>
                <input type="number" name="harga" class="input input-bordered" required>
            </div>

            <div class="form-control mb-3">
                <label class="label">Stok</label>
                <input type="number" name="stok" class="input input-bordered" required>
            </div>

            <div class="modal-action">
                <button class="btn btn-primary">Simpan</button>
                <button type="reset" class="btn" onclick="add_ticket_modal.close()">Batal</button>
            </div>
        </form>
    </dialog>

    {{-- ================= EDIT MODAL ================= --}}
    <dialog id="edit_ticket_modal" class="modal">
        <form method="POST" class="modal-box">
            @csrf
            @method('PUT')

            <h3 class="font-bold text-lg mb-4">Edit Ticket</h3>

            <div class="form-control mb-3">
                <label class="label">Tipe Ticket</label>
                <select name="tipe_tiket_id" id="edit_tipe_tiket_id" class="select select-bordered" required>
                    @foreach ($tipeTikets as $tipe)
                        <option value="{{ $tipe->id }}">{{ $tipe->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-control mb-3">
                <label class="label">Harga</label>
                <input type="number" name="harga" id="edit_harga" class="input input-bordered" required>
            </div>

            <div class="form-control mb-3">
                <label class="label">Stok</label>
                <input type="number" name="stok" id="edit_stok" class="input input-bordered" required>
            </div>

            <div class="modal-action">
                <button class="btn btn-primary">Update</button>
                <button type="reset" class="btn" onclick="edit_ticket_modal.close()">Batal</button>
            </div>
        </form>
    </dialog>

    {{-- ================= DELETE MODAL ================= --}}
    <dialog id="delete_modal" class="modal">
        <form method="POST" class="modal-box">
            @csrf
            @method('DELETE')
            <h3 class="font-bold text-lg mb-4">Hapus Ticket?</h3>
            <p>Data tidak bisa dikembalikan.</p>
            <div class="modal-action">
                <button class="btn btn-error">Hapus</button>
                <button type="reset" class="btn" onclick="delete_modal.close()">Batal</button>
            </div>
        </form>
    </dialog>

    {{-- ================= SCRIPT ================= --}}
    <script>
        function openEditModal(btn) {
            const form = document.querySelector('#edit_ticket_modal form');
            form.action = `/admin/tickets/${btn.dataset.id}`;
            document.getElementById('edit_tipe_tiket_id').value = btn.dataset.tipeTiketId;
            document.getElementById('edit_harga').value = btn.dataset.harga;
            document.getElementById('edit_stok').value = btn.dataset.stok;
            edit_ticket_modal.showModal();
        }

        function openDeleteModal(btn) {
            const form = document.querySelector('#delete_modal form');
            form.action = `/admin/tickets/${btn.dataset.id}`;
            delete_modal.showModal();
        }
    </script>
</x-layouts.admin>
