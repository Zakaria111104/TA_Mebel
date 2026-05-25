<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $totalProduk = Product::count();
        $daftarNamaProduk = Product::orderBy('nama')->pluck('nama');
        $stokMenipis = Product::whereColumn('stok', '<=', 'stok_minimum')->count();
        $jumlahBarangMasuk = (int) StockMovement::kategori(StockMovement::KATEGORI_MASUK)->sum('jumlah');
        $jumlahBarangKeluar = (int) StockMovement::kategori(StockMovement::KATEGORI_KELUAR)->sum('jumlah');
        $tz = 'Asia/Jakarta';
        $kolomWaktuMutasi = StockMovement::columnDibuat();
        $awalBulanIni = Carbon::now($tz)->startOfMonth();
        $akhirBulanIni = Carbon::now($tz)->endOfMonth();
        $awalBulanLalu = Carbon::now($tz)->subMonthNoOverflow()->startOfMonth();
        $akhirBulanLalu = Carbon::now($tz)->subMonthNoOverflow()->endOfMonth();
        $jumlahBarangMasukBulanIni = (int) StockMovement::kategori(StockMovement::KATEGORI_MASUK)
            ->whereBetween($kolomWaktuMutasi, [$awalBulanIni, $akhirBulanIni])
            ->sum('jumlah');
        $jumlahBarangMasukBulanLalu = (int) StockMovement::kategori(StockMovement::KATEGORI_MASUK)
            ->whereBetween($kolomWaktuMutasi, [$awalBulanLalu, $akhirBulanLalu])
            ->sum('jumlah');
        $jumlahBarangKeluarBulanIni = (int) StockMovement::kategori(StockMovement::KATEGORI_KELUAR)
            ->whereBetween($kolomWaktuMutasi, [$awalBulanIni, $akhirBulanIni])
            ->sum('jumlah');
        $jumlahBarangKeluarBulanLalu = (int) StockMovement::kategori(StockMovement::KATEGORI_KELUAR)
            ->whereBetween($kolomWaktuMutasi, [$awalBulanLalu, $akhirBulanLalu])
            ->sum('jumlah');
        $perubahanBarangMasuk = $jumlahBarangMasukBulanIni - $jumlahBarangMasukBulanLalu;
        $perubahanBarangKeluar = $jumlahBarangKeluarBulanIni - $jumlahBarangKeluarBulanLalu;
        $labelBulanPerbandinganPenjualan = $awalBulanIni->copy()->translatedFormat('F Y');
        $produkStokMenipis = Product::whereColumn('stok', '<=', 'stok_minimum')
            ->orderBy('stok')
            ->get();
        $aktivitasTerbaru = StockMovement::with('product')
            ->latest(StockMovement::columnDibuat())
            ->limit(6)
            ->get();
        $stokAman = max(0, $totalProduk - $stokMenipis);
        $persentaseStokAman = $totalProduk > 0 ? (int) round(($stokAman / $totalProduk) * 100) : 100;
        $topPergerakanProduk = StockMovement::with('product')
            ->selectRaw('id_barang, SUM(jumlah) as total')
            ->groupBy('id_barang')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        $perbandinganProdukTerjual = StockMovement::with('product')
            ->selectRaw('id_barang, SUM(jumlah) as total_terjual')
            ->kategori(StockMovement::KATEGORI_KELUAR)
            ->whereBetween($kolomWaktuMutasi, [$awalBulanIni, $akhirBulanIni])
            ->groupBy('id_barang')
            ->havingRaw('SUM(jumlah) > 0')
            ->orderByDesc('total_terjual')
            ->get();
        $produkTercepatTerjual = $perbandinganProdukTerjual->first();
        $produkTerlambatTerjual = $perbandinganProdukTerjual->sortBy('total_terjual')->first();
        $selisihProdukTerjual = ($produkTercepatTerjual && $produkTerlambatTerjual)
            ? (int) $produkTercepatTerjual->total_terjual - (int) $produkTerlambatTerjual->total_terjual
            : 0;
        $batasSelisihProdukMirip = 5;
        $produkCepatTerjualLainnya = collect();
        $produkLambatTerjualLainnya = collect();
        if ($produkTercepatTerjual) {
            $totalTercepat = (int) $produkTercepatTerjual->total_terjual;
            $produkCepatTerjualLainnya = $perbandinganProdukTerjual
                ->filter(function ($item) use ($produkTercepatTerjual, $totalTercepat, $batasSelisihProdukMirip): bool {
                    return $item->id_barang !== $produkTercepatTerjual->id_barang
                        && ((int) $item->total_terjual >= $totalTercepat - $batasSelisihProdukMirip);
                })
                ->take(4)
                ->values();
        }
        if ($produkTerlambatTerjual) {
            $totalTerlambat = (int) $produkTerlambatTerjual->total_terjual;
            $produkLambatTerjualLainnya = $perbandinganProdukTerjual
                ->sortBy('total_terjual')
                ->filter(function ($item) use ($produkTerlambatTerjual, $totalTerlambat, $batasSelisihProdukMirip): bool {
                    return $item->id_barang !== $produkTerlambatTerjual->id_barang
                        && ((int) $item->total_terjual <= $totalTerlambat + $batasSelisihProdukMirip);
                })
                ->take(4)
                ->values();
        }
        $tahunDipilih = (int) $request->input('tahun', Carbon::now('Asia/Jakarta')->year);
        $jenisData = $request->input('jenis_data', 'semua');
        if (!in_array($jenisData, ['semua', 'masuk', 'keluar'], true)) {
            $jenisData = 'semua';
        }
        $dataPertama = StockMovement::oldest($kolomWaktuMutasi)->value($kolomWaktuMutasi);
        $dataTerakhir = StockMovement::latest($kolomWaktuMutasi)->value($kolomWaktuMutasi);
        $tahunAwal = $dataPertama ? Carbon::parse($dataPertama)->timezone('Asia/Jakarta')->year : $tahunDipilih;
        $tahunAkhir = $dataTerakhir ? Carbon::parse($dataTerakhir)->timezone('Asia/Jakarta')->year : $tahunDipilih;
        $daftarTahun = collect(range($tahunAkhir, $tahunAwal));

        if ($daftarTahun->isEmpty()) {
            $daftarTahun = collect([$tahunDipilih]);
        } elseif (!$daftarTahun->contains($tahunDipilih)) {
            $daftarTahun = $daftarTahun->push($tahunDipilih)->sortDesc()->values();
        }

        $labelBulan = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
        $trenTahunan = collect(range(1, 12))->map(function (int $bulan) use ($tahunDipilih, $labelBulan, $tz, $kolomWaktuMutasi) {
            $mulaiBulan = Carbon::create($tahunDipilih, $bulan, 1, 0, 0, 0, $tz)->startOfMonth();
            $akhirBulan = Carbon::create($tahunDipilih, $bulan, 1, 0, 0, 0, $tz)->endOfMonth();

            return [
                'label' => $labelBulan[$bulan],
                'masuk' => (int) StockMovement::kategori(StockMovement::KATEGORI_MASUK)
                    ->whereBetween($kolomWaktuMutasi, [$mulaiBulan, $akhirBulan])
                    ->sum('jumlah'),
                'keluar' => (int) StockMovement::kategori(StockMovement::KATEGORI_KELUAR)
                    ->whereBetween($kolomWaktuMutasi, [$mulaiBulan, $akhirBulan])
                    ->sum('jumlah'),
            ];
        });
        $totalMasukTahunan = (int) $trenTahunan->sum('masuk');
        $totalKeluarTahunan = (int) $trenTahunan->sum('keluar');
        $totalArusTahunan = $totalMasukTahunan + $totalKeluarTahunan;
        if ($totalArusTahunan === 0) {
            $persenMasukTahunan = 0;
            $persenKeluarTahunan = 0;
        } else {
            $persenMasukTahunan = (int) round(($totalMasukTahunan / $totalArusTahunan) * 100);
            $persenKeluarTahunan = 100 - $persenMasukTahunan;
        }

        $nilaiMasukPerBulan = $trenTahunan->pluck('masuk')->all();
        $nilaiKeluarPerBulan = $trenTahunan->pluck('keluar')->all();


        $pctMasukKontribTahunan = $totalMasukTahunan > 0
            ? self::distribusiPersenProporsionalTerhadapTarget($nilaiMasukPerBulan, $totalMasukTahunan, $persenMasukTahunan)
            : array_fill(0, 12, null);
        $pctKeluarKontribTahunan = $totalKeluarTahunan > 0
            ? self::distribusiPersenProporsionalTerhadapTarget($nilaiKeluarPerBulan, $totalKeluarTahunan, $persenKeluarTahunan)
            : array_fill(0, 12, null);

        $maksTrenTahunan = match ($jenisData) {
            'masuk' => max(1, (int) $trenTahunan->max('masuk')),
            'keluar' => max(1, (int) $trenTahunan->max('keluar')),
            default => max(1, (int) $trenTahunan->max(fn($item) => max($item['masuk'], $item['keluar']))),
        };

        return view('dashboard', [
            'user' => Auth::user(),
            'totalProduk' => $totalProduk,
            'daftarNamaProduk' => $daftarNamaProduk,
            'stokMenipis' => $stokMenipis,
            'jumlahBarangMasuk' => $jumlahBarangMasuk,
            'jumlahBarangKeluar' => $jumlahBarangKeluar,
            'jumlahBarangMasukBulanLalu' => $jumlahBarangMasukBulanLalu,
            'jumlahBarangKeluarBulanLalu' => $jumlahBarangKeluarBulanLalu,
            'perubahanBarangMasuk' => $perubahanBarangMasuk,
            'perubahanBarangKeluar' => $perubahanBarangKeluar,
            'produkStokMenipis' => $produkStokMenipis,
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'persentaseStokAman' => $persentaseStokAman,
            'topPergerakanProduk' => $topPergerakanProduk,
            'produkTercepatTerjual' => $produkTercepatTerjual,
            'produkTerlambatTerjual' => $produkTerlambatTerjual,
            'produkCepatTerjualLainnya' => $produkCepatTerjualLainnya,
            'produkLambatTerjualLainnya' => $produkLambatTerjualLainnya,
            'batasSelisihProdukMirip' => $batasSelisihProdukMirip,
            'selisihProdukTerjual' => $selisihProdukTerjual,
            'labelBulanPerbandinganPenjualan' => $labelBulanPerbandinganPenjualan,
            'tahunDipilih' => $tahunDipilih,
            'daftarTahun' => $daftarTahun,
            'trenTahunan' => $trenTahunan,
            'maksTrenTahunan' => $maksTrenTahunan,
            'totalMasukTahunan' => $totalMasukTahunan,
            'totalKeluarTahunan' => $totalKeluarTahunan,
            'jenisData' => $jenisData,
            'persenMasukTahunan' => $persenMasukTahunan,
            'persenKeluarTahunan' => $persenKeluarTahunan,
            'pctMasukKontribTahunan' => $pctMasukKontribTahunan,
            'pctKeluarKontribTahunan' => $pctKeluarKontribTahunan,
        ]);
    }

    /**
     * Bagi angka pembulatan $targetPersen ke tiap kolom secara proporsional terhadap
     * total jenis datanya ($totalJenis) agar jumlah hasil tepat sama dengan $targetPersen.
     *
     * @param  array<int, int|float>  $nilaiPerBulan
     * @return array<int, int|null>|array<int, int>
     */
    private static function distribusiPersenProporsionalTerhadapTarget(array $nilaiPerBulan, int $totalJenis, int $targetPersen): array
    {
        if ($totalJenis <= 0) {
            return array_map(fn() => null, $nilaiPerBulan);
        }

        if ($targetPersen <= 0) {
            return array_map(fn() => 0, $nilaiPerBulan);
        }

        $raw = array_map(fn($v) => ((float) $v / $totalJenis) * $targetPersen, $nilaiPerBulan);
        $out = array_map(fn($v) => (int) floor($v), $raw);
        $sisa = $targetPersen - array_sum($out);
        $urutan = array_keys($nilaiPerBulan);
        usort($urutan, function (int $a, int $b) use ($raw): int {
            $fa = $raw[$a] - floor($raw[$a]);
            $fb = $raw[$b] - floor($raw[$b]);

            return $fb <=> $fa;
        });
        $jumlahKolom = count($urutan);
        for ($i = 0; $i < $sisa; $i++) {
            $out[$urutan[$i % $jumlahKolom]]++;
        }

        return $out;
    }
}
