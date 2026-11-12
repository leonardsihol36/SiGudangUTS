<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    protected $table = 't036_barang';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'satuan',
        'stok',
        'stok_minimum',
        'lokasi_penyimpanan',
        'supplier_id',
        'deskripsi',
    ];

    protected $casts = [
        'stok' => 'integer',
        'stok_minimum' => 'integer',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function mutasi(): HasMany
    {
        return $this->hasMany(MutasiStok::class, 'barang_id');
    }

    public function isStokMenipis(): bool
    {
        return $this->stok <= $this->stok_minimum;
    }

    public function isStokHabis(): bool
    {
        return $this->stok <= 0;
    }
}
