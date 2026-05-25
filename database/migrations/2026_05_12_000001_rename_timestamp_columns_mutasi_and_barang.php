<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyelaraskan nama kolom waktu dengan model (dibuat_pada / diperbarui_pada)
     * bila database masih memakai created_at / updated_at pada tabel mutasi_stok atau barang.
     */
    public function up(): void
    {
        if (Schema::hasTable('mutasi_stok')) {
            if (Schema::hasColumn('mutasi_stok', 'created_at')
                && ! Schema::hasColumn('mutasi_stok', 'dibuat_pada')) {
                Schema::table('mutasi_stok', function (Blueprint $table) {
                    $table->renameColumn('created_at', 'dibuat_pada');
                    $table->renameColumn('updated_at', 'diperbarui_pada');
                });
            }
        }

        if (Schema::hasTable('barang')) {
            if (Schema::hasColumn('barang', 'created_at')
                && ! Schema::hasColumn('barang', 'dibuat_pada')) {
                Schema::table('barang', function (Blueprint $table) {
                    $table->renameColumn('created_at', 'dibuat_pada');
                    $table->renameColumn('updated_at', 'diperbarui_pada');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('mutasi_stok')) {
            if (Schema::hasColumn('mutasi_stok', 'dibuat_pada')
                && ! Schema::hasColumn('mutasi_stok', 'created_at')) {
                Schema::table('mutasi_stok', function (Blueprint $table) {
                    $table->renameColumn('dibuat_pada', 'created_at');
                    $table->renameColumn('diperbarui_pada', 'updated_at');
                });
            }
        }

        if (Schema::hasTable('barang')) {
            if (Schema::hasColumn('barang', 'dibuat_pada')
                && ! Schema::hasColumn('barang', 'created_at')) {
                Schema::table('barang', function (Blueprint $table) {
                    $table->renameColumn('dibuat_pada', 'created_at');
                    $table->renameColumn('diperbarui_pada', 'updated_at');
                });
            }
        }
    }
};
