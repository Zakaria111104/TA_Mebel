@extends('layouts.sidebar')

@section('title', 'Barang Hilang - SIM Stok')
@section('active_menu', 'hilang')

@section('content')
    <style>
        .panel { margin-bottom: 14px; }
        .inline { display: grid; grid-template-columns: 1fr 220px 1fr auto; gap: 10px; }
    </style>

    <div class="table-card panel">
        <h2>Input Barang Hilang</h2>
        <form action="{{ route('stock-movements.lost.store') }}" method="POST" class="inline">
            @csrf
            <select name="id_barang" required>
                <option value="">Pilih barang</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->nama }} (stok: {{ $product->stok }})</option>
                @endforeach
            </select>
            <input type="number" name="jumlah" min="1" placeholder="Jumlah hilang" required>
            <input type="text" name="keterangan" placeholder="Keterangan kehilangan">
            <button type="submit" style="background:#b91c1c; border-color:#b91c1c;">Simpan</button>
        </form>
    </div>

    <div class="table-card panel">
        <h3>Riwayat Barang Hilang</h3>
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
                    <tr><td colspan="5">Belum ada data barang hilang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
