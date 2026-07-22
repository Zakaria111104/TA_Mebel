<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasIndonesianTimestamps
{
    public function getCreatedAtColumn(): ?string
    {
        return self::CREATED_AT;
    }

    public function getUpdatedAtColumn(): ?string
    {
        return self::UPDATED_AT;
    }

    protected function createdAt(): Attribute
    {
        return Attribute::get(function () {
            $raw = $this->attributes[self::CREATED_AT] ?? null;

            if ($raw === null || $raw === '') {
                return null;
            }

            return $this->asDateTime($raw);
        });
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::get(function () {
            $raw = $this->attributes[self::UPDATED_AT] ?? null;

            if ($raw === null || $raw === '') {
                return null;
            }

            return $this->asDateTime($raw);
        });
    }

    protected function casts(): array
    {
        return [
            'waktu' => 'datetime',
            self::CREATED_AT => 'datetime',
            self::UPDATED_AT => 'datetime',
        ];
    }
}
