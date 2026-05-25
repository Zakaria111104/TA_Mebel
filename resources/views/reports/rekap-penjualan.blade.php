@extends('layouts.sidebar')

@section('title', 'Rekap Penjualan')
@section('active_menu', 'reports-rekap-penjualan')

@section('content')
    @include('reports._nav', ['activeReport' => 'rekap-penjualan'])
    @include('reports._filter', ['action' => route('reports.rekap-penjualan')])

    <div class="table-card">
        <div style="font-size:14px; color:#64748b;">Total Qty Penjualan</div>
        <div style="font-size:30px; color:#14532d; font-weight:800;">{{ $total }}</div>
    </div>

    <div class="table-card export-actions">
        <a href="{{ route('reports.rekap-penjualan.export', array_merge(request()->query(), ['format' => 'excel'])) }}"
            class="export-link excel">
            Export Excel
        </a>
        <a href="{{ route('reports.rekap-penjualan.export', array_merge(request()->query(), ['format' => 'pdf'])) }}"
            class="export-link pdf">
            Export PDF
        </a>
    </div>

    <div class="table-card">
        <h2>Rekap Penjualan per Produk</h2>
        <table class="app-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Total Qty</th>
                    <th>Total Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
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
@endsection
