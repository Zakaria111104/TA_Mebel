<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Samakan struktur kolom waktu antara database dan model Laravel:
     * - Jika kedua bahasa (created_at & dibuat_pada) ikut ada, salin dari Inggris ke Indonesia lalu hapus kolom Inggris.
     * - Jika hanya Inggris, rename ke Indonesia.
     * - Isi NULL terakhir (aman untuk MySQL maupun SQLite saat tes).
     */
    public function up(): void
    {
        foreach (['mutasi_stok', 'barang'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $hasId = Schema::hasColumn($table, 'dibuat_pada');
            $hasEn = Schema::hasColumn($table, 'created_at');

            if ($hasEn && $hasId) {
                foreach (DB::table($table)->select('id', 'dibuat_pada', 'diperbarui_pada', 'created_at', 'updated_at')->lazyById() as $row) {
                    $fallback = Carbon::now()->format('Y-m-d H:i:s');

                    $dibuatResolved = $row->dibuat_pada ?? $row->created_at ?? $fallback;
                    $diperbaruiResolved = $row->diperbarui_pada ?? $row->updated_at ?? $row->created_at ?? $fallback;

                    DB::table($table)->where('id', $row->id)->update([
                        'dibuat_pada' => $dibuatResolved,
                        'diperbarui_pada' => $diperbaruiResolved,
                    ]);
                }

                Schema::table($table, function (Blueprint $bp): void {
                    $bp->dropColumn(['created_at', 'updated_at']);
                });
            }

            $hasId = Schema::hasColumn($table, 'dibuat_pada');
            $hasEn = Schema::hasColumn($table, 'created_at');

            if ($hasEn && ! $hasId) {
                Schema::table($table, function (Blueprint $bp): void {
                    $bp->renameColumn('created_at', 'dibuat_pada');
                    $bp->renameColumn('updated_at', 'diperbarui_pada');
                });
            }

            if (! Schema::hasColumn($table, 'dibuat_pada')) {
                continue;
            }

            $fallbackBase = Carbon::now();
            foreach (DB::table($table)->select('id', 'dibuat_pada', 'diperbarui_pada')->lazyById() as $row) {
                if ($row->dibuat_pada !== null && $row->diperbarui_pada !== null) {
                    continue;
                }

                $fallback = Carbon::parse($fallbackBase)->format('Y-m-d H:i:s');

                DB::table($table)->where('id', $row->id)->update([
                    'dibuat_pada' => $row->dibuat_pada ?? $row->diperbarui_pada ?? $fallback,
                    'diperbarui_pada' => $row->diperbarui_pada ?? $row->dibuat_pada ?? $fallback,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Tidak dibalik untuk menjaga konsistensi data.
    }
};
