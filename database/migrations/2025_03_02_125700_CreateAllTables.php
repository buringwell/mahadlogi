<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {

           // Tabel Santri
        Schema::create('santris', function (Blueprint $table) {
            $table->id(); // santri_id
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('no_induk_santri')->unique();
            $table->string('nis')->unique();
            $table->string('kelas_sekolah');
            $table->text('alamat');
            $table->date('tanggal_lahir');
            $table->string('no_hp', 15);
            $table->string('nama_ayah');
            $table->string('nama_ibu');
            $table->string('nama_wali')->nullable();
            $table->enum('status', ['aktif', 'alumni'])->default('aktif');
            $table->time('waktu_masuk')->nullable();
            $table->time('waktu_keluar')->nullable();
            $table->timestamps();
        });

        // Tabel Santri Detail
        Schema::create('santri_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->onDelete('cascade');
            $table->date('tanggal_daftar');
            $table->binary('file_foto')->nullable();
            $table->boolean('daftar_ulang')->default(false);
            $table->enum('status', ['aktif', 'tidak aktif'])->default('aktif');
            $table->timestamps();
        });

        // Tabel Ustadz
        Schema::create('ustadzs', function (Blueprint $table) {
            $table->id(); // ustadz_id
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('JK', ['L', 'P']);
            $table->text('alamat');
            $table->string('No_HP', 15);
            $table->string('mata_pelajaran');
            $table->timestamps();
        });

        // Tabel Pembayaran
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id(); // pembayaran_id
            $table->foreignId('santri_id')->constrained('santris')->onDelete('cascade');
            $table->decimal('jumlah', 12, 2);
            $table->date('tanggal');
            $table->string('kode_transaksi')->unique();
            $table->enum('status_pembayaran', ['pending', 'lunas', 'gagal'])->default('pending');
            $table->enum('metode_pembayaran', ['cash', 'transfer', 'qris', 'beasiswa'])->default('cash');
            $table->timestamps();
        });

        // Tabel Pembayaran Detail
        Schema::create('pembayaran_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->constrained('pembayarans')->onDelete('cascade');
            $table->binary('file_transaksi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Tabel Petugas Pembayaran
        Schema::create('petugas_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('alamat');
            $table->string('no_hp', 15);
            $table->timestamps();
        });

        // Tabel Presensi (Absensi)
        Schema::create('presensis', function (Blueprint $table) {
            $table->id(); // presensi_id
            $table->foreignId('santri_id')->constrained('santris')->onDelete('cascade');
            $table->date('tanggal');;
            $table->enum('status', ['hadir', 'izin', 'alfa', 'sakit']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('presensis');
        Schema::dropIfExists('petugas_pembayarans');
        Schema::dropIfExists('pembayaran_details');
        Schema::dropIfExists('pembayarans');
        Schema::dropIfExists('ustadzs');
        Schema::dropIfExists('santri_details');
        Schema::dropIfExists('santris');
        Schema::dropIfExists('users');
    }
};
