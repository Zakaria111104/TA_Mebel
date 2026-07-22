<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\IncomingStock;
use App\Models\LostStock;
use App\Models\OutgoingStock;
use App\Models\StockMovement;
use App\Models\StockActivity;
use App\Services\WhatsAppStockNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function __construct(private readonly WhatsAppStockNotifier $whatsAppStockNotifier)
    {
    }

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
        $this->ensureAdminOrOwner();

        $query = IncomingStock::with(['product', 'user'])
            ->latest(IncomingStock::CREATED_AT);

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(IncomingStock::CREATED_AT, '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate(IncomingStock::CREATED_AT, '<=', $request->tanggal_selesai);
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
        $this->ensureAdminOrOwner();

        $query = OutgoingStock::with(['product', 'user'])
            ->latest(OutgoingStock::CREATED_AT);

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(OutgoingStock::CREATED_AT, '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate(OutgoingStock::CREATED_AT, '<=', $request->tanggal_selesai);
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
            'movements' => LostStock::with(['product', 'user'])
                ->latest(LostStock::CREATED_AT)
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

        $minimumStockNotificationSent = $this->saveMovement($request, 'keluar');

        if ($minimumStockNotificationSent === true) {
            return redirect()
                ->route('stock-movements.outgoing')
                ->with('success', 'Barang keluar berhasil disimpan dan notifikasi stok minimum berhasil dikirim.');
        }

        if ($minimumStockNotificationSent === false) {
            return redirect()
                ->route('stock-movements.outgoing')
                ->with('success', 'Barang keluar berhasil disimpan.')
                ->with('warning', 'Notifikasi WhatsApp stok minimum gagal dikirim. Pastikan perangkat WhatsApp gateway terhubung.');
        }

        return redirect()->route('stock-movements.outgoing')->with('success', 'Barang keluar berhasil disimpan.');
    }

    public function storeLost(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $minimumStockNotificationSent = $this->saveMovement($request, 'keluar', true);

        if ($minimumStockNotificationSent === true) {
            return redirect()
                ->route('stock-movements.lost')
                ->with('success', 'Input barang hilang berhasil disimpan dan notifikasi stok minimum berhasil dikirim.');
        }

        if ($minimumStockNotificationSent === false) {
            return redirect()
                ->route('stock-movements.lost')
                ->with('success', 'Input barang hilang berhasil disimpan.')
                ->with('warning', 'Notifikasi WhatsApp stok minimum gagal dikirim. Pastikan perangkat WhatsApp gateway terhubung.');
        }

        return redirect()->route('stock-movements.lost')->with('success', 'Input barang hilang berhasil disimpan.');
    }

    private function saveMovement(Request $request, string $type, bool $isLost = false): ?bool
    {
        $rules = [
            'id_barang' => ['required', 'exists:barang,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ];

        if (!$isLost) {
            $rules['kategori'] = ['required', 'string', 'max:100'];
        }

        if ($type === 'masuk') {
            $rules['keterangan'] = ['nullable', 'string', 'max:255'];
        }

        if ($type === 'keluar') {
            $rules['keterangan'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);
        $description = $this->movementDescription($validated, $type, $isLost);

        $minimumStockAlertProduct = DB::transaction(function () use ($validated, $description, $type, $isLost): ?Product {
            $product = Product::lockForUpdate()->findOrFail($validated['id_barang']);

            if (!$isLost && strtolower(trim((string) $product->kategori)) !== strtolower(trim($validated['kategori']))) {
                throw ValidationException::withMessages([
                    'kategori' => 'Kategori yang dipilih tidak sesuai dengan barang.',
                ]);
            }

            $user = Auth::user();
            $waktu = now();
            $oldStock = (int) $product->stok;
            $newStock = $type === 'masuk'
                ? $oldStock + $validated['jumlah']
                : $oldStock - $validated['jumlah'];

            if ($newStock < 0) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Stok tidak mencukupi untuk transaksi keluar.',
                ]);
            }

            $movement = StockMovement::create([
                'id_barang' => $product->id,
                'tipe' => $type,
                'kategori' => $isLost
                    ? StockMovement::KATEGORI_HILANG
                    : ($type === 'masuk' ? StockMovement::KATEGORI_MASUK : StockMovement::KATEGORI_KELUAR),
                'jumlah' => $validated['jumlah'],
                'keterangan' => $description,
                'id_pengguna' => Auth::id(),
            ]);

            $activityModel = $this->activityModel($type, $isLost);

            $activityModel::create([
                'id_barang' => $product->id,
                'id_mutasi_stok' => $movement->id,
                'waktu' => $waktu,
                'barang' => $product->nama,
                'jumlah' => $validated['jumlah'],
                'keterangan' => $description,
                'id_pengguna' => $user?->id,
                'input_oleh' => $user?->name,
            ]);

            $product->update(['stok' => $newStock]);

            $shouldSendMinimumStockAlert = $type === 'keluar'
                && $newStock <= (int) $product->stok_minimum;

            if ($shouldSendMinimumStockAlert) {
                $product->stok = $newStock;

                return $product;
            }

            return null;
        });

        if ($minimumStockAlertProduct !== null) {
            return $this->whatsAppStockNotifier->sendMinimumStockAlert(
                $minimumStockAlertProduct,
                (int) $validated['jumlah']
            );
        }

        return null;
    }

    private function movementDescription(array $validated, string $type, bool $isLost): ?string
    {
        return $validated['keterangan'] ?? null;
    }

    /**
     * @return class-string<StockActivity>
     */
    private function activityModel(string $type, bool $isLost): string
    {
        if ($isLost) {
            return LostStock::class;
        }

        return $type === 'masuk'
            ? IncomingStock::class
            : OutgoingStock::class;
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }

    private function ensureAdminOrOwner(): void
    {
        abort_unless(in_array(auth()->user()?->role, ['admin', 'owner'], true), 403);
    }
}
