<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS barang_masuk');
        DB::statement('DROP VIEW IF EXISTS barang_keluar');
        DB::statement('DROP VIEW IF EXISTS barang_hilang');

        $this->createActivityTable('barang_masuk');
        $this->createActivityTable('barang_keluar');
        $this->createActivityTable('barang_hilang');

        $this->copyMutasiToActivityTable('barang_masuk', 'masuk');
        $this->copyMutasiToActivityTable('barang_keluar', 'keluar');
        $this->copyMutasiToActivityTable('barang_hilang', 'hilang');
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_hilang');
        Schema::dropIfExists('barang_keluar');
        Schema::dropIfExists('barang_masuk');

        $this->createCategoryView('barang_masuk', 'masuk');
        $this->createCategoryView('barang_keluar', 'keluar');
        $this->createCategoryView('barang_hilang', 'hilang');
    }

    private function createActivityTable(string $tableName): void
    {
        if (Schema::hasTable($tableName)) {
            return;
        }

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')->constrained('barang')->cascadeOnDelete();
            $table->foreignId('id_mutasi_stok')->nullable()->unique()->constrained('mutasi_stok')->nullOnDelete();
            $table->timestamp('waktu')->nullable();
            $table->string('barang')->nullable();
            $table->unsignedInteger('jumlah');
            $table->string('keterangan')->nullable();
            $table->foreignId('id_pengguna')->nullable()->constrained('users')->nullOnDelete();
            $table->string('input_oleh')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });
    }

    private function copyMutasiToActivityTable(string $tableName, string $kategori): void
    {
        if (! Schema::hasTable('mutasi_stok') || ! Schema::hasTable($tableName)) {
            return;
        }

        DB::statement("
            INSERT INTO {$tableName}
                (id_mutasi_stok, id_barang, waktu, barang, jumlah, keterangan, id_pengguna, input_oleh, dibuat_pada, diperbarui_pada)
            SELECT
                ms.id,
                ms.id_barang,
                ms.dibuat_pada,
                b.nama,
                ms.jumlah,
                ms.keterangan,
                ms.id_pengguna,
                u.name,
                ms.dibuat_pada,
                ms.diperbarui_pada
            FROM mutasi_stok ms
            LEFT JOIN barang b ON b.id = ms.id_barang
            LEFT JOIN users u ON u.id = ms.id_pengguna
            WHERE ms.kategori = ?
              AND NOT EXISTS (
                  SELECT 1 FROM {$tableName} target
                  WHERE target.id_mutasi_stok = ms.id
              )
        ", [$kategori]);
    }

    private function createCategoryView(string $viewName, string $category): void
    {
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
