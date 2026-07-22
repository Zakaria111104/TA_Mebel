@extends('layouts.sidebar')

@section('title', 'Laporan Pembelian')
@section('active_menu', 'reports-pembelian')

@section('content')
    <style>
        .report-table {
            table-layout: fixed;
        }
    </style>
    @include('reports._nav', ['activeReport' => 'pembelian'])
    @include('reports._filter', ['action' => route('reports.pembelian')])

    <div class="table-card">
        <div style="font-size:14px; color:#64748b;">Total Pembelian</div>
        <div style="font-size:30px; color:#14532d; font-weight:800;">{{ $total }}</div>
    </div>

    <div class="table-card export-actions">
        <a href="{{ route('reports.pembelian.export', array_merge(request()->query(), ['format' => 'excel'])) }}"
            class="export-link excel">
            Export Excel
        </a>
        <a href="{{ route('reports.pembelian.export', array_merge(request()->query(), ['format' => 'pdf'])) }}"
            class="export-link pdf">
            Export PDF
        </a>
    </div>

    <div class="table-card">
        <h2>Detail Pembelian</h2>
        <table class="app-table report-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Input Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr>
                        <td>{{ $item->created_at?->timezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '-' }}</td>
                        <td>{{ $item->product->nama ?? '-' }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Tidak ada data pembelian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
