<?php

/**
 * ==========================================================
 * JUDUL  : Migration Auth & User Tables
 * LOKASI : database/migrations/0001_01_01_000000_create_users_table.php
 * ==========================================================
 *
 * FUNGSI:
 * Migration ini bertugas membuat tabel-tabel inti
 * yang berkaitan dengan autentikasi user, yaitu:
 * - users
 * - password_reset_tokens
 * - sessions
 *
 * TUJUAN:
 * - Menyimpan data user
 * - Mendukung fitur login & logout
 * - Mendukung reset password
 * - Menyimpan session user
 *
 * KAMUS UMUM:
 * - Migration : Versi struktur database
 * - Blueprint : Skema tabel
 * - up()      : Membuat tabel
 * - down()    : Menghapus tabel (rollback)
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
     * - Dijalankan saat migrate
     * - Membuat seluruh tabel yang dibutuhkan
     */
    public function up(): void
    {
        /**
         * ==============================================
         * TABEL: users
         * ==============================================
         * Menyimpan data utama user
         */
        Schema::create('users', function (Blueprint $table) {

            // Primary key (auto increment)
            $table->id();

            // Nama lengkap user
            $table->string('name');

            // Email user (unik)
            $table->string('email')->unique();

            // Waktu verifikasi email (nullable)
            $table->timestamp('email_verified_at')
                  ->nullable();

            // Nomor HP user (opsional)
            $table->string('no_hp')
                  ->nullable();

            /**
             * Role user
             * Contoh:
             * - admin
             * - user
             */
            $table->string('role');

            // Password user (terenkripsi)
            $table->string('password');

            /**
             * Remember token
             * Digunakan untuk fitur "remember me"
             */
            $table->rememberToken();

            // created_at & updated_at
            $table->timestamps();
        });

        /**
         * ==============================================
         * TABEL: password_reset_tokens
         * ==============================================
         * Menyimpan token reset password
         */
        Schema::create('password_reset_tokens', function (Blueprint $table) {

            /**
             * Email user sebagai primary key
             * Satu email = satu token aktif
             */
            $table->string('email')->primary();

            // Token reset password
            $table->string('token');

            // Waktu token dibuat
            $table->timestamp('created_at')
                  ->nullable();
        });

        /**
         * ==============================================
         * TABEL: sessions
         * ==============================================
         * Menyimpan session login user
         */
        Schema::create('sessions', function (Blueprint $table) {

            // ID session (primary key)
            $table->string('id')->primary();

            /**
             * Relasi ke user (nullable)
             * Bisa null untuk guest
             */
            $table->foreignId('user_id')
                  ->nullable()
                  ->index();

            // Alamat IP user
            $table->string('ip_address', 45)
                  ->nullable();

            // Informasi browser / device
            $table->text('user_agent')
                  ->nullable();

            /**
             * Payload session
             * Berisi data serialized session
             */
            $table->longText('payload');

            /**
             * Waktu aktivitas terakhir
             * Digunakan untuk expired session
             */
            $table->integer('last_activity')
                  ->index();
        });
    }

    /**
     * ==================================================
     * [METHOD] down()
     * ==================================================
     * FUNGSI:
     * - Dijalankan saat rollback
     * - Menghapus tabel yang dibuat di up()
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
