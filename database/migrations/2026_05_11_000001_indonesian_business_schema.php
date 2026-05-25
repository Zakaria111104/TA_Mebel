<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyamakan skema domain ke penamaan bahasa Indonesia + tabel tambahan.
     * Urutan: hapus FK mutasi → rename tabel/kolom barang → kolom mutasi → nama tabel mutasi → FK baru → tabel referensi.
     */
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('created_at', 'dibuat_pada');
            $table->renameColumn('updated_at', 'diperbarui_pada');
        });

        Schema::rename('products', 'barang');

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->renameColumn('product_id', 'id_barang');
            $table->renameColumn('user_id', 'id_pengguna');
            $table->renameColumn('created_at', 'dibuat_pada');
            $table->renameColumn('updated_at', 'diperbarui_pada');
        });

        Schema::rename('stock_movements', 'mutasi_stok');

        Schema::table('mutasi_stok', function (Blueprint $table) {
            $table->foreign('id_barang')->references('id')->on('barang')->cascadeOnDelete();
            $table->foreign('id_pengguna')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('kategori_barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150)->unique();
            $table->string('keterangan_ringkas', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_barang');

        Schema::table('mutasi_stok', function (Blueprint $table) {
            $table->dropForeign(['id_barang']);
            $table->dropForeign(['id_pengguna']);
        });

        Schema::rename('mutasi_stok', 'stock_movements');

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->renameColumn('id_barang', 'product_id');
            $table->renameColumn('id_pengguna', 'user_id');
            $table->renameColumn('dibuat_pada', 'created_at');
            $table->renameColumn('diperbarui_pada', 'updated_at');
        });

        Schema::rename('barang', 'products');

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('dibuat_pada', 'created_at');
            $table->renameColumn('diperbarui_pada', 'updated_at');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }
};
