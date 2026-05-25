<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/', function () {
        return redirect()->route('login');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');

    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/barang-masuk', [StockMovementController::class, 'incoming'])->name('stock-movements.incoming');
    Route::post('/barang-masuk', [StockMovementController::class, 'storeIncoming'])->name('stock-movements.incoming.store');
    Route::get('/barang-keluar', [StockMovementController::class, 'outgoing'])->name('stock-movements.outgoing');
    Route::post('/barang-keluar', [StockMovementController::class, 'storeOutgoing'])->name('stock-movements.outgoing.store');
    Route::get('/barang-hilang', [StockMovementController::class, 'lost'])->name('stock-movements.lost');
    Route::post('/barang-hilang', [StockMovementController::class, 'storeLost'])->name('stock-movements.lost.store');

    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    Route::get('/admin/laporan/pembelian', [ReportController::class, 'pembelian'])->name('reports.pembelian');
    Route::get('/admin/laporan/penjualan', [ReportController::class, 'penjualan'])->name('reports.penjualan');
    Route::get('/admin/laporan/rekap-pembelian', [ReportController::class, 'rekapPembelian'])->name('reports.rekap-pembelian');
    Route::get('/admin/laporan/rekap-penjualan', [ReportController::class, 'rekapPenjualan'])->name('reports.rekap-penjualan');
    Route::get('/admin/laporan/barang-hilang', [ReportController::class, 'barangHilang'])->name('reports.barang-hilang');

    Route::get('/admin/laporan/pembelian/export', [ReportController::class, 'exportPembelian'])->name('reports.pembelian.export');
    Route::get('/admin/laporan/penjualan/export', [ReportController::class, 'exportPenjualan'])->name('reports.penjualan.export');
    Route::get('/admin/laporan/rekap-pembelian/export', [ReportController::class, 'exportRekapPembelian'])->name('reports.rekap-pembelian.export');
    Route::get('/admin/laporan/rekap-penjualan/export', [ReportController::class, 'exportRekapPenjualan'])->name('reports.rekap-penjualan.export');
    Route::get('/admin/laporan/barang-hilang/export', [ReportController::class, 'exportBarangHilang'])->name('reports.barang-hilang.export');
    
    // Backward compatibility: route lama laporan barang
    Route::get('/admin/laporan-barang', function () {
        return redirect()->route('reports.pembelian');
    })->name('reports.stock');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
