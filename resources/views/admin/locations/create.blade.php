<x-layouts.admin title="Tambah Lokasi">
    <div class="container mx-auto p-10 max-w-xl">
        <h1 class="text-3xl font-bold mb-6">Tambah Lokasi</h1>

        {{-- Error validation --}}
        @if ($errors->any())
            <div class="alert alert-error mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded shadow p-6">
            <form action="{{ route('admin.locations.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Nama Lokasi --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            Nama Lokasi
                        </span>
                    </label>
                    <input type="text"
                           name="nama_lokasi"
                           value="{{ old('nama_lokasi') }}"
                           class="input input-bordered w-full"
                           placeholder="Contoh: Gedung Serbaguna A"
                           required>
                </div>

                {{-- Alamat --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            Alamat
                        </span>
                    </label>
                    <textarea name="alamat"
                              rows="3"
                              class="textarea textarea-bordered w-full"
                              placeholder="Jl. Sudirman No. 10, Jakarta">{{ old('alamat') }}</textarea>
                </div>

                {{-- Action --}}
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>

                    <a href="{{ route('admin.locations.index') }}"
                       class="btn btn-ghost">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
