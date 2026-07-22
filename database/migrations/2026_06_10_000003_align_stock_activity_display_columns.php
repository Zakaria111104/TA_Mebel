<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $activityTables = [
        'barang_masuk',
        'barang_keluar',
        'barang_hilang',
    ];

    public function up(): void
    {
        foreach ($this->activityTables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'waktu')) {
                    $table->timestamp('waktu')->nullable()->after('id_mutasi_stok');
                }

                if (! Schema::hasColumn($tableName, 'barang')) {
                    $table->string('barang')->nullable()->after('waktu');
                }

                if (! Schema::hasColumn($tableName, 'input_oleh')) {
                    $table->string('input_oleh')->nullable()->after('id_pengguna');
                }
            });

            $this->backfillDisplayColumns($tableName);
        }
    }

    public function down(): void
    {
        foreach ($this->activityTables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'input_oleh')) {
                    $table->dropColumn('input_oleh');
                }

                if (Schema::hasColumn($tableName, 'barang')) {
                    $table->dropColumn('barang');
                }

                if (Schema::hasColumn($tableName, 'waktu')) {
                    $table->dropColumn('waktu');
                }
            });
        }
    }

    private function backfillDisplayColumns(string $tableName): void
    {
        DB::statement("
            UPDATE {$tableName} aktivitas
            LEFT JOIN barang b ON b.id = aktivitas.id_barang
            LEFT JOIN users u ON u.id = aktivitas.id_pengguna
            SET
                aktivitas.waktu = COALESCE(aktivitas.waktu, aktivitas.dibuat_pada),
                aktivitas.barang = COALESCE(aktivitas.barang, b.nama),
                aktivitas.input_oleh = COALESCE(aktivitas.input_oleh, u.name)
        ");
    }
};
