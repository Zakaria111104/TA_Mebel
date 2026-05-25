<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    protected $table = 'kategori_barang';

    protected $fillable = [
        'nama',
        'keterangan_ringkas',
    ];
}
