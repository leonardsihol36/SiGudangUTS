<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t036_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 50)->unique();
            $table->string('nama_barang', 150);
            $table->string('kategori', 100);
            $table->string('satuan', 50);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(10);
            $table->string('lokasi_penyimpanan', 100);
            $table->foreignId('supplier_id')->constrained('t036_supplier')->onDelete('restrict');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t036_barang');
    }
};
