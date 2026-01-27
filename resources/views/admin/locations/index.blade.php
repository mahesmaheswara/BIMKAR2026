<x-layouts.admin title="Manajemen Lokasi">
    <div class="container mx-auto p-10">
        <h1 class="text-3xl font-bold mb-6">Manajemen Lokasi</h1>

        <a href="{{ route('admin.locations.create') }}"
           class="btn btn-primary mb-4">
            Tambah Lokasi
        </a>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded shadow p-4">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lokasi</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($locations as $i => $location)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $location->nama_lokasi }}</td>
                            <td>{{ $location->alamat ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.locations.edit', $location->id) }}"
                                   class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <form action="{{ route('admin.locations.destroy', $location->id) }}"
                                      method="POST"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-error"
                                            onclick="return confirm('Yakin hapus lokasi ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">
                                Belum ada lokasi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
