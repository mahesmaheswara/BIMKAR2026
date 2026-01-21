<x-layouts.admin title="Manajemen Tipe Tiket">
    <div class="container mx-auto p-10">
        <h1 class="text-3xl font-bold mb-6">Manajemen Tipe Tiket</h1>

        <a href="{{ route('admin.tipe-tiket.create') }}" class="btn btn-primary mb-4">
            Tambah Tipe Tiket
        </a>

        <div class="bg-white rounded shadow p-4">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Tipe Tiket</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tipeTikets as $i => $tipe)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $tipe->nama }}</td>
                            <td>
                                <a href="{{ route('admin.tipe-tiket.edit', $tipe->id) }}"
                                   class="btn btn-sm btn-warning">Edit</a>

                                <form action="{{ route('admin.tipe-tiket.destroy', $tipe->id) }}"
                                      method="POST"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-error"
                                            onclick="return confirm('Yakin?')">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">
                                Belum ada tipe tiket
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
