@extends('layouts.sidebar')

@section('title', 'Barang Keluar - SIM Stok')
@section('active_menu', 'keluar')

@section('content')
    @php($isAdmin = auth()->user()?->role === 'admin')
    @php($kategoriBarang = ['Meja', 'Kursi', 'Lemari', 'Kasur', 'Sofa', 'Rak', 'Bufet'])
    @php($keteranganBarangKeluar = ['Terjual'])

    <style>
        .panel {
            margin-bottom: 14px;
        }

        form.outgoing-form {
            display: grid;
            grid-template-columns: minmax(150px, 190px) minmax(230px, 1fr) minmax(170px, 220px) minmax(150px, 180px) max-content !important;
            gap: 10px;
        }

        .inline button {
            min-width: 72px;
            height: 38px;
            padding: 8px 12px;
        }

        @media (max-width: 768px) {
            form.outgoing-form {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    @if ($isAdmin)
        <div class="table-card panel">
            <h2>Input Barang Keluar</h2>
            <form action="{{ route('stock-movements.outgoing.store') }}" method="POST" class="inline outgoing-form">
                @csrf
                <select name="kategori" class="category-filter" data-target="outgoing-product-select" required
                    oninvalid="this.setCustomValidity('Pilih kategori terlebih dahulu.')"
                    onchange="this.setCustomValidity('')">
                    <option value="">Pilih kategori</option>
                    @foreach ($kategoriBarang as $kategori)
                         <option value="{{ strtolower($kategori) }}">{{ ucfirst($kategori) }}</option>
                    @endforeach
                </select>
                <select name="id_barang" id="outgoing-product-select" required>
                    <option value="">Pilih barang</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-kategori="{{ strtolower($product->kategori ?? '') }}">
                            {{ $product->nama }} (stok: {{ $product->stok }})
                        </option>
                    @endforeach
                </select>
                <input type="number" name="jumlah" min="1" placeholder="Jumlah keluar" required>
                <select name="keterangan" required>
                    <option value="">Pilih keterangan</option>
                    @foreach ($keteranganBarangKeluar as $keterangan)
                        <option value="{{ $keterangan }}" @selected($keterangan === 'Terjual')>{{ $keterangan }}</option>
                    @endforeach
                </select>
                <button type="submit">Simpan</button>
            </form>
        </div>
    @endif

    <div class="table-card panel">
        <h2>Filter Barang Keluar</h2>
        <form method="GET" action="{{ route('stock-movements.outgoing') }}" class="report-filter-form">
            <div>
                <label for="tanggal_mulai">Tanggal Mulai</label>
                <input id="tanggal_mulai" type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
            </div>
            <div>
                <label for="tanggal_selesai">Tanggal Selesai</label>
                <input id="tanggal_selesai" type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
            </div>
            <button type="submit">Terapkan</button>
            <a href="{{ route('stock-movements.outgoing') }}" class="btn-link">
                Reset
            </a>
        </form>
    </div>

    <div class="table-card panel">
        <h3>Riwayat Barang Keluar</h3>
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
                        <td>{{ ($movement->waktu ?? $movement->created_at)?->timezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '—' }}</td>
                        <td>{{ $movement->barang ?? $movement->product?->nama ?? '-' }}</td>
                        <td>{{ $movement->jumlah }}</td>
                        <td>{{ $movement->keterangan ?? '-' }}</td>
                        <td>{{ $movement->input_oleh ?? $movement->user?->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Belum ada data barang keluar.</td>
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
