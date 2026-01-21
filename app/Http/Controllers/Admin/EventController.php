<?php

/**
 * ==========================================================
 * JUDUL  : EventController (Manajemen Event - Admin)
 * LOKASI : app/Http/Controllers/Admin/EventController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini menangani proses CRUD Event oleh Admin.
 *
 * UPDATE SISTEM:
 * - Ditambahkan integrasi dengan TipeTiket (dinamis)
 * - Admin dapat mengelola tiket event dengan tipe tiket dari master data
 *
 * CATATAN:
 * - Tidak mengubah struktur event
 * - Tidak mengganggu flow order & checkout
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Kategori;
use App\Models\TipeTiket; // 🔹 TAMBAHAN
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * ==================================================
     * INDEX
     * ==================================================
     * Menampilkan daftar event
     */
    public function index()
    {
        $events = Event::all();
        return view('admin.event.index', compact('events'));
    }

    /**
     * ==================================================
     * CREATE
     * ==================================================
     * Menampilkan form tambah event
     */
    public function create()
    {
        $categories = Kategori::all();
        return view('admin.event.create', compact('categories'));
    }

    /**
     * ==================================================
     * STORE
     * ==================================================
     * Menyimpan event baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'judul'         => 'required|string|max:255',
            'deskripsi'     => 'required|string',
            'tanggal_waktu' => 'required|date',
            'lokasi'        => 'required|string|max:255',
            'kategori_id'   => 'required|exists:kategoris,id',
            'gambar'        => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $imageName = time() . '.' . $request->gambar->extension();
            $request->gambar->move(public_path('images/events'), $imageName);

            $validatedData['gambar']  = $imageName;
            $validatedData['user_id'] = Auth::user()->id;
        }

        Event::create($validatedData);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event berhasil ditambahkan.');
    }

    /**
     * ==================================================
     * SHOW (UPDATED)
     * ==================================================
     * Menampilkan detail event + tiket + tipe tiket
     *
     * 🔹 UPDATE:
     * - Menambahkan data TipeTiket
     * - Digunakan untuk dropdown tambah/edit tiket
     */
    public function show(string $id)
    {
        $event = Event::findOrFail($id);

        $categories = Kategori::all();

        // Tiket milik event
        $tickets = $event->tikets()->with('tipeTiket')->get();

        // 🔹 MASTER DATA TIPE TIKET (DINAMIS)
        $tipeTikets = TipeTiket::orderBy('nama')->get();

        return view(
            'admin.event.show',
            compact('event', 'categories', 'tickets', 'tipeTikets')
        );
    }

    /**
     * ==================================================
     * EDIT
     * ==================================================
     * Menampilkan form edit event
     */
    public function edit(string $id)
    {
        $event = Event::findOrFail($id);
        $categories = Kategori::all();

        return view('admin.event.edit', compact('event', 'categories'));
    }

    /**
     * ==================================================
     * UPDATE
     * ==================================================
     * Memperbarui data event
     */
    public function update(Request $request, string $id)
    {
        try {
            $event = Event::findOrFail($id);

            $validatedData = $request->validate([
                'judul'         => 'required|string|max:255',
                'deskripsi'     => 'required|string',
                'tanggal_waktu' => 'required|date',
                'lokasi'        => 'required|string|max:255',
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
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors([
                    'error' => 'Terjadi kesalahan saat memperbarui event: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * ==================================================
     * DESTROY
     * ==================================================
     * Menghapus event
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
