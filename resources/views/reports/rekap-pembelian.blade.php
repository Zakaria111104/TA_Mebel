@extends('layouts.sidebar')

@section('title', 'Rekap Pembelian')
@section('active_menu', 'reports-rekap-pembelian')

@section('content')
    @include('reports._nav', ['activeReport' => 'rekap-pembelian'])
    @include('reports._filter', ['action' => route('reports.rekap-pembelian')])

    <div class="table-card">
        <div style="font-size:14px; color:#64748b;">Total Qty Pembelian</div>
        <div style="font-size:30px; color:#14532d; font-weight:800;">{{ $total }}</div>
    </div>

    <div class="table-card export-actions">
        <a href="{{ route('reports.rekap-pembelian.export', array_merge(request()->query(), ['format' => 'excel'])) }}"
            class="export-link excel">
            Export Excel
        </a>
        <a href="{{ route('reports.rekap-pembelian.export', array_merge(request()->query(), ['format' => 'pdf'])) }}"
            class="export-link pdf">
            Export PDF
        </a>
    </div>

    <div class="table-card">
        <h2>Rekap Pembelian per Barang</h2>
        <table class="app-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Barang</th>
                    <th>Kategori</th>
                    <th>Stok Saat Ini</th>
                    <th>Total Qty</th>
                    <th>Aktivitas</th>
                    <th>Rata-rata</th>
                    <th>Qty Terkecil</th>
                    <th>Qty Terbesar</th>
                    <th>Aktivitas Pertama</th>
                    <th>Aktivitas Terakhir</th>
                    <th>Input Oleh</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr>
                        <td>{{ $item->product->kode ?? '-' }}</td>
                        <td>{{ $item->product->nama ?? '-' }}</td>
                        <td>{{ $item->product->kategori ?? '-' }}</td>
                        <td>{{ (int) ($item->product->stok ?? 0) }}</td>
                        <td>{{ (int) $item->total_jumlah }}</td>
                        <td>{{ (int) $item->total_transaksi }}</td>
                        <td>{{ number_format((float) $item->rata_rata, 2, ',', '.') }}</td>
                        <td>{{ (int) $item->jumlah_terkecil }}</td>
                        <td>{{ (int) $item->jumlah_terbesar }}</td>
                        <td>{{ $item->aktivitas_pertama?->timezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '-' }}</td>
                        <td>{{ $item->aktivitas_terakhir?->timezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '-' }}</td>
                        <td>{{ $item->input_oleh }}</td>
                        <td>{{ $item->keterangan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13">Tidak ada data rekap pembelian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
