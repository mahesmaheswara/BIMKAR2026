<?php

/**
 * ==========================================================
 * JUDUL  : CategoryController (Manajemen Kategori - Admin)
 * LOKASI : app/Http/Controllers/Admin/CategoryController.php
 * ==========================================================
 *
 * FUNGSI:
 * Controller ini bertanggung jawab untuk menangani proses
 * CRUD (Create, Read, Update, Delete) data Kategori oleh Admin.
 *
 * TUJUAN:
 * - Memberikan admin kemampuan mengelola kategori event
 * - Kategori digunakan sebagai data master dalam sistem
 * - Contoh kategori: Musik, Seminar, Olahraga, dll
 *
 * KAMUS:
 * - Controller : Pengatur logika antara Request, Model, dan View
 * - CRUD       : Create, Read, Update, Delete
 * - Request    : Data input dari user (admin)
 * - Model      : Representasi tabel database
 * - View       : Tampilan antarmuka (Blade)
 */

namespace App\Http\Controllers\Admin;

// Controller dasar Laravel
use App\Http\Controllers\Controller;

// Digunakan untuk menangani data request dari form
use Illuminate\Http\Request;

// Model Kategori (tabel kategori di database)
use App\Models\Kategori;

class CategoryController extends Controller
{
    /**
     * ==================================================
     * [METHOD] index()
     * ==================================================
     * FUNGSI:
     * - Menampilkan seluruh data kategori
     *
     * TUJUAN:
     * - Digunakan oleh admin untuk melihat daftar kategori
     * - Biasanya ditampilkan dalam bentuk tabel
     *
     * TIPE METHOD:
     * - READ (R dalam CRUD)
     */
    public function index()
    {
        // Mengambil seluruh data kategori dari database
        $categories = Kategori::all();

        // Mengirim data kategori ke view admin.category.index
        return view('admin.category.index', compact('categories'));
    }

    /**
     * ==================================================
     * [METHOD] create()
     * ==================================================
     * FUNGSI:
     * - Menampilkan form tambah kategori
     *
     * CATATAN:
     * - Saat ini belum digunakan
     * - Bisa diisi jika ingin halaman form terpisah
     */
    public function create()
    {
        //
    }

    /**
     * ==================================================
     * [METHOD] store(Request $request)
     * ==================================================
     * FUNGSI:
     * - Menyimpan kategori baru ke database
     *
     * TUJUAN:
     * - Digunakan saat admin menambahkan kategori baru
     *
     * TIPE METHOD:
     * - CREATE (C dalam CRUD)
     */
    public function store(Request $request)
    {
        /**
         * ----------------------------------------------
         * VALIDASI INPUT
         * ----------------------------------------------
         * - nama wajib diisi
         * - harus berupa string
         * - maksimal 255 karakter
         */
        $payload = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        /**
         * VALIDASI TAMBAHAN (DEFENSIVE PROGRAMMING)
         * ----------------------------------------------
         * Meskipun validate() sudah menangani error,
         * pengecekan ini memastikan data benar-benar ada
         */
        if (!isset($payload['nama'])) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'Nama kategori wajib diisi.');
        }

        /**
         * ----------------------------------------------
         * SIMPAN DATA KE DATABASE
         * ----------------------------------------------
         * Menggunakan mass assignment
         * Field 'nama' harus ada di $fillable Model Kategori
         */
        Kategori::create([
            'nama' => $payload['nama'],
        ]);

        /**
         * ----------------------------------------------
         * REDIRECT & FLASH MESSAGE
         * ----------------------------------------------
         * - Kembali ke halaman daftar kategori
         * - Menampilkan pesan sukses
         */
        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * ==================================================
     * [METHOD] show(string $id)
     * ==================================================
     * FUNGSI:
     * - Menampilkan detail satu kategori
     *
     * CATATAN:
     * - Tidak digunakan karena kategori bersifat sederhana
     */
    public function show(string $id)
    {
        //
    }

    /**
     * ==================================================
     * [METHOD] edit(string $id)
     * ==================================================
     * FUNGSI:
     * - Menampilkan form edit kategori
     *
     * CATATAN:
     * - Bisa diimplementasikan jika diperlukan
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * ==================================================
     * [METHOD] update(Request $request, string $id)
     * ==================================================
     * FUNGSI:
     * - Memperbarui data kategori yang sudah ada
     *
     * TUJUAN:
     * - Admin dapat mengubah nama kategori
     *
     * TIPE METHOD:
     * - UPDATE (U dalam CRUD)
     */
    public function update(Request $request, string $id)
    {
        /**
         * ----------------------------------------------
         * VALIDASI INPUT
         * ----------------------------------------------
         */
        $payload = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        /**
         * VALIDASI TAMBAHAN
         */
        if (!isset($payload['nama'])) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'Nama kategori wajib diisi.');
        }

        /**
         * ----------------------------------------------
         * AMBIL DATA BERDASARKAN ID
         * ----------------------------------------------
         * - Jika tidak ditemukan, otomatis 404
         */
        $category = Kategori::findOrFail($id);

        /**
         * ----------------------------------------------
         * UPDATE DATA
         * ----------------------------------------------
         */
        $category->nama = $payload['nama'];
        $category->save();

        /**
         * ----------------------------------------------
         * REDIRECT & FLASH MESSAGE
         * ----------------------------------------------
         */
        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * ==================================================
     * [METHOD] destroy(string $id)
     * ==================================================
     * FUNGSI:
     * - Menghapus kategori dari database
     *
     * TUJUAN:
     * - Digunakan jika kategori sudah tidak diperlukan
     *
     * TIPE METHOD:
     * - DELETE (D dalam CRUD)
     */
    public function destroy(string $id)
    {
        /**
         * ----------------------------------------------
         * HAPUS DATA KATEGORI
         * ----------------------------------------------
         */
        Kategori::destroy($id);

        /**
         * ----------------------------------------------
         * REDIRECT & FLASH MESSAGE
         * ----------------------------------------------
         */
        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
