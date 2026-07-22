<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppStockNotifier
{
    public function sendMinimumStockAlert(Product $product, ?int $outgoingQuantity = null): bool
    {
        $targets = config('services.whatsapp.target_numbers', []);
        $token = config('services.whatsapp.token');
        $endpoint = config('services.whatsapp.endpoint');

        if ($targets === [] || !$token || !$endpoint) {
            Log::info('Notifikasi stok minimum WhatsApp belum dikirim karena konfigurasi belum lengkap.', [
                'product_id' => $product->id,
                'product_name' => $product->nama,
            ]);

            return false;
        }

        $message = $this->minimumStockMessage($product, $outgoingQuantity);
        $allSent = true;

        foreach ($targets as $target) {
            try {
                $response = Http::timeout(10)->withHeaders([
                    'Authorization' => $token,
                ])->asForm()->post($endpoint, [
                    'target' => $target,
                    'message' => $message,
                ]);
                $responseData = $response->json();

                if (!$response->successful() || ($responseData !== null && ($responseData['status'] ?? true) === false)) {
                    Log::warning('Notifikasi stok minimum WhatsApp gagal dikirim.', [
                        'product_id' => $product->id,
                        'target' => $target,
                        'status' => $response->status(),
                        'response' => $response->body(),
                    ]);

                    $allSent = false;
                } else {
                    Log::info('Notifikasi stok minimum WhatsApp berhasil dikirim.', [
                        'product_id' => $product->id,
                        'product_name' => $product->nama,
                        'target' => $target,
                        'outgoing_quantity' => $outgoingQuantity,
                        'stock' => $product->stok,
                        'minimum_stock' => $product->stok_minimum,
                    ]);
                }
            } catch (\Throwable $exception) {
                Log::warning('Notifikasi stok minimum WhatsApp gagal diproses.', [
                    'product_id' => $product->id,
                    'target' => $target,
                    'error' => $exception->getMessage(),
                ]);

                $allSent = false;
            }
        }

        return $allSent;
    }

    public function minimumStockMessage(Product $product, ?int $outgoingQuantity = null): string
    {
        $lines = [
            'Peringatan Stok Minimum',
            '',
            "Barang: {$product->nama}",
        ];

        if ($outgoingQuantity !== null) {
            $lines[] = "Stok keluar: {$outgoingQuantity} unit";
        }

        return implode("\n", [
            ...$lines,
            "Stok saat ini: {$product->stok} unit",
            "Stok minimum: {$product->stok_minimum} unit",
            '',
            'Mohon segera lakukan restock barang.',
        ]);
    }
}
