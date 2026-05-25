@extends('layouts.sidebar')

@section('title', 'Laporan Stok')
@section('active_menu', 'reports')

@section('content')
    <style>
        .report-table {
            table-layout: fixed;
        }
    </style>

    <div class="table-card">
        <h2>Filter Laporan</h2>
        <form method="GET" action="{{ route('reports.stock') }}"
            style="display:grid; grid-template-columns: 1fr 1fr 1fr auto auto; gap:10px; align-items:end;">
            <div>
                <label for="jenis_laporan">Jenis Laporan</label>
                <select id="jenis_laporan" name="jenis_laporan">
                    <option value="pembelian" @selected($jenisLaporan === 'pembelian')>Laporan Pembelian</option>
                    <option value="penjualan" @selected($jenisLaporan === 'penjualan')>Laporan Penjualan</option>
                    <option value="rekap_pembelian" @selected($jenisLaporan === 'rekap_pembelian')>Rekap Pembelian</option>
                    <option value="rekap_penjualan" @selected($jenisLaporan === 'rekap_penjualan')>Rekap Penjualan</option>
                    <option value="barang_hilang" @selected($jenisLaporan === 'barang_hilang')>Laporan Barang Hilang</option>
                </select>
            </div>
            <div>
                <label for="tanggal_mulai">Tanggal Mulai</label>
                <input id="tanggal_mulai" type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}">
            </div>
            <div>
                <label for="tanggal_selesai">Tanggal Selesai</label>
                <input id="tanggal_selesai" type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}">
            </div>
            <button type="submit">Terapkan</button>
            <a href="{{ route('reports.stock') }}"
                style="display:inline-block; padding:9px 10px; border-radius:7px; border:1px solid #cbd5e1; text-decoration:none; color:#1f2937; background:#fff;">
                Reset
            </a>
        </form>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
        <div style="border:1px solid #dbe2ea; border-radius:12px; background:#fff; padding:16px;">
            <div style="font-size:14px; color:#64748b;">Total Pembelian</div>
            <div style="font-size:30px; color:#14532d; font-weight:800;">{{ $totalPembelian }}</div>
        </div>
        <div style="border:1px solid #dbe2ea; border-radius:12px; background:#fff; padding:16px;">
            <div style="font-size:14px; color:#64748b;">Total Penjualan</div>
            <div style="font-size:30px; color:#14532d; font-weight:800;">{{ $totalPenjualan }}</div>
        </div>
    </div>

    @if ($jenisLaporan === 'pembelian')
    <div class="table-card">
        <h2>Laporan Pembelian</h2>
        <table class="app-table report-table">
            <colgroup>
                <col style="width: 18%;">
                <col style="width: 24%;">
                <col style="width: 12%;">
                <col style="width: 20%;">
                <col style="width: 26%;">
            </colgroup>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>User</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pembelian as $item)
                    <tr>
                        <td>{{ $item->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>{{ $item->product->nama ?? '-' }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Tidak ada data pembelian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    @if ($jenisLaporan === 'penjualan')
    <div class="table-card">
        <h2>Laporan Penjualan</h2>
        <table class="app-table report-table">
            <colgroup>
                <col style="width: 18%;">
                <col style="width: 24%;">
                <col style="width: 12%;">
                <col style="width: 20%;">
                <col style="width: 26%;">
            </colgroup>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>User</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penjualan as $item)
                    <tr>
                        <td>{{ $item->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>{{ $item->product->nama ?? '-' }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Tidak ada data penjualan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    @if ($jenisLaporan === 'rekap_pembelian')
    <div class="table-card">
        <h2>Rekap Pembelian per Produk</h2>
        <table class="app-table report-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Total Qty</th>
                    <th>Total Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekapPembelian as $item)
                    <tr>
                        <td>{{ $item->product->nama ?? '-' }}</td>
                        <td>{{ (int) $item->total_jumlah }}</td>
                        <td>{{ (int) $item->total_transaksi }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Tidak ada data rekap pembelian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    @if ($jenisLaporan === 'rekap_penjualan')
    <div class="table-card">
        <h2>Rekap Penjualan per Produk</h2>
        <table class="app-table report-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Total Qty</th>
                    <th>Total Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekapPenjualan as $item)
                    <tr>
                        <td>{{ $item->product->nama ?? '-' }}</td>
                        <td>{{ (int) $item->total_jumlah }}</td>
                        <td>{{ (int) $item->total_transaksi }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Tidak ada data rekap penjualan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    @if ($jenisLaporan === 'barang_hilang')
    <div style="display:grid; grid-template-columns:1fr; gap:16px; margin-bottom:16px;">
        <div style="border:1px solid #dbe2ea; border-radius:12px; background:#fff; padding:16px;">
            <div style="font-size:14px; color:#64748b;">Total Barang Hilang</div>
            <div style="font-size:30px; color:#b91c1c; font-weight:800;">{{ $totalHilang }}</div>
        </div>
    </div>

    <div class="table-card">
        <h2>Laporan Barang Hilang</h2>
        <div style="font-size:13px; color:#64748b; margin-bottom:10px;">
            Data diambil dari transaksi barang keluar dengan keterangan mengandung kata "hilang" atau "kehilangan".
        </div>
        <table class="app-table report-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>User</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barangHilang as $item)
                    <tr>
                        <td>{{ $item->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>{{ $item->product->nama ?? '-' }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Tidak ada data barang hilang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
@endsection
