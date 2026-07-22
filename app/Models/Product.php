<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Product extends Model
{
    use HasFactory;

    protected $table = 'barang';

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'stok',
        'stok_minimum',
        'keterangan',
    ];

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'id_barang');
    }

    public static function columnDibuat(): string
    {
        $table = (new static)->getTable();

        return Schema::hasColumn($table, self::CREATED_AT) ? self::CREATED_AT : 'created_at';
    }

    public static function columnDiperbarui(): string
    {
        $table = (new static)->getTable();

        return Schema::hasColumn($table, self::UPDATED_AT) ? self::UPDATED_AT : 'updated_at';
    }

    public function getCreatedAtColumn(): ?string
    {
        return static::columnDibuat();
    }

    public function getUpdatedAtColumn(): ?string
    {
        return static::columnDiperbarui();
    }

    protected function createdAt(): Attribute
    {
        return Attribute::get(function () {
            $col = static::columnDibuat();
            $raw = $this->attributes[$col] ?? null;

            if ($raw === null || $raw === '') {
                return null;
            }

            return $this->asDateTime($raw);
        });
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::get(function () {
            $col = static::columnDiperbarui();
            $raw = $this->attributes[$col] ?? null;

            if ($raw === null || $raw === '') {
                return null;
            }

            return $this->asDateTime($raw);
        });
    }

    protected function casts(): array
    {
        return [
            'dibuat_pada' => 'datetime',
            'diperbarui_pada' => 'datetime',
        ];
    }
}
