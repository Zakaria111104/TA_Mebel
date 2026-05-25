<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (Schema::hasTable('kategori_barang')) {
            $now = now();
            foreach (
                ['Kursi', 'Meja', 'Lemari', 'Rak'] as $nama
            ) {
                DB::table('kategori_barang')->updateOrInsert(
                    ['nama' => $nama],
                    [
                        'keterangan_ringkas' => null,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }
        }

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => 'admin123',
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'owner@gmail.com'],
            [
                'name' => 'Owner',
                'password' => 'owner123',
                'role' => 'owner',
            ]
        );
    }
}
