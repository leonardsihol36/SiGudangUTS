<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t036_mutasi_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('t036_barang')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('t036_users')->onDelete('restrict');
            $table->enum('tipe_mutasi', ['masuk', 'keluar']);
            $table->integer('jumlah');
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->text('keterangan')->nullable();
            $table->dateTime('tanggal_mutasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t036_mutasi_stok');
    }
};
