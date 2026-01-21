<?php

/**
 * ==========================================================
 * JUDUL  : TiketController (Manajemen Tiket Event - Admin)
 * LOKASI : app/Http/Controllers/Admin/TiketController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini menangani pengelolaan data Tiket
 * yang terikat langsung dengan sebuah Event.
 *
 * UPDATE SISTEM:
 * - ENUM `tipe` DIHAPUS
 * - Diganti dengan relasi ke tabel `tipe_tikets`
 *
 * TUJUAN:
 * - Admin dapat menambahkan tiket ke event
 * - Admin dapat memilih tipe tiket secara DINAMIS
 * - Admin dapat mengubah harga dan stok tiket
 * - Admin dapat menghapus tiket
 *
 * RELASI DATA:
 * - Tiket belongsTo Event
 * - Tiket belongsTo TipeTiket
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Http\Request;

class TiketController extends Controller
{
    /**
     * ==================================================
     * INDEX
     * ==================================================
     * Tidak digunakan secara terpisah.
     * Tiket ditampilkan di halaman detail Event.
     */
    public function index()
    {
        //
    }

    /**
     * ==================================================
     * CREATE
     * ==================================================
     * Tidak digunakan.
     * Form tiket tersedia di halaman Event.
     */
    public function create()
    {
        //
    }

    /**
     * ==================================================
     * STORE
     * ==================================================
     * Menyimpan tiket baru ke database
     *
     * INPUT:
     * - event_id
     * - tipe_tiket_id
     * - harga
     * - stok
     */
    public function store(Request $request)
    {
        /**
         * ----------------------------------------------
         * VALIDASI INPUT
         * ----------------------------------------------
         */
        $validatedData = $request->validate([
            'event_id'       => 'required|exists:events,id',
            'tipe_tiket_id'  => 'required|exists:tipe_tikets,id',
            'harga'          => 'required|numeric|min:0',
            'stok'           => 'required|integer|min:0',
        ]);

        /**
         * ----------------------------------------------
         * SIMPAN TIKET
         * ----------------------------------------------
         */
        Tiket::create($validatedData);

        /**
         * ----------------------------------------------
         * REDIRECT KE DETAIL EVENT
         * ----------------------------------------------
         */
        return redirect()
            ->route('admin.events.show', $validatedData['event_id'])
            ->with('success', 'Ticket berhasil ditambahkan.');
    }

    /**
     * ==================================================
     * SHOW
     * ==================================================
     * Tidak digunakan.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * ==================================================
     * EDIT
     * ==================================================
     * Tidak digunakan.
     * Edit dilakukan via modal di halaman Event.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * ==================================================
     * UPDATE
     * ==================================================
     * Memperbarui data tiket
     *
     * INPUT:
     * - tipe_tiket_id
     * - harga
     * - stok
     */
    public function update(Request $request, string $id)
    {
        /**
         * ----------------------------------------------
         * AMBIL DATA TIKET
         * ----------------------------------------------
         */
        $ticket = Tiket::findOrFail($id);

        /**
         * ----------------------------------------------
         * VALIDASI INPUT
         * ----------------------------------------------
         */
        $validatedData = $request->validate([
            'tipe_tiket_id' => 'required|exists:tipe_tikets,id',
            'harga'         => 'required|numeric|min:0',
            'stok'          => 'required|integer|min:0',
        ]);

        /**
         * ----------------------------------------------
         * UPDATE TIKET
         * ----------------------------------------------
         */
        $ticket->update($validatedData);

        /**
         * ----------------------------------------------
         * REDIRECT KE DETAIL EVENT
         * ----------------------------------------------
         */
        return redirect()
            ->route('admin.events.show', $ticket->event_id)
            ->with('success', 'Ticket berhasil diperbarui.');
    }

    /**
     * ==================================================
     * DESTROY
     * ==================================================
     * Menghapus tiket dari database
     */
    public function destroy(string $id)
    {
        /**
         * ----------------------------------------------
         * AMBIL DATA TIKET
         * ----------------------------------------------
         */
        $ticket = Tiket::findOrFail($id);

        $eventId = $ticket->event_id;

        /**
         * ----------------------------------------------
         * HAPUS TIKET
         * ----------------------------------------------
         */
        $ticket->delete();

        /**
         * ----------------------------------------------
         * REDIRECT KE DETAIL EVENT
         * ----------------------------------------------
         */
        return redirect()
            ->route('admin.events.show', $eventId)
            ->with('success', 'Ticket berhasil dihapus.');
    }
}
