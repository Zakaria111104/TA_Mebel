<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    // public function incoming(): View
    // {
    //     $this->ensureAdmin();

    //     return view('mutasi-stok.barang-masuk', [
    //         'products' => Product::orderBy('nama')->get(),
    //         'movements' => StockMovement::with(['product', 'user'])
    //             ->where('tipe', 'masuk')
    //             ->latest(StockMovement::columnDibuat())
    //             ->limit(50)
    //             ->get(),
    //     ]);
    // }

    public function incoming(Request $request): View
    {
        $this->ensureAdmin();

        $query = StockMovement::with(['product', 'user'])
            ->kategori(StockMovement::KATEGORI_MASUK)
            ->latest(StockMovement::columnDibuat());

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(StockMovement::columnDibuat(), '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate(StockMovement::columnDibuat(), '<=', $request->tanggal_selesai);
        }

        if (!$request->filled('tanggal_mulai') && !$request->filled('tanggal_selesai')) {
            $query->limit(50);
        }

        return view('mutasi-stok.barang-masuk', [
            'products' => Product::orderBy('nama')->get(),
            'movements' => $query->get(),
        ]);
    }

    // public function outgoing(): View
    // {
    //     $this->ensureAdmin();

    //     return view('mutasi-stok.barang-keluar', [
    //         'products' => Product::orderBy('nama')->get(),
    //         'movements' => StockMovement::with(['product', 'user'])
    //             ->where('tipe', 'keluar')
    //             ->latest(StockMovement::columnDibuat())
    //             ->limit(50)
    //             ->get(),
    //     ]);
    // }

    public function outgoing(Request $request): View
    {
        $this->ensureAdmin();

        $query = StockMovement::with(['product', 'user'])
            ->kategori(StockMovement::KATEGORI_KELUAR)
            ->latest(StockMovement::columnDibuat());

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(StockMovement::columnDibuat(), '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate(StockMovement::columnDibuat(), '<=', $request->tanggal_selesai);
        }

        if (!$request->filled('tanggal_mulai') && !$request->filled('tanggal_selesai')) {
            $query->limit(50);
        }

        return view('mutasi-stok.barang-keluar', [
            'products' => Product::orderBy('nama')->get(),
            'movements' => $query->get(),
        ]);
    }

    public function lost(): View
    {
        $this->ensureAdmin();

        return view('mutasi-stok.barang-hilang', [
            'products' => Product::orderBy('nama')->get(),
            'movements' => StockMovement::with(['product', 'user'])
                ->kategori(StockMovement::KATEGORI_HILANG)
                ->latest(StockMovement::columnDibuat())
                ->limit(50)
                ->get(),
        ]);
    }

    public function storeIncoming(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $this->saveMovement($request, 'masuk');

        return redirect()->route('stock-movements.incoming')->with('success', 'Barang masuk berhasil disimpan.');
    }

    public function storeOutgoing(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $this->saveMovement($request, 'keluar');

        return redirect()->route('stock-movements.outgoing')->with('success', 'Barang keluar berhasil disimpan.');
    }

    public function storeLost(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $this->saveMovement($request, 'keluar', true);

        return redirect()->route('stock-movements.lost')->with('success', 'Input barang hilang berhasil disimpan.');
    }

    private function saveMovement(Request $request, string $type, bool $isLost = false): void
    {
        $validated = $request->validate([
            'id_barang' => ['required', 'exists:barang,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $type, $isLost): void {
            $product = Product::lockForUpdate()->findOrFail($validated['id_barang']);
            $newStock = $type === 'masuk'
                ? $product->stok + $validated['jumlah']
                : $product->stok - $validated['jumlah'];

            if ($newStock < 0) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Stok tidak mencukupi untuk transaksi keluar.',
                ]);
            }

            StockMovement::create([
                'id_barang' => $product->id,
                'tipe' => $type,
                'kategori' => $isLost
                    ? StockMovement::KATEGORI_HILANG
                    : ($type === 'masuk' ? StockMovement::KATEGORI_MASUK : StockMovement::KATEGORI_KELUAR),
                'jumlah' => $validated['jumlah'],
                'keterangan' => $isLost
                    ? ($validated['keterangan'] ?? null)
                    : ($validated['keterangan'] ?? null),
                'id_pengguna' => Auth::id(),
            ]);

            $product->update(['stok' => $newStock]);
        });
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }
}
