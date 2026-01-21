<x-layouts.admin title="Edit Tipe Tiket">
    <div class="container mx-auto p-10 max-w-xl">
        <h1 class="text-2xl font-bold mb-6">Edit Tipe Tiket</h1>

        {{-- Error Validation --}}
        @if ($errors->any())
            <div class="alert alert-error mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.tipe-tiket.update', $tipeTiket->id) }}"
              method="POST"
              class="space-y-4">
            @csrf
            @method('PUT')

            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold">Nama Tipe Tiket</span>
                </label>
                <input
                    type="text"
                    name="nama"
                    value="{{ old('nama', $tipeTiket->nama) }}"
                    class="input input-bordered w-full"
                    required
                />
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.tipe-tiket.index') }}" class="btn btn-ghost">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    Update
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
