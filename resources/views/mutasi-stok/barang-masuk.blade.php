@extends('layouts.sidebar')

@section('title', 'Barang Masuk - SIM Stok')
@section('active_menu', 'masuk')

@section('content')
@php($isAdmin = auth()->user()?->role === 'admin')
@php($kategoriBarang = ['Meja', 'Kursi', 'Lemari', 'Kasur', 'Sofa', 'Rak', 'Bufet'])
<style>
    .panel {
        margin-bottom: 14px;
    }

    .inline {
        display: grid;
        grid-template-columns: 180px 1fr 220px 1fr auto;
        gap: 10px;
    }

    .inline button {
        min-width: 72px;
        height: 38px;
        padding: 8px 12px;
    }

    @media (max-width: 768px) {
        .inline {
            grid-template-columns: 1fr;
        }
    }
</style>

@if ($isAdmin)
    <div class="table-card panel">
        <h2>Input Barang Masuk</h2>
        <form action="{{ route('stock-movements.incoming.store') }}" method="POST" class="inline">
            @csrf
            <select name="kategori" class="category-filter" data-target="incoming-product-select" required
                oninvalid="this.setCustomValidity('Pilih kategori terlebih dahulu.')" onchange="this.setCustomValidity('')">
                <option value="">Pilih kategori</option>
                @foreach ($kategoriBarang as $kategori)
                    <option value="{{ strtolower($kategori) }}">{{ ucfirst($kategori) }}</option>
                @endforeach
            </select>
            <select name="id_barang" id="incoming-product-select" required>
                <option value="">Pilih barang</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" data-kategori="{{ strtolower($product->kategori ?? '') }}">
                        {{ $product->nama }} (stok: {{ $product->stok }})
                    </option>
                @endforeach
            </select>
            <input type="number" name="jumlah" min="1" placeholder="Jumlah masuk" required>
            <input type="text" name="keterangan" placeholder="Keterangan">
            <button type="submit">Simpan</button>
        </form>
    </div>
@endif

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
                    <td>{{ ($movement->waktu ?? $movement->created_at)?->timezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '—' }}
                    </td>
                    <td>{{ $movement->barang ?? $movement->product?->nama ?? '-' }}</td>
                    <td>{{ $movement->jumlah }}</td>
                    <td>{{ $movement->keterangan ?? '-' }}</td>
                    <td>{{ $movement->input_oleh ?? $movement->user?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada data barang masuk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($isAdmin)
    <script>
        document.querySelectorAll('.category-filter').forEach((categorySelect) => {
            categorySelect.addEventListener('change', () => {
                const productSelect = document.getElementById(categorySelect.dataset.target);
                const selectedCategory = categorySelect.value;

                if (!productSelect) {
                    return;
                }

                productSelect.value = '';

                productSelect.querySelectorAll('option').forEach((option) => {
                    if (!option.value) {
                        option.hidden = false;
                        return;
                    }

                    option.hidden = selectedCategory !== '' && option.dataset.kategori !== selectedCategory;
                });
            });
        });
    </script>
@endif
@endsection