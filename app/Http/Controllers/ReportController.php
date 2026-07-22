<?php

namespace App\Http\Controllers;

use App\Models\LostStock;
use App\Models\StockMovement;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class ReportController extends Controller
{
    public function pembelian(Request $request): View
    {
        $this->ensureReportAccess();
        [$tanggalMulai, $tanggalSelesai] = $this->resolveTanggal($request);

        $data = $this->detailMutasi(StockMovement::KATEGORI_MASUK, $tanggalMulai, $tanggalSelesai);

        return view('reports.pembelian', [
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'data' => $data,
            'total' => (int) $data->sum('jumlah'),
        ]);
    }

    public function penjualan(Request $request): View
    {
        $this->ensureReportAccess();
        [$tanggalMulai, $tanggalSelesai] = $this->resolveTanggal($request);

        $data = $this->detailMutasi(StockMovement::KATEGORI_KELUAR, $tanggalMulai, $tanggalSelesai);

        return view('reports.penjualan', [
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'data' => $data,
            'total' => (int) $data->sum('jumlah'),
        ]);
    }

    public function rekapPembelian(Request $request): View
    {
        $this->ensureReportAccess();
        [$tanggalMulai, $tanggalSelesai] = $this->resolveTanggal($request);

        $data = $this->rekapMutasi(StockMovement::KATEGORI_MASUK, $tanggalMulai, $tanggalSelesai);

        return view('reports.rekap-pembelian', [
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'data' => $data,
            'total' => (int) $data->sum('total_jumlah'),
        ]);
    }

    public function rekapPenjualan(Request $request): View
    {
        $this->ensureReportAccess();
        [$tanggalMulai, $tanggalSelesai] = $this->resolveTanggal($request);

        $data = $this->rekapMutasi(StockMovement::KATEGORI_KELUAR, $tanggalMulai, $tanggalSelesai);

        return view('reports.rekap-penjualan', [
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'data' => $data,
            'total' => (int) $data->sum('total_jumlah'),
        ]);
    }

    public function barangHilang(Request $request): View
    {
        $this->ensureReportAccess();
        [$tanggalMulai, $tanggalSelesai] = $this->resolveTanggal($request);

        $data = $this->detailBarangHilang($tanggalMulai, $tanggalSelesai);

        return view('reports.barang-hilang', [
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'data' => $data,
            'total' => (int) $data->sum('jumlah'),
        ]);
    }

    public function exportPembelian(Request $request)
    {
        $this->ensureReportAccess();
        [$tanggalMulai, $tanggalSelesai] = $this->resolveTanggal($request);

        $data = $this->detailMutasi(StockMovement::KATEGORI_MASUK, $tanggalMulai, $tanggalSelesai);

        return $this->exportReport(
            $request,
            'Laporan Pembelian',
            $data,
            ['Waktu', 'Barang', 'Jumlah', 'Keterangan', 'Input Oleh'],
            fn ($item) => [
                $item->created_at?->timezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '-',
                $item->product->nama ?? '-',
                $item->jumlah,
                $item->keterangan ?? '-',
                $item->user->name ?? '-',
            ],
            'laporan-pembelian-' . now()->format('YmdHis')
        );
    }

    public function exportPenjualan(Request $request)
    {
        $this->ensureReportAccess();
        [$tanggalMulai, $tanggalSelesai] = $this->resolveTanggal($request);

        $data = $this->detailMutasi(StockMovement::KATEGORI_KELUAR, $tanggalMulai, $tanggalSelesai);

        return $this->exportReport(
            $request,
            'Laporan Penjualan',
            $data,
            ['Waktu', 'Barang', 'Jumlah', 'Keterangan', 'Input Oleh'],
            fn ($item) => [
                $item->created_at?->timezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '-',
                $item->product->nama ?? '-',
                $item->jumlah,
                $item->keterangan ?? '-',
                $item->user->name ?? '-',
            ],
            'laporan-penjualan-' . now()->format('YmdHis')
        );
    }

    public function exportRekapPembelian(Request $request)
    {
        $this->ensureReportAccess();
        [$tanggalMulai, $tanggalSelesai] = $this->resolveTanggal($request);

        $data = $this->rekapMutasi(StockMovement::KATEGORI_MASUK, $tanggalMulai, $tanggalSelesai);

        return $this->exportReport(
            $request,
            'Rekap Pembelian',
            $data,
            ['Kode', 'Barang', 'Kategori', 'Stok Saat Ini', 'Total Qty', 'Aktivitas', 'Rata-rata', 'Qty Terkecil', 'Qty Terbesar', 'Aktivitas Pertama', 'Aktivitas Terakhir', 'Input Oleh', 'Keterangan'],
            fn ($item) => [
                $item->product->kode ?? '-',
                $item->product->nama ?? '-',
                $item->product->kategori ?? '-',
                (int) ($item->product->stok ?? 0),
                (int) $item->total_jumlah,
                (int) $item->total_transaksi,
                number_format((float) $item->rata_rata, 2, ',', '.'),
                (int) $item->jumlah_terkecil,
                (int) $item->jumlah_terbesar,
                $item->aktivitas_pertama?->timezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '-',
                $item->aktivitas_terakhir?->timezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '-',
                $item->input_oleh,
                $item->keterangan,
            ],
            'rekap-pembelian-' . now()->format('YmdHis')
        );
    }

    public function exportRekapPenjualan(Request $request)
    {
        $this->ensureReportAccess();
        [$tanggalMulai, $tanggalSelesai] = $this->resolveTanggal($request);

        $data = $this->rekapMutasi(StockMovement::KATEGORI_KELUAR, $tanggalMulai, $tanggalSelesai);

        return $this->exportReport(
            $request,
            'Rekap Penjualan',
            $data,
            ['Kode', 'Barang', 'Kategori', 'Stok Saat Ini', 'Total Qty', 'Aktivitas', 'Rata-rata', 'Qty Terkecil', 'Qty Terbesar', 'Aktivitas Pertama', 'Aktivitas Terakhir', 'Input Oleh', 'Keterangan'],
            fn ($item) => [
                $item->product->kode ?? '-',
                $item->product->nama ?? '-',
                $item->product->kategori ?? '-',
                (int) ($item->product->stok ?? 0),
                (int) $item->total_jumlah,
                (int) $item->total_transaksi,
                number_format((float) $item->rata_rata, 2, ',', '.'),
                (int) $item->jumlah_terkecil,
                (int) $item->jumlah_terbesar,
                $item->aktivitas_pertama?->timezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '-',
                $item->aktivitas_terakhir?->timezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '-',
                $item->input_oleh,
                $item->keterangan,
            ],
            'rekap-penjualan-' . now()->format('YmdHis')
        );
    }

    public function exportBarangHilang(Request $request)
    {
        $this->ensureReportAccess();
        [$tanggalMulai, $tanggalSelesai] = $this->resolveTanggal($request);

        $data = $this->detailBarangHilang($tanggalMulai, $tanggalSelesai);

        return $this->exportReport(
            $request,
            'Laporan Barang Hilang',
            $data,
            ['Tanggal', 'Produk', 'Jumlah', 'User', 'Keterangan'],
            fn ($item) => [
                $item->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-',
                $item->barang ?? $item->product->nama ?? '-',
                $item->jumlah,
                $item->input_oleh ?? $item->user->name ?? '-',
                $item->keterangan ?? '-',
            ],
            'barang-hilang-' . now()->format('YmdHis')
        );
    }

    private function exportReport(Request $request, string $title, iterable $data, array $columns, callable $rowMapper, string $filenameBase)
    {
        $rows = collect($data)->map($rowMapper)->toArray();

        if ($request->input('format') === 'pdf') {
            return $this->exportPdf($title, $filenameBase . '.pdf', $columns, $rows, [
                'tanggalMulai' => $request->input('tanggal_mulai'),
                'tanggalSelesai' => $request->input('tanggal_selesai'),
            ]);
        }

        return $this->exportExcel($title, $filenameBase . '.xls', $columns, $rows);
    }

    private function detailMutasi(string $kategori, ?string $tanggalMulai, ?string $tanggalSelesai)
    {
        return StockMovement::with(['product', 'user'])
            ->kategori($kategori)
            ->when($tanggalMulai, fn ($q) => $q->where($this->kolomWaktuMutasi(), '>=', $this->mulaiHariDiJakarta($tanggalMulai)))
            ->when($tanggalSelesai, fn ($q) => $q->where($this->kolomWaktuMutasi(), '<=', $this->akhirHariDiJakarta($tanggalSelesai)))
            ->latest(StockMovement::columnDibuat())
            ->get();
    }

    private function detailBarangHilang(?string $tanggalMulai, ?string $tanggalSelesai)
    {
        return LostStock::with(['product', 'user'])
            ->when($tanggalMulai, fn ($q) => $q->where('waktu', '>=', $this->mulaiHariDiJakarta($tanggalMulai)))
            ->when($tanggalSelesai, fn ($q) => $q->where('waktu', '<=', $this->akhirHariDiJakarta($tanggalSelesai)))
            ->latest(LostStock::CREATED_AT)
            ->get();
    }

    private function rekapMutasi(string $kategori, ?string $tanggalMulai, ?string $tanggalSelesai)
    {
        return $this->detailMutasi($kategori, $tanggalMulai, $tanggalSelesai)
            ->groupBy('id_barang')
            ->map(function ($items) {
                $sorted = $items->sortBy(fn ($item) => $item->created_at?->timestamp ?? 0);
                $keterangan = $items->pluck('keterangan')
                    ->filter()
                    ->unique()
                    ->values()
                    ->implode('; ');
                $inputOleh = $items->map(fn ($item) => $item->user?->name)
                    ->filter()
                    ->unique()
                    ->values()
                    ->implode(', ');

                return (object) [
                    'product' => $items->first()->product,
                    'total_jumlah' => (int) $items->sum('jumlah'),
                    'total_transaksi' => $items->count(),
                    'rata_rata' => (float) $items->avg('jumlah'),
                    'jumlah_terkecil' => (int) $items->min('jumlah'),
                    'jumlah_terbesar' => (int) $items->max('jumlah'),
                    'aktivitas_pertama' => $sorted->first()?->created_at,
                    'aktivitas_terakhir' => $sorted->last()?->created_at,
                    'input_oleh' => $inputOleh !== '' ? $inputOleh : '-',
                    'keterangan' => $keterangan !== '' ? $keterangan : '-',
                ];
            })
            ->sortByDesc('total_jumlah')
            ->values();
    }

    private function exportExcel(string $title, string $filename, array $columns, array $rows)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan');

        $lastColumn = Coordinate::stringFromColumnIndex(max(1, count($columns)));
        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach ($columns as $index => $column) {
            $sheet->setCellValueByColumnAndRow($index + 1, 3, $column);
        }

        if ($rows === []) {
            $sheet->mergeCells("A4:{$lastColumn}4");
            $sheet->setCellValue('A4', 'Tidak ada data.');
        } else {
            foreach ($rows as $rowIndex => $row) {
                foreach ($columns as $columnIndex => $column) {
                    $sheet->setCellValueByColumnAndRow(
                        $columnIndex + 1,
                        $rowIndex + 4,
                        $row[$columnIndex] ?? ''
                    );
                }
            }
        }

        $lastRow = max(4, count($rows) + 3);
        $tableRange = "A3:{$lastColumn}{$lastRow}";

        $sheet->getStyle("A3:{$lastColumn}3")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8EEF6'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'B9C0C9'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ]);

        foreach (range(1, count($columns)) as $columnIndex) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($columnIndex))->setAutoSize(true);
        }

        $sheet->freezePane('A4');

        ob_start();
        (new Xls($spreadsheet))->save('php://output');
        $content = ob_get_clean();
        $spreadsheet->disconnectWorksheets();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function exportPdf(string $title, string $filename, array $columns, array $rows, array $filters)
    {
        $period = ($filters['tanggalMulai'] || $filters['tanggalSelesai'])
            ? ($filters['tanggalMulai'] ?: '-') . ' s/d ' . ($filters['tanggalSelesai'] ?: '-')
            : 'Semua tanggal';

        $html = view('reports.exports.table', [
            'title' => $title,
            'period' => $period,
            'tanggalMulai' => $filters['tanggalMulai'],
            'tanggalSelesai' => $filters['tanggalSelesai'],
            'printedAt' => now('Asia/Jakarta')->format('d/m/Y H:i'),
            'columns' => $columns,
            'rows' => $rows,
        ])->render();

        try {
            $pdf = new Dompdf();
            $pdf->set_option('defaultFont', 'Arial');
            $pdf->set_option('isHtml5ParserEnabled', true);
            $pdf->set_option('isRemoteEnabled', true);
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'landscape');
            $pdf->render();

            $output = $pdf->output();
        } catch (\Throwable $exception) {
            report($exception);

            $output = $this->buildFallbackPdf($title, $period, $columns, $rows);
        }

        return response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function buildFallbackPdf(string $title, string $period, array $columns, array $rows): string
    {
        if ($rows === []) {
            $rows = [['Tidak ada data.']];
        }

        $pageWidth = 842;
        $pageHeight = 595;
        $margin = 45;
        $tableTop = 470;
        $rowHeight = 28;
        $headerHeight = 30;
        $rowsPerPage = 14;
        $columnWidths = match (count($columns)) {
            3 => [360, 170, 222],
            5 => [135, 180, 75, 110, 252],
            default => array_fill(0, max(1, count($columns)), floor(($pageWidth - ($margin * 2)) / max(1, count($columns)))),
        };
        $pages = array_chunk($rows, $rowsPerPage);
        $objects = [];
        $pageObjectIds = [];
        $nextObjectId = 5;

        foreach ($pages as $pageIndex => $pageRows) {
            $content = '';
            $content .= $this->fallbackPdfText(strtoupper($title), $margin, 545, 15, true);
            $content .= $this->fallbackPdfText('Periode: ' . $period, $margin, 524, 10);
            $content .= $this->fallbackPdfText('Dicetak: ' . now('Asia/Jakarta')->format('d/m/Y H:i'), $margin, 508, 9);

            $x = $margin;
            foreach ($columns as $index => $column) {
                $width = $columnWidths[$index] ?? end($columnWidths);
                $content .= $this->fallbackPdfCell($x, $tableTop, $width, $headerHeight, (string) $column, true);
                $x += $width;
            }

            $y = $tableTop - $rowHeight;
            foreach ($pageRows as $row) {
                $x = $margin;
                foreach ($columns as $index => $column) {
                    $width = $columnWidths[$index] ?? end($columnWidths);
                    $content .= $this->fallbackPdfCell($x, $y, $width, $rowHeight, (string) ($row[$index] ?? ''));
                    $x += $width;
                }
                $y -= $rowHeight;
            }

            $content .= $this->fallbackPdfText('Halaman ' . ($pageIndex + 1) . ' dari ' . count($pages), $pageWidth - 125, 32, 8);

            $contentObjectId = $nextObjectId++;
            $objects[$contentObjectId] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream";

            $pageObjectId = $nextObjectId++;
            $pageObjectIds[] = $pageObjectId;
            $objects[$pageObjectId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . $pageWidth . ' ' . $pageHeight . '] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents ' . $contentObjectId . ' 0 R >>';
        }

        $kids = implode(' ', array_map(fn ($id) => $id . ' 0 R', $pageObjectIds));
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Kids [' . $kids . '] /Count ' . count($pageObjectIds) . ' >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>',
        ] + $objects;
        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        return $pdf . "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF";
    }

    private function fallbackPdfCell(float $x, float $y, float $width, float $height, string $text, bool $header = false): string
    {
        $fontSize = $header ? 9 : 8.5;
        $maxChars = max(6, (int) floor(($width - 12) / ($fontSize * 0.55)));
        $text = strlen($text) > $maxChars ? substr($text, 0, $maxChars - 3) . '...' : $text;

        $content = $header ? "0.90 0.94 0.98 rg\n{$x} {$y} {$width} {$height} re f\n" : '';
        $content .= "0.25 0.30 0.36 RG\n0.6 w\n{$x} {$y} {$width} {$height} re S\n";
        $content .= $this->fallbackPdfText($text, $x + 6, $y + (($height - $fontSize) / 2), $fontSize, $header);

        return $content;
    }

    private function fallbackPdfText(string $text, float $x, float $y, float $fontSize = 10, bool $bold = false): string
    {
        $font = $bold ? 'F2' : 'F1';
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\(', '\)'], $text);

        return "0 0 0 rg\nBT\n/{$font} {$fontSize} Tf\n{$x} {$y} Td\n({$text}) Tj\nET\n";
    }

    private function resolveTanggal(Request $request): array
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        if ($tanggalMulai && !$tanggalSelesai) {
            $tanggalSelesai = $tanggalMulai;
        }

        return [$tanggalMulai, $tanggalSelesai];
    }

    /**
     * Batas tanggal cocok dengan jam di web (hari penuh WIB), tanpa menyimpan sebagai UTC lain.
     */
    private function mulaiHariDiJakarta(string $tanggal): Carbon
    {
        return Carbon::parse($tanggal, 'Asia/Jakarta')->startOfDay();
    }

    private function akhirHariDiJakarta(string $tanggal): Carbon
    {
        return Carbon::parse($tanggal, 'Asia/Jakarta')->endOfDay();
    }

    private function kolomWaktuMutasi(): string
    {
        return StockMovement::columnDibuat();
    }

    private function ensureReportAccess(): void
    {
        abort_unless(in_array(auth()->user()?->role, ['admin', 'owner'], true), 403);
    }
}
