@extends('layouts.sidebar')

@section('title', 'Laporan Penjualan')
@section('active_menu', 'reports-penjualan')

@section('content')
    <style>
        .report-table {
            table-layout: fixed;
        }
    </style>
    @include('reports._nav', ['activeReport' => 'penjualan'])
    @include('reports._filter', ['action' => route('reports.penjualan')])

    <div class="table-card">
        <div style="font-size:14px; color:#64748b;">Total Penjualan</div>
        <div style="font-size:30px; color:#14532d; font-weight:800;">{{ $total }}</div>
    </div>

    <div class="table-card export-actions">
        <a href="{{ route('reports.penjualan.export', array_merge(request()->query(), ['format' => 'excel'])) }}"
            class="export-link excel">
            Export Excel
        </a>
        <a href="{{ route('reports.penjualan.export', array_merge(request()->query(), ['format' => 'pdf'])) }}"
            class="export-link pdf">
            Export PDF
        </a>
    </div>

    <div class="table-card">
        <h2>Detail Penjualan</h2>
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
                @forelse ($data as $item)
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
@endsection
