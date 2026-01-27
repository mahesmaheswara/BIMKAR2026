<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::latest()->get();
        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        Location::create([
            'nama_lokasi' => $request->nama_lokasi,
            'alamat' => $request->alamat,
        ]);

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil ditambahkan');
    }

    public function edit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        $location->update([
            'nama_lokasi' => $request->nama_lokasi,
            'alamat' => $request->alamat,
        ]);

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil diperbarui');
    }

    public function destroy(Location $location)
    {
        // 🔒 Proteksi: jangan hapus lokasi yang masih dipakai event
        if ($location->events()->exists()) {
            return redirect()
                ->route('admin.locations.index')
                ->with('error', 'Lokasi masih digunakan oleh event');
        }

        $location->delete();

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil dihapus');
    }
}
