<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipeTiket;
use Illuminate\Http\Request;

class TipeTiketController extends Controller
{
    /**
     * ==================================================
     * INDEX
     * ==================================================
     * Menampilkan daftar tipe tiket
     */
    public function index()
    {
        $tipeTikets = TipeTiket::orderBy('nama')->get();

        return view('admin.tipe_tiket.index', compact('tipeTikets'));
    }

    /**
     * ==================================================
     * CREATE
     * ==================================================
     * Menampilkan form tambah tipe tiket
     */
    public function create()
    {
        return view('admin.tipe_tiket.create');
    }

    /**
     * ==================================================
     * STORE
     * ==================================================
     * Menyimpan tipe tiket baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:tipe_tikets,nama',
        ]);

        TipeTiket::create($validated);

        return redirect()
            ->route('admin.tipe-tiket.index')
            ->with('success', 'Tipe tiket berhasil ditambahkan.');
    }

    /**
     * ==================================================
     * EDIT
     * ==================================================
     * Menampilkan form edit tipe tiket
     */
    public function edit(TipeTiket $tipeTiket)
    {
        return view('admin.tipe_tiket.edit', compact('tipeTiket'));
    }

    /**
     * ==================================================
     * UPDATE
     * ==================================================
     * Memperbarui tipe tiket
     */
    public function update(Request $request, TipeTiket $tipeTiket)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:tipe_tikets,nama,' . $tipeTiket->id,
        ]);

        $tipeTiket->update($validated);

        return redirect()
            ->route('admin.tipe-tiket.index')
            ->with('success', 'Tipe tiket berhasil diperbarui.');
    }

    /**
     * ==================================================
     * DESTROY
     * ==================================================
     * Menghapus tipe tiket
     */
    public function destroy(TipeTiket $tipeTiket)
    {
        // 🔒 optional safety (kalau sudah dipakai tiket)
        if ($tipeTiket->tikets()->exists()) {
            return back()->withErrors([
                'error' => 'Tipe tiket tidak dapat dihapus karena masih digunakan.'
            ]);
        }

        $tipeTiket->delete();

        return redirect()
            ->route('admin.tipe-tiket.index')
            ->with('success', 'Tipe tiket berhasil dihapus.');
    }
}
