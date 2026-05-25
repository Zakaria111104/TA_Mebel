<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user:reset-password {email} {password}', function (string $email, string $password) {
    $user = User::where('email', $email)->first();

    if (! $user) {
        $this->error("Tidak ada user dengan email: {$email}");

        return 1;
    }

    $user->update(['password' => Hash::make($password)]);
    $this->info("Password untuk [{$email}] berhasil diatur ulang.");

    return 0;
})->purpose('Atur ulang password user lewat CLI (pemulihan jika admin tidak bisa login ke web)');
