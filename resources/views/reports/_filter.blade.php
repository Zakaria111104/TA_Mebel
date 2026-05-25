<div class="table-card">
    <h2>Filter Laporan</h2>
    <form method="GET" action="{{ $action }}" class="report-filter-form">
        <div>
            <label for="tanggal_mulai">Tanggal Mulai</label>
            <input id="tanggal_mulai" type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}">
        </div>
        <div>
            <label for="tanggal_selesai">Tanggal Selesai</label>
            <input id="tanggal_selesai" type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}">
        </div>
        <button type="submit">Terapkan</button>
        <a href="{{ $action }}" class="btn-link">
            Reset
        </a>
    </form>
</div>
