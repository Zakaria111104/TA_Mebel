<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4 landscape;
            margin: 22px 26px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #222;
            margin: 0;
            font-size: 11px;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 6px;
            text-transform: uppercase;
        }

        .meta {
            font-size: 11px;
            margin-bottom: 14px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #b9c0c9;
            padding: 6px 7px;
            font-size: 10px;
            line-height: 1.35;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        th {
            background: #e8eef6;
            text-align: left;
            font-weight: bold;
        }

        td {
            vertical-align: top;
        }

        .empty {
            text-align: center;
            color: #64748b;
        }
    </style>
</head>

<body>
    @php
        $columnCount = count($columns);
        $columnWidths = match ($columnCount) {
            3 => ['54%', '22%', '24%'],
            5 => ['17%', '26%', '9%', '16%', '32%'],
            default => array_fill(0, max(1, $columnCount), (100 / max(1, $columnCount)) . '%'),
        };
    @endphp

    <h1>{{ $title }}</h1>
    <div class="meta">
        Periode: {{ $period ?? (($tanggalMulai ?? '-') . ' sampai ' . ($tanggalSelesai ?? '-')) }}
        &nbsp;|&nbsp;
        Dicetak: {{ $printedAt ?? now('Asia/Jakarta')->format('d/m/Y H:i') }}
    </div>

    <table>
        <colgroup>
            @foreach ($columnWidths as $width)
                <col style="width: {{ $width }};">
            @endforeach
        </colgroup>
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th>{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($columns as $index => $column)
                        <td>{{ $row[$index] ?? '' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td class="empty" colspan="{{ count($columns) }}">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
