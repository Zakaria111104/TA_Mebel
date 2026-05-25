@extends('layouts.sidebar')

@section('title', 'Dashboard - SIM Stok')
@section('active_menu', '')

@section('content')
    <style>
        .hero {
            border: 1px solid #dbe2ea;
            background: linear-gradient(120deg, #f1fff3 0%, #ffffff 70%);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .hero h2 {
            margin: 0 0 8px;
            font-size: 28px;
            font-family: var(--font-heading);
            letter-spacing: -0.02em;
        }

        .hero p {
            margin: 0;
            font-size: 15px;
            color: #475569;
            font-weight: 500;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .stat-card {
            position: relative;
            border: 1px solid #e2e8f0;
            border-left-width: 4px;
            border-radius: 10px;
            background: #fff;
            padding: 16px;
            min-width: 0;
            overflow-wrap: anywhere;
            word-break: break-word;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        }

        .stat-card.stat-total {
            border-left-color: #3b82f6;
        }

        .stat-card.stat-low {
            border-left-color: #f59e0b;
        }

        .stat-card.stat-in {
            border-left-color: #10b981;
        }

        .stat-card.stat-out {
            border-left-color: #ef4444;
        }

        .stat-main {
            min-width: 0;
            flex: 1;
        }

        .stat-title {
            font-size: 12px;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            font-weight: 800;
            color: #64748b;
            font-family: var(--font-heading);
        }

        .stat-value {
            margin-top: 4px;
            font-size: 34px;
            line-height: 1.1;
            font-weight: 800;
            color: #0f172a;
            font-family: var(--font-heading);
            letter-spacing: -0.02em;
            font-variant-numeric: tabular-nums;
        }

        .stat-icon {
            width: 34px;
            height: 34px;
            flex-shrink: 0;
            color: #cbd5e1;
            opacity: 0.95;
        }

        .stat-trend {
            margin-top: 10px;
            font-size: 13px;
            color: #64748b;
            font-weight: 600;
        }

        .stat-trend-value {
            display: inline-block;
            margin-left: 6px;
            font-weight: 700;
            color: #0f172a;
        }

        .panel {
            border: 1px solid #dbe2ea;
            border-radius: 14px;
            background: #fff;
            padding: 20px;
        }

        .panel h3 {
            font-size: 18px;
            margin: 0 0 10px;
            font-family: var(--font-heading);
            letter-spacing: -0.015em;
        }

        /* .bars {
                    display: flex;
                    align-items: flex-end;
                    gap: 20px;
                    height: 300px;
                } */

        /* .bar-row {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 8px;
                    width: 60px;
                } */

        /* .bar-track {
                    width: 100%;
                    height: 150px;
                    background: #eef2f7;
                    border-radius: 10px;
                    position: relative;
                    overflow: hidden;
                } */

        /* .bar-fill {
                    position: absolute;
                    bottom: 0;
                    width: 100%;
                    height: 60%;
                    background: linear-gradient(to top, forestgreen, #34d399);
                } */

        .list {
            margin: 0;
            padding-left: 20px;
            color: #334155;
            line-height: 1.8;
            font-size: 15px;
        }

        .compare-grid {
            margin-top: 10px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .compare-card {
            position: relative;
            border: 1px solid #e2e8f0;
            border-left-width: 4px;
            border-radius: 10px;
            background: #fff;
            padding: 16px;
            min-width: 0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        }

        .compare-card.fast {
            border-left-color: #10b981;
        }

        .compare-card.slow {
            border-left-color: #f59e0b;
        }

        .compare-main {
            min-width: 0;
            flex: 1;
        }

        .compare-item-title {
            font-size: 12px;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            font-weight: 800;
            color: #64748b;
            font-family: var(--font-heading);
            margin: 0;
        }

        .compare-item-name {
            margin: 5px 0 0;
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            font-family: var(--font-heading);
            letter-spacing: -0.01em;
        }

        .compare-item-value {
            margin: 5px 0 0;
            font-size: 24px;
            line-height: 1.1;
            color: #0f172a;
            font-weight: 800;
            font-family: var(--font-heading);
            font-variant-numeric: tabular-nums;
        }

        .compare-icon {
            width: 34px;
            height: 34px;
            flex-shrink: 0;
            color: #cbd5e1;
            opacity: 0.95;
        }

        .compare-item-sublist {
            margin: 8px 0 0;
            padding-left: 18px;
            font-size: 13px;
            color: #475569;
            line-height: 1.5;
        }

        .compare-more {
            margin-top: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
            padding: 8px 10px;
        }

        .compare-more summary {
            cursor: pointer;
            font-size: 12px;
            color: #334155;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .compare-more ul {
            margin: 8px 0 0;
            padding-left: 18px;
            font-size: 12px;
            color: #475569;
            line-height: 1.5;
        }

        .yearly-header {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 12px;
            margin-bottom: 14px;
        }

        .yearly-chart {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 10px;
            align-items: end;
            min-height: 310px;
        }

        .month-col {
            text-align: center;
        }

        .month-bars {
            height: 240px;
            display: flex;
            justify-content: center;
            align-items: end;
            gap: 6px;
        }

        .month-bar {
            width: 22px;
            border-radius: 5px 5px 0 0;
        }

        .month-bar.in {
            background: #16a34a;
        }

        .month-bar.out {
            background: #ef4444;
        }

        .month-label {
            margin-top: 6px;
            font-size: 12px;
            color: #64748b;
            font-weight: 700;
            font-family: var(--font-heading);
        }

        .month-pct {
            margin-top: 3px;
            font-size: 12px;
            line-height: 1.35;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .month-pct.month-pct-baris {
            font-size: 10px;
            font-weight: 600;
            line-height: 1.35;
            font-variant-numeric: tabular-nums;
        }

        .month-pct .pct-in {
            color: #16a34a;
        }

        .month-pct .pct-sep {
            color: #94a3b8;
            font-weight: 500;
            margin: 0 2px;
        }

        .month-pct .pct-out {
            color: #ef4444;
        }

        .month-pct .pct-none {
            color: #cbd5e1;
            font-weight: 500;
            font-size: 12px;
        }

        .legend {
            margin-top: 12px;
            font-size: 12px;
            color: #475569;
            display: flex;
            gap: 16px;
            font-weight: 600;
        }

        .stock-alert {
            border: 1px solid #facc15;
            background: #fffbeb;
            color: #854d0e;
            border-radius: 12px;
            padding: 14px 16px;
            margin: 0 0 20px;
            font-size: 14px;
        }

        .stat-muted {
            margin-top: 10px;
            font-size: 12px;
            color: #94a3b8;
            font-weight: 600;
        }

        .stat-details {
            margin-top: 10px;
            font-size: 11px;
            color: #64748b;
        }

        .stat-details summary {
            cursor: pointer;
            color: #166534;
            font-weight: 600;
            user-select: none;
            font-family: var(--font-heading);
        }

        .stat-details summary:hover {
            text-decoration: underline;
        }

        .stat-details-body {
            margin-top: 8px;
            max-height: 140px;
            overflow-y: auto;
            padding: 8px 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            line-height: 1.5;
        }

        .stat-details-body ul {
            margin: 0;
            padding-left: 18px;
        }

        .stat-details-body li {
            margin-bottom: 4px;
        }

        .stock-alert details {
            margin-top: 8px;
        }

        .stock-alert summary {
            cursor: pointer;
            font-weight: 600;
            color: #713f12;
        }
    </style>

    <section class="hero">
        <div>
            <h2>Dashboard</h2>
            <p>Selamat datang, {{ $user->name }}.</p>
        </div>
    </section>

    <div class="stats">
        <article class="stat-card stat-total">
            <div class="stat-main">
                <div class="stat-title">Total Produk</div>
                <div class="stat-value">{{ $totalProduk }}</div>
                @if ($daftarNamaProduk->isNotEmpty())
                    <details class="stat-details">
                        <summary>Lihat daftar nama produk ({{ $daftarNamaProduk->count() }})</summary>
                        <div class="stat-details-body">{{ $daftarNamaProduk->implode(', ') }}</div>
                    </details>
                @else
                    <p class="stat-muted">Belum ada produk terdaftar.</p>
                @endif
            </div>
            <svg class="stat-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="currentColor"
                    d="M6 2h8l4 4v2h1a2 2 0 0 1 2 2v9.5A2.5 2.5 0 0 1 18.5 22h-13A2.5 2.5 0 0 1 3 19.5V10a2 2 0 0 1 2-2h1V4a2 2 0 0 1 2-2m1 6h10V7h-3a1 1 0 0 1-1-1V3H8a1 1 0 0 0-1 1zm-2 2v9.5a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5V10z" />
            </svg>
        </article>
        <article class="stat-card stat-low">
            <div class="stat-main">
                <div class="stat-title">Stok Menipis</div>
                <div class="stat-value">{{ $stokMenipis }}</div>
                @if ($produkStokMenipis->isNotEmpty())
                    <details class="stat-details">
                        <summary>Lihat barang menipis ({{ $produkStokMenipis->count() }})</summary>
                        <div class="stat-details-body">
                            <ul>
                                @foreach ($produkStokMenipis as $p)
                                    <li>{{ $p->nama }} (stok {{ $p->stok }}, min. {{ $p->stok_minimum }})</li>
                                @endforeach
                            </ul>
                        </div>
                    </details>
                @else
                    <p class="stat-muted">Tidak ada.</p>
                @endif
            </div>
            <svg class="stat-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="currentColor"
                    d="M13 2a1 1 0 0 0-2 0v10.59l-1.3-1.3a1 1 0 1 0-1.4 1.42l3 2.98a1 1 0 0 0 1.4 0l3-2.98a1 1 0 1 0-1.4-1.42l-1.3 1.3zM5 18a1 1 0 0 1 1 1v1h12v-1a1 1 0 1 1 2 0v1a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-1a1 1 0 0 1 1-1" />
            </svg>
        </article>
        <article class="stat-card stat-in">
            <div class="stat-main">
                <div class="stat-title">Barang Masuk</div>
                <div class="stat-value">{{ number_format($jumlahBarangMasuk) }}</div>
            </div>
            <svg class="stat-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="currentColor"
                    d="M4 11a1 1 0 0 1 1-1h8.59L11.3 7.7a1 1 0 0 1 1.4-1.4l4 4a1 1 0 0 1 0 1.4l-4 4a1 1 0 1 1-1.4-1.4L13.59 12H5a1 1 0 0 1-1-1m1 7h14a1 1 0 1 1 0 2H5a1 1 0 1 1 0-2" />
            </svg>
        </article>
        <article class="stat-card stat-out">
            <div class="stat-main">
                <div class="stat-title">Barang Keluar</div>
                <div class="stat-value">{{ number_format($jumlahBarangKeluar) }}</div>
            </div>
            <svg class="stat-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="currentColor"
                    d="M20 11a1 1 0 0 0-1-1h-8.59l2.29-2.3a1 1 0 0 0-1.4-1.4l-4 4a1 1 0 0 0 0 1.4l4 4a1 1 0 0 0 1.4-1.4L10.41 12H19a1 1 0 0 0 1-1m-1 7H5a1 1 0 1 0 0 2h14a1 1 0 1 0 0-2" />
            </svg>
        </article>
    </div>

    <!-- <div class="stock-alert">
            Stok Menipis: <strong>{{ $stokMenipis }}</strong> produk perlu perhatian.
            Persentase stok aman saat ini <strong>{{ $persentaseStokAman }}%</strong>.
            @if ($produkStokMenipis->isNotEmpty())
                <details>
                    <summary>Detail barang menipis ({{ $produkStokMenipis->count() }})</summary>
                    <div class="stat-details-body" style="margin-top:8px;">
                        {{ $produkStokMenipis->map(fn($p) => $p->nama . ' (stok ' . $p->stok . ')')->implode(', ') }}
                    </div>
                </details>
            @else
                <div style="margin-top:8px;">
                    <strong>Barang menipis:</strong> Tidak ada.
                </div>
            @endif
        </div> -->

    <section class="panel" style="margin-top:14px;">
        <div class="yearly-header">
            <div>
                <h3 style="margin:0;">Grafik Tahunan Arus Barang</h3>
                <div style="font-size:13px; color:#64748b; margin-top:4px;">
                    Barang masuk: <strong>{{ $totalMasukTahunan }}</strong> unit —
                    Barang keluar: <strong>{{ $totalKeluarTahunan }}</strong> unit —
                    Presentase masuk: <strong>{{ $persenMasukTahunan }}%</strong>, keluar:
                    <strong>{{ $persenKeluarTahunan }}%</strong>
                </div>
            </div>
            <!-- <form method="GET" action="{{ route('dashboard') }}" style="display:flex; gap:8px; align-items:center;">
                    <select name="tahun">
                        @foreach ($daftarTahun as $tahun)
                            <option value="{{ $tahun }}" @selected((int) $tahunDipilih === (int) $tahun)>{{ $tahun }}</option>
                        @endforeach
                    </select>
                    <select name="jenis_data">
                        <option value="semua" @selected($jenisData === 'semua')>Masuk dan Keluar</option>
                        <option value="masuk" @selected($jenisData === 'masuk')>Masuk</option>
                        <option value="keluar" @selected($jenisData === 'keluar')>Keluar</option>
                    </select>
                    <button type="submit">Tampilkan</button>
                </form> -->
        </div>

        @php
            $tinggiAreaBatangPx = 228;
        @endphp

        <div class="yearly-chart">
            @foreach ($trenTahunan as $bulan)
                @php
                    $idxBulan = $loop->index;
                    $tinggiMasuk = max(2, (int) round(($bulan['masuk'] / $maksTrenTahunan) * $tinggiAreaBatangPx));
                    $tinggiKeluar = max(2, (int) round(($bulan['keluar'] / $maksTrenTahunan) * $tinggiAreaBatangPx));
                    $kontribMasukPct = $pctMasukKontribTahunan[$idxBulan] ?? null;
                    $kontribKeluarPct = $pctKeluarKontribTahunan[$idxBulan] ?? null;
                @endphp
                <div class="month-col">
                    <div class="month-bars">
                        @if ($jenisData !== 'keluar')
                            <div class="month-bar in" style="height: {{ $tinggiMasuk }}px;"
                                title="{{ $bulan['label'] }} | Masuk: {{ $bulan['masuk'] }} unit @if ($kontribMasukPct !== null) | Kontribusi ke presentase masuk tahun {{ $persenMasukTahunan }}%: {{ $kontribMasukPct }} poin persen @endif">
                            </div>
                        @endif
                        @if ($jenisData !== 'masuk')
                            <div class="month-bar out" style="height: {{ $tinggiKeluar }}px;"
                                title="{{ $bulan['label'] }} | Keluar: {{ $bulan['keluar'] }} unit @if ($kontribKeluarPct !== null) | Kontribusi ke presentase keluar tahun {{ $persenKeluarTahunan }}%: {{ $kontribKeluarPct }} poin persen @endif">
                            </div>
                        @endif
                    </div>
                    <div class="month-label">{{ $bulan['label'] }}</div>
                    <div class="month-pct month-pct-baris">
                        @if ($kontribMasukPct !== null && $kontribKeluarPct !== null)
                            Presentase masuk: <span class="pct-in">{{ $kontribMasukPct }}%</span>,
                            keluar: <span class="pct-out">{{ $kontribKeluarPct }}%</span>
                        @else
                            <span class="pct-none">—</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="legend">
            @if ($jenisData !== 'keluar')
                <span><strong style="color:#16a34a;">Hijau</strong> = Barang Masuk</span>
            @endif
            @if ($jenisData !== 'masuk')
                <span><strong style="color:#ef4444;">Merah</strong> = Barang Keluar</span>
            @endif
        </div>
    </section>

    <!-- <section class="panel" style="margin-top:14px;">
                <h3>Top Produk Paling Aktif</h3>
                <ul class="list">
                    @forelse ($topPergerakanProduk as $item)
                        <li>{{ $item->product->nama ?? 'Produk' }} - {{ $item->total }} unit transaksi</li>
                    @empty
                        <li>Belum ada data pergerakan produk.</li>
                    @endforelse
                </ul>
            </section> -->

    <section class="panel" style="margin-top:14px;">
        <h3>Perbandingan Kecepatan Penjualan Produk</h3>
        <!-- <p class="stat-muted" style="margin-top:-2px; margin-bottom:10px;">
            Periode otomatis: {{ $labelBulanPerbandinganPenjualan }} (berdasarkan transaksi barang keluar bulan berjalan).
        </p> -->
        @if ($produkTercepatTerjual && $produkTerlambatTerjual)
            <div class="compare-grid">
                <article class="compare-card fast">
                    <div class="compare-main">
                        <p class="compare-item-title">Produk Paling Aktif Terjual</p>
                        <p class="compare-item-name">{{ $produkTercepatTerjual->product->nama ?? 'Produk' }}</p>
                        <p class="compare-item-value">{{ number_format((int) $produkTercepatTerjual->total_terjual) }} unit</p>
                        <details class="compare-more">
                            <summary>More info (produk mendekati tercepat)</summary>
                            @if ($produkCepatTerjualLainnya->isNotEmpty())
                                <ul>
                                    @foreach ($produkCepatTerjualLainnya as $item)
                                        <li>
                                            {{ $item->product->nama ?? 'Produk' }} -
                                            {{ number_format((int) $item->total_terjual) }} unit
                                            (selisih
                                            {{ number_format(abs((int) $produkTercepatTerjual->total_terjual - (int) $item->total_terjual)) }}
                                            unit)
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="stat-muted" style="margin:8px 0 0;">Belum ada produk lain yang mendekati.</p>
                            @endif
                        </details>
                    </div>
                </article>
                <article class="compare-card slow">
                    <div class="compare-main">
                        <p class="compare-item-title">Produk Paling Lambat Terjual</p>
                        <p class="compare-item-name">{{ $produkTerlambatTerjual->product->nama ?? 'Produk' }}</p>
                        <p class="compare-item-value">{{ number_format((int) $produkTerlambatTerjual->total_terjual) }} unit</p>
                        <details class="compare-more">
                            <summary>More info (produk mendekati terlambat)</summary>
                            @if ($produkLambatTerjualLainnya->isNotEmpty())
                                <ul>
                                    @foreach ($produkLambatTerjualLainnya as $item)
                                        <li>
                                            {{ $item->product->nama ?? 'Produk' }} -
                                            {{ number_format((int) $item->total_terjual) }} unit
                                            (selisih
                                            {{ number_format(abs((int) $item->total_terjual - (int) $produkTerlambatTerjual->total_terjual)) }}
                                            unit)
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="stat-muted" style="margin:8px 0 0;">Belum ada produk lain yang mendekati.</p>
                            @endif
                        </details>
                    </div>
                </article>
            </div>
            <p class="stat-trend">
                Selisih performa penjualan:
                <span class="stat-trend-value">{{ number_format($selisihProdukTerjual) }} unit</span>
            </p>
            <p class="stat-muted">Produk tambahan ditampilkan jika selisih penjualan maksimal
                {{ $batasSelisihProdukMirip }} unit dari produk utama.
            </p>
        @else
            <p class="stat-muted">Belum ada data barang keluar untuk menghitung kecepatan penjualan produk.</p>
        @endif
    </section>


    <!-- <section class="panel" style="margin-top:14px;">
                <h3>Prioritas Restock</h3>
                <div class="bars">
                    @forelse ($produkStokMenipis as $produk)
                        @php
                            $ratio = $produk->stok_minimum > 0 ? min(100, (int) (($produk->stok / $produk->stok_minimum) * 100)) : 0;
                        @endphp
                        <div class="bar-row">
                            <strong>{{ $produk->nama }}</strong>
                            <div class="bar-track">
                                <div class="bar-fill" style="width: {{ $ratio }}%"></div>
                            </div>
                            <span>{{ $produk->stok }}</span>
                        </div>
                    @empty
                        <p>Tidak ada produk yang perlu restock.</p>
                    @endforelse
                </div>
            </section> -->

    <section class="panel" style="margin-top:14px;">
        <h3>Aktivitas Terbaru</h3>
        <table class="app-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Barang</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($aktivitasTerbaru as $aktivitas)
                    <tr>
                        <td>{{ $aktivitas->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>{{ strtoupper($aktivitas->tipe) }}</td>
                        <td>{{ $aktivitas->jumlah }} unit</td>
                        <td>{{ $aktivitas->product->nama ?? 'Produk' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Belum ada aktivitas transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection