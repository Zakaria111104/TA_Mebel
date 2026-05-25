@extends('layouts.sidebar')

@section('title', 'Barang Masuk - SIM Stok')
@section('active_menu', 'masuk')

@section('content')
    <style>
        .panel {
            margin-bottom: 14px;
        }

        .inline {
            display: grid;
            grid-template-columns: 1fr 220px 1fr auto;
            gap: 10px;
        }
    </style>

    <div class="table-card panel">
        <h2>Input Barang Masuk</h2>
        <form action="{{ route('stock-movements.incoming.store') }}" method="POST" class="inline">
            @csrf
            <select name="id_barang" required>
                <option value="">Pilih barang</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->nama }} (stok: {{ $product->stok }})</option>
                @endforeach
            </select>
            <input type="number" name="jumlah" min="1" placeholder="Jumlah masuk" required>
            <input type="text" name="keterangan" placeholder="Keterangan">
            <button type="submit">Simpan</button>
        </form>
    </div>

    <div class="table-card panel">
        <h2>Filter Barang Masuk</h2>
        <form method="GET" action="{{ route('stock-movements.incoming') }}" class="report-filter-form">
            <div>
                <label for="tanggal_mulai">Tanggal Mulai</label>
                <input id="tanggal_mulai" type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
            </div>
            <div>
                <label for="tanggal_selesai">Tanggal Selesai</label>
                <input id="tanggal_selesai" type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
            </div>
            <button type="submit">Terapkan</button>
            <a href="{{ route('stock-movements.incoming') }}" class="btn-link">
                Reset
            </a>
        </form>
    </div>

    <div class="table-card panel">
        <h3>Riwayat Barang Masuk</h3>
        <table class="app-table">
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
                @forelse ($movements as $movement)
                    <tr>
                        <td>{{ $movement->created_at?->timezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '—' }}</td>
                        <td>{{ $movement->product->nama }}</td>
                        <td>{{ $movement->jumlah }}</td>
                        <td>{{ $movement->keterangan ?? '-' }}</td>
                        <td>{{ $movement->user?->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Belum ada data barang masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
