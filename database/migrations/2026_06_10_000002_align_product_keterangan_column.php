<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('barang')) {
            return;
        }

        if (Schema::hasColumn('barang', 'deskripsi') && ! Schema::hasColumn('barang', 'keterangan')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->renameColumn('deskripsi', 'keterangan');
            });

            return;
        }

        if (! Schema::hasColumn('barang', 'keterangan')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->text('keterangan')->nullable();
            });
        }

        if (Schema::hasColumn('barang', 'deskripsi') && Schema::hasColumn('barang', 'keterangan')) {
            DB::table('barang')
                ->whereNull('keterangan')
                ->whereNotNull('deskripsi')
                ->update(['keterangan' => DB::raw('deskripsi')]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('barang')) {
            return;
        }

        if (Schema::hasColumn('barang', 'keterangan') && ! Schema::hasColumn('barang', 'deskripsi')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->renameColumn('keterangan', 'deskripsi');
            });
        }
    }
};
