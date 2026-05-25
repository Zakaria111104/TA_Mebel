<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class StockMovement extends Model
{
    use HasFactory;

    protected $table = 'mutasi_stok';

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    public const KATEGORI_MASUK = 'masuk';

    public const KATEGORI_KELUAR = 'keluar';

    public const KATEGORI_HILANG = 'hilang';

    protected $fillable = [
        'id_barang',
        'tipe',
        'kategori',
        'jumlah',
        'keterangan',
        'id_pengguna',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_barang');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }

    /** Nama kolom waktu rekaman sesuai tabel aktual di database. */
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

    public static function hasKategoriColumn(): bool
    {
        return Schema::hasColumn((new static)->getTable(), 'kategori');
    }

    public function scopeKategori(Builder $query, string $kategori): Builder
    {
        if (static::hasKategoriColumn()) {
            return $query->where('kategori', $kategori);
        }

        return match ($kategori) {
            self::KATEGORI_MASUK => $query->where('tipe', 'masuk'),
            self::KATEGORI_HILANG => $query->where('tipe', 'keluar')->where(function ($query) {
                $query->whereRaw("LOWER(COALESCE(keterangan, '')) LIKE ?", ['%[hilang]%'])
                    ->orWhereRaw("LOWER(COALESCE(keterangan, '')) LIKE ?", ['%hilang%'])
                    ->orWhereRaw("LOWER(COALESCE(keterangan, '')) LIKE ?", ['%kehilangan%']);
            }),
            default => $query->where('tipe', 'keluar')->where(function ($query) {
                $query->whereNull('keterangan')
                    ->orWhereRaw("LOWER(COALESCE(keterangan, '')) NOT LIKE ?", ['%hilang%'])
                    ->whereRaw("LOWER(COALESCE(keterangan, '')) NOT LIKE ?", ['%kehilangan%']);
            }),
        };
    }

    public function getCreatedAtColumn(): ?string
    {
        return static::columnDibuat();
    }

    public function getUpdatedAtColumn(): ?string
    {
        return static::columnDiperbarui();
    }

    /**
     * Blade memakai $model->created_at; kolom DB adalah dibuat_pada.
     * Tanpa accessor ini, getAttributeFromArray('created_at') selalu null.
     */
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
