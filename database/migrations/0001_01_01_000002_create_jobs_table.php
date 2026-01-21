<?php

/**
 * ==========================================================
 * JUDUL  : Migration Queue / Jobs Tables
 * LOKASI : database/migrations/0001_01_01_000002_create_jobs_table.php
 * ==========================================================
 *
 * FUNGSI:
 * Migration ini membuat tabel-tabel yang digunakan
 * oleh sistem QUEUE (antrian pekerjaan) Laravel.
 *
 * TUJUAN:
 * - Menyimpan job yang dijalankan secara asynchronous
 * - Mendukung batch job
 * - Menyimpan riwayat job yang gagal
 *
 * CATATAN PENTING:
 * - Ini adalah migration BAWAAN Laravel
 * - Digunakan jika queue driver = database
 * - Tidak langsung terkait fitur ticketing
 *
 * KAMUS UMUM:
 * - Job        : Tugas background (email, notifikasi, dll)
 * - Queue      : Antrian pekerjaan
 * - Batch      : Sekumpulan job
 * - Failed Job : Job yang gagal dieksekusi
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ==================================================
     * [METHOD] up()
     * ==================================================
     * FUNGSI:
     * - Membuat seluruh tabel queue
     * - Dieksekusi saat migrate
     */
    public function up(): void
    {
        /**
         * ==============================================
         * TABEL: jobs
         * ==============================================
         * Menyimpan job yang sedang menunggu
         * atau sedang diproses
         */
        Schema::create('jobs', function (Blueprint $table) {

            // Primary key
            $table->id();

            /**
             * Nama queue
             * Contoh: default, emails, notifications
             */
            $table->string('queue')->index();

            /**
             * Payload job
             * Berisi data serialized job
             */
            $table->longText('payload');

            /**
             * Jumlah percobaan eksekusi job
             */
            $table->unsignedTinyInteger('attempts');

            /**
             * Waktu job di-reserve worker
             * NULL jika belum diproses
             */
            $table->unsignedInteger('reserved_at')
                  ->nullable();

            /**
             * Waktu job siap dijalankan
             */
            $table->unsignedInteger('available_at');

            /**
             * Waktu job dibuat
             */
            $table->unsignedInteger('created_at');
        });

        /**
         * ==============================================
         * TABEL: job_batches
         * ==============================================
         * Menyimpan informasi batch job
         */
        Schema::create('job_batches', function (Blueprint $table) {

            // ID batch (UUID)
            $table->string('id')->primary();

            // Nama batch
            $table->string('name');

            // Total job dalam batch
            $table->integer('total_jobs');

            // Job yang belum selesai
            $table->integer('pending_jobs');

            // Job yang gagal
            $table->integer('failed_jobs');

            /**
             * ID job yang gagal
             * Disimpan dalam format serialized
             */
            $table->longText('failed_job_ids');

            /**
             * Opsi tambahan batch (JSON)
             */
            $table->mediumText('options')
                  ->nullable();

            // Waktu batch dibatalkan
            $table->integer('cancelled_at')
                  ->nullable();

            // Waktu batch dibuat
            $table->integer('created_at');

            // Waktu batch selesai
            $table->integer('finished_at')
                  ->nullable();
        });

        /**
         * ==============================================
         * TABEL: failed_jobs
         * ==============================================
         * Menyimpan job yang gagal dieksekusi
         */
        Schema::create('failed_jobs', function (Blueprint $table) {

            // Primary key
            $table->id();

            // UUID job (unik)
            $table->string('uuid')->unique();

            // Koneksi queue (database, redis, dll)
            $table->text('connection');

            // Nama queue
            $table->text('queue');

            // Payload job
            $table->longText('payload');

            // Exception / error detail
            $table->longText('exception');

            // Waktu job gagal
            $table->timestamp('failed_at')
                  ->useCurrent();
        });
    }

    /**
     * ==================================================
     * [METHOD] down()
     * ==================================================
     * FUNGSI:
     * - Menghapus seluruh tabel queue
     * - Digunakan saat rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
