<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Kategori;
use App\Models\Location; // ✅ TAMBAHAN
use App\Models\TipeTiket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * INDEX
     */
    public function index()
    {
        $events = Event::with(['location', 'kategori'])->get();
        return view('admin.event.index', compact('events'));
    }

    /**
     * CREATE
     */
    public function create()
    {
        $categories = Kategori::all();
        $locations  = Location::all(); // ✅ TAMBAHAN

        return view(
            'admin.event.create',
            compact('categories', 'locations')
        );
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'judul'         => 'required|string|max:255',
            'deskripsi'     => 'required|string',
            'tanggal_waktu' => 'required|date',
            'location_id'   => 'required|exists:locations,id', // ✅ GANTI
            'kategori_id'   => 'required|exists:kategoris,id',
            'gambar'        => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $imageName = time() . '.' . $request->gambar->extension();
            $request->gambar->move(public_path('images/events'), $imageName);
            $validatedData['gambar'] = $imageName;
        }

        $validatedData['user_id'] = Auth::id();

        Event::create($validatedData);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event berhasil ditambahkan.');
    }

    /**
     * SHOW
     */
    public function show(string $id)
    {
        $event = Event::with(['location', 'kategori'])->findOrFail($id);

        $categories = Kategori::all();
        $tickets    = $event->tikets()->with('tipeTiket')->get();
        $tipeTikets = TipeTiket::orderBy('nama')->get();

        return view(
            'admin.event.show',
            compact('event', 'categories', 'tickets', 'tipeTikets')
        );
    }

    /**
     * EDIT
     */
    public function edit(string $id)
    {
        $event      = Event::findOrFail($id);
        $categories = Kategori::all();
        $locations  = Location::all(); // ✅ TAMBAHAN

        return view(
            'admin.event.edit',
            compact('event', 'categories', 'locations')
        );
    }

    /**
     * UPDATE
     */
    public function update(Request $request, string $id)
    {
        $event = Event::findOrFail($id);

        $validatedData = $request->validate([
            'judul'         => 'required|string|max:255',
            'deskripsi'     => 'required|string',
            'tanggal_waktu' => 'required|date',
            'location_id'   => 'required|exists:locations,id', // ✅ GANTI
            'kategori_id'   => 'required|exists:kategoris,id',
            'gambar'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $imageName = time() . '.' . $request->gambar->extension();
            $request->gambar->move(public_path('images/events'), $imageName);
            $validatedData['gambar'] = $imageName;
        }

        $event->update($validatedData);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event berhasil diperbarui.');
    }

    /**
     * DESTROY
     */
    public function destroy(string $id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }
}
