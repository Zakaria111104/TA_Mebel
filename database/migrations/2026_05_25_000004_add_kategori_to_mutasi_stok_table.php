<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('mutasi_stok') || Schema::hasColumn('mutasi_stok', 'kategori')) {
            return;
        }

        Schema::table('mutasi_stok', function (Blueprint $table) {
            $table->string('kategori', 30)->nullable()->after('tipe')->index();
        });

        DB::table('mutasi_stok')
            ->where('tipe', 'masuk')
            ->update(['kategori' => 'masuk']);

        DB::table('mutasi_stok')
            ->where('tipe', 'keluar')
            ->where(function ($query) {
                $query->whereRaw("LOWER(COALESCE(keterangan, '')) LIKE ?", ['%[hilang]%'])
                    ->orWhereRaw("LOWER(COALESCE(keterangan, '')) LIKE ?", ['%hilang%'])
                    ->orWhereRaw("LOWER(COALESCE(keterangan, '')) LIKE ?", ['%kehilangan%']);
            })
            ->update(['kategori' => 'hilang']);

        DB::table('mutasi_stok')
            ->where('tipe', 'keluar')
            ->whereNull('kategori')
            ->update(['kategori' => 'keluar']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('mutasi_stok') || ! Schema::hasColumn('mutasi_stok', 'kategori')) {
            return;
        }

        Schema::table('mutasi_stok', function (Blueprint $table) {
            $table->dropIndex(['kategori']);
            $table->dropColumn('kategori');
        });
    }
};
