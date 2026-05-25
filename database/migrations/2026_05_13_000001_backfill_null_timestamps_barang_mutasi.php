<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Isi kolom timestamp yang NULL tanpa menghapus data,
     * supaya tanggal di web tampil normal lagi (sumber kebenaran: mutasi_stok & barang).
     */
    public function up(): void
    {
        $fallbackBase = Carbon::now();

        if (Schema::hasTable('mutasi_stok') && Schema::hasColumn('mutasi_stok', 'dibuat_pada')) {
            foreach (DB::table('mutasi_stok')->select('id', 'dibuat_pada', 'diperbarui_pada')->lazyById() as $row) {
                $rowDibuat = $row->dibuat_pada;
                $rowDiperbarui = $row->diperbarui_pada;

                if ($rowDibuat !== null && $rowDiperbarui !== null) {
                    continue;
                }

                $fallback = Carbon::parse($fallbackBase)->format('Y-m-d H:i:s');
                $dibuatResolved = $rowDibuat ?? $rowDiperbarui ?? $fallback;
                $diperbaruiResolved = $rowDiperbarui ?? $rowDibuat ?? $fallback;

                DB::table('mutasi_stok')->where('id', $row->id)->update([
                    'dibuat_pada' => $dibuatResolved,
                    'diperbarui_pada' => $diperbaruiResolved,
                ]);
            }
        }

        if (Schema::hasTable('barang') && Schema::hasColumn('barang', 'dibuat_pada')) {
            foreach (DB::table('barang')->select('id', 'dibuat_pada', 'diperbarui_pada')->lazyById() as $row) {
                $rowDibuat = $row->dibuat_pada;
                $rowDiperbarui = $row->diperbarui_pada;

                if ($rowDibuat !== null && $rowDiperbarui !== null) {
                    continue;
                }

                $fallback = Carbon::parse($fallbackBase)->format('Y-m-d H:i:s');
                $dibuatResolved = $rowDibuat ?? $rowDiperbarui ?? $fallback;
                $diperbaruiResolved = $rowDiperbarui ?? $rowDibuat ?? $fallback;

                DB::table('barang')->where('id', $row->id)->update([
                    'dibuat_pada' => $dibuatResolved,
                    'diperbarui_pada' => $diperbaruiResolved,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Tidak dikembalikan: mengosongkan timestamp berisiko rusak konsistensi.
    }
};
