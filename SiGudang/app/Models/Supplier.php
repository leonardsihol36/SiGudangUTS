<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 't036_supplier';

    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'nama_kontak',
        'no_telepon',
        'email',
        'alamat',
        'status',
    ];

    public function barang(): HasMany
    {
        return $this->hasMany(Barang::class, 'supplier_id');
    }
}
