@extends('layouts.sidebar')

@section('title', 'Data Barang - SIM Stok')
@section('active_menu', 'barang')

@section('content')
@php($isAdmin = auth()->user()?->role === 'admin')
@php($kategoriBarang = ['meja', 'kursi', 'lemari', 'kasur', 'sofa', 'rak', 'bufet'])

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

    .inline-form {
        display: grid;
        grid-template-columns: 60px 1fr 1fr 95px 95px 1fr auto auto;
        gap: 6px;
    }

    .action-form {
        display: inline;
    }

    .action-button {
        min-width: 72px;
        height: 38px;
        padding: 8px 12px;
    }

    .edit-row[hidden] {
        display: none;
    }

    .edit-row td {
        background: #f8fafc;
    }

    .button-secondary {
        background: #64748b;
        border-color: #64748b;
    }

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

    .app-table th,
    .app-table td {
        font-size: 16px;
    }
</style>

@if ($isAdmin)
    <div class="table-card panel">
        <h2>Data Barang</h2>
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="grid-form">
                <input type="text" name="kode" placeholder="Kode barang" value="{{ old('kode') }}" required>
                <input type="text" name="nama" placeholder="Nama barang" value="{{ old('nama') }}" required>
                <select name="kategori">
                    <option value="">Pilih kategori</option>
                    @foreach ($kategoriBarang as $kategori)
                        <option value="{{ $kategori }}" @selected(old('kategori') === $kategori)>{{ ucfirst($kategori) }}</option>
                    @endforeach
                </select>
                <input type="number" name="stok" placeholder="Stok awal" min="0" value="{{ old('stok', 0) }}"
                    data-stock-input required>
                <input type="number" name="stok_minimum" placeholder="Stok minimum" min="0" max="{{ old('stok', 0) }}"
                    value="{{ old('stok_minimum', 0) }}" data-minimum-stock-input required>
                <input type="text" name="keterangan" placeholder="Keterangan" value="{{ old('keterangan') }}">
            </div>
            <button type="submit">Simpan Data Barang</button>
        </form>
    </div>
@endif

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
                <!-- <th style="padding-left: 80px;">Kategori</th> -->
                <th>Kategori</th>
                <th>Stok</th>
                <th>Stok Min</th>
                <th>Keterangan</th>
                @if ($isAdmin)
                    <th colspan="2">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                @if ($isAdmin)
                    <tr>
                        <td>{{ $product->kode }}</td>
                        <td>{{ $product->nama }}</td>
                        <td>{{ $product->kategori ?? '-' }}</td>
                        <td>{{ $product->stok }}</td>
                        <td>{{ $product->stok_minimum }}</td>
                        <td>{{ $product->keterangan ?? '-' }}</td>
                        <td>
                            <button type="button" class="action-button edit-product-button"
                                data-target="edit-product-{{ $product->id }}">Edit</button>
                        </td>
                        <td>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="action-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-button" onclick="return confirm('Hapus barang ini?')"
                                    style="background:#b91c1c; border-color:#b91c1c;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <tr id="edit-product-{{ $product->id }}" class="edit-row" hidden>
                        <td colspan="8">
                            <form action="{{ route('products.update', $product) }}" method="POST" class="inline-form">
                                @csrf
                                @method('PUT')
                                <input type="text" name="kode" value="{{ $product->kode }}" required>
                                <input type="text" name="nama" value="{{ $product->nama }}" required>
                                <input type="text" name="kategori" value="{{ $product->kategori }}">
                                <input type="number" name="stok" min="0" value="{{ $product->stok }}" data-stock-input required>
                                <input type="number" name="stok_minimum" min="0" max="{{ $product->stok }}"
                                    value="{{ $product->stok_minimum }}" data-minimum-stock-input required>
                                <input type="text" name="keterangan" value="{{ $product->keterangan }}">
                                <button type="submit">Update</button>
                                <button type="button" class="button-secondary cancel-edit-button"
                                    data-target="edit-product-{{ $product->id }}">Batal</button>
                            </form>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $product->kode }}</td>
                        <td>{{ $product->nama }}</td>
                        <td>{{ $product->kategori ?? '-' }}</td>
                        <td>{{ $product->stok }}</td>
                        <td>{{ $product->stok_minimum }}</td>
                        <td>{{ $product->keterangan ?? '-' }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="{{ $isAdmin ? 8 : 6 }}">Belum ada data barang.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($isAdmin)
    <script>
        document.querySelectorAll('.edit-product-button, .cancel-edit-button').forEach((button) => {
            button.addEventListener('click', () => {
                const editRow = document.getElementById(button.dataset.target);

                if (editRow) {
                    editRow.hidden = !editRow.hidden;
                }
            });
        });

        document.querySelectorAll('form').forEach((form) => {
            const stockInput = form.querySelector('[data-stock-input]');
            const minimumStockInput = form.querySelector('[data-minimum-stock-input]');

            if (!stockInput || !minimumStockInput) {
                return;
            }

            const syncMinimumStockLimit = () => {
                const stock = Number.parseInt(stockInput.value || '0', 10);
                const minimumStock = Number.parseInt(minimumStockInput.value || '0', 10);

                minimumStockInput.max = stock;

                if (minimumStock > stock) {
                    minimumStockInput.value = stock;
                }
            };

            stockInput.addEventListener('input', syncMinimumStockLimit);
            minimumStockInput.addEventListener('input', syncMinimumStockLimit);
            syncMinimumStockLimit();
        });
    </script>
@endif
@endsection