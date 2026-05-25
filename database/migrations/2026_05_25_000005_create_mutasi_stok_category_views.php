<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->createCategoryView('barang_masuk', 'masuk');
        $this->createCategoryView('barang_keluar', 'keluar');
        $this->createCategoryView('barang_hilang', 'hilang');
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS barang_hilang');
        DB::statement('DROP VIEW IF EXISTS barang_keluar');
        DB::statement('DROP VIEW IF EXISTS barang_masuk');
    }

    private function createCategoryView(string $viewName, string $category): void
    {
        DB::statement("DROP VIEW IF EXISTS {$viewName}");

        DB::statement("
            CREATE VIEW {$viewName} AS
            SELECT
                ms.id,
                ms.id_barang,
                b.kode AS kode_barang,
                b.nama AS nama_barang,
                ms.tipe,
                ms.kategori,
                ms.jumlah,
                ms.keterangan,
                ms.id_pengguna,
                u.name AS nama_pengguna,
                ms.dibuat_pada,
                ms.diperbarui_pada
            FROM mutasi_stok ms
            LEFT JOIN barang b ON b.id = ms.id_barang
            LEFT JOIN users u ON u.id = ms.id_pengguna
            WHERE ms.kategori = '{$category}'
        ");
    }
};
