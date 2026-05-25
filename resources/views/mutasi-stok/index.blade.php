<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Stok - SIM Stok Mebel</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 24px;">
    <h1>Transaksi Stok Mebel</h1>
    <p><a href="{{ route('dashboard') }}">Kembali ke dashboard</a> | <a href="{{ route('products.index') }}">Master Barang</a></p>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        <p style="color: red;">{{ $errors->first() }}</p>
    @endif

    <h2>Input Transaksi</h2>
    <form action="{{ route('stock-movements.store') }}" method="POST">
        @csrf
        <select name="id_barang" required>
            <option value="">Pilih barang</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->nama }} (stok: {{ $product->stok }})</option>
            @endforeach
        </select>
        <select name="tipe" required>
            <option value="">Pilih tipe</option>
            <option value="masuk">Stok Masuk</option>
            <option value="keluar">Stok Keluar</option>
        </select>
        <input type="number" name="jumlah" min="1" placeholder="Jumlah" required>
        <input type="text" name="keterangan" placeholder="Keterangan">
        <button type="submit">Simpan Transaksi</button>
    </form>

    <h2>Riwayat Terakhir</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Barang</th>
                <th>Tipe</th>
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
                    <td>{{ strtoupper($movement->tipe) }}</td>
                    <td>{{ $movement->jumlah }}</td>
                    <td>{{ $movement->keterangan ?? '-' }}</td>
                    <td>{{ $movement->user?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Belum ada transaksi.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
