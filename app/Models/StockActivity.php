<?php

namespace App\Models;

use App\Models\Concerns\HasIndonesianTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

abstract class StockActivity extends Model
{
    use HasIndonesianTimestamps;

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'id_barang',
        'id_mutasi_stok',
        'waktu',
        'barang',
        'jumlah',
        'keterangan',
        'id_pengguna',
        'input_oleh',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_barang');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }

    public function stockMovement(): BelongsTo
    {
        return $this->belongsTo(StockMovement::class, 'id_mutasi_stok');
    }
}
