@extends('layouts.sidebar')

@section('title', 'Data Barang - SIM Stok')
@section('active_menu', 'barang')

@section('content')
    <style>
        .grid-form {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }
        .panel {
            margin-bottom: 10px;
        }
        .inline-form { display: grid; grid-template-columns: 60px 1fr 1fr 95px 95px 1fr auto; gap: 6px; }
        .table-toolbar {
            margin-bottom: 10px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
        }
        .table-toolbar label {
            font-size: 13px;
            color: #64748b;
            font-weight: 600;
        }
    </style>

    <div class="table-card panel">
        <h2>Data Barang</h2>
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="grid-form">
                <input type="text" name="kode" placeholder="Kode barang" value="{{ old('kode') }}" required>
                <input type="text" name="nama" placeholder="Nama barang" value="{{ old('nama') }}" required>
                <input type="text" name="kategori" placeholder="Kategori" value="{{ old('kategori') }}">
                <input type="number" name="stok" placeholder="Stok awal" min="0" value="{{ old('stok', 0) }}" required>
                <input type="number" name="stok_minimum" placeholder="Stok minimum" min="0" value="{{ old('stok_minimum', 0) }}" required>
                <input type="text" name="deskripsi" placeholder="Deskripsi" value="{{ old('deskripsi') }}">
            </div>
            <button type="submit">Simpan Data Barang</button>
        </form>
    </div>

    <div class="table-card panel">
        <h3>Daftar Barang</h3>
        <form method="GET" action="{{ route('products.index') }}" class="table-toolbar">
            <label for="urut">Urutkan</label>
            <select name="urut" id="urut" onchange="this.form.submit()">
                <option value="nama_asc" @selected(($opsiUrut ?? 'nama_asc') === 'nama_asc')>Nama (A-Z)</option>
                <option value="lama_baru" @selected(($opsiUrut ?? 'nama_asc') === 'lama_baru')>Data lama ke terbaru</option>
            </select>
        </form>
        <table class="app-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th style="padding-left: 80px;">Kategori</th>
                    <th>Stok</th>
                    <th>Stok Min</th>
                    <th>Deskripsi</th>
                    <th colspan="2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td colspan="8">
                            <div style="display:flex; align-items:start; gap:6px;">
                                <form action="{{ route('products.update', $product) }}" method="POST" class="inline-form" style="flex:1;">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="kode" value="{{ $product->kode }}" required>
                                    <input type="text" name="nama" value="{{ $product->nama }}" required>
                                    <input type="text" name="kategori" value="{{ $product->kategori }}">
                                    <input type="number" name="stok" min="0" value="{{ $product->stok }}" required>
                                    <input type="number" name="stok_minimum" min="0" value="{{ $product->stok_minimum }}" required>
                                    <input type="text" name="deskripsi" value="{{ $product->deskripsi }}">
                                    <button type="submit">Update</button>
                                </form>
                                <form action="{{ route('products.destroy', $product) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus barang ini?')" style="background:#b91c1c; border-color:#b91c1c;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">Belum ada data barang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
