@extends('layouts.sidebar')

@section('title', 'Laporan Barang Hilang')
@section('active_menu', 'reports-barang-hilang')

@section('content')
    <style>
        .report-table {
            table-layout: fixed;
        }
    </style>
    @include('reports._nav', ['activeReport' => 'barang-hilang'])
    @include('reports._filter', ['action' => route('reports.barang-hilang')])

    <div class="table-card">
        <div style="font-size:14px; color:#64748b;">Total Barang Hilang</div>
        <div style="font-size:30px; color:#b91c1c; font-weight:800;">{{ $total }}</div>
        <div style="margin-top:8px; font-size:13px; color:#64748b;">
            Data berdasarkan transaksi kehilangan dari input "Barang Hilang".
        </div>
    </div>

    <div class="table-card export-actions">
        <a href="{{ route('reports.barang-hilang.export', array_merge(request()->query(), ['format' => 'excel'])) }}"
            class="export-link excel">
            Export Excel
        </a>
        <a href="{{ route('reports.barang-hilang.export', array_merge(request()->query(), ['format' => 'pdf'])) }}"
            class="export-link pdf">
            Export PDF
        </a>
    </div>

    <div class="table-card">
        <h2>Detail Barang Hilang</h2>
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
                        <td colspan="5">Tidak ada data barang hilang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
