<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIM Stok')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bg-app: #f1f5f9;
            --bg-card: #ffffff;
            --bg-soft: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --line: #e2e8f0;
            --primary: #166534;
            --primary-soft: #22c55e;
            --danger: #b91c1c;
            --radius-md: 12px;
            --radius-sm: 10px;
            --shadow-soft: 0 10px 26px rgba(15, 23, 42, 0.06);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: var(--bg-app);
            color: var(--text-main);
            font-size: 18px;
            line-height: 1.55;
            overflow-x: hidden;
        }

        .topbar {
            height: 75px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(6px);
            position: sticky;
            top: 0;
            z-index: 9;
        }

        .brand {
            font-size: 30px;
            font-weight: 850;
            color: #166534;
            margin: 0;
            letter-spacing: -0.02em;
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .nav {
            display: flex;
            gap: 8px;
        }

        .nav a {
            text-decoration: none;
            color: #166534;
            font-weight: 600;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #cde9d2;
            background: #f3fff4;
            font-size: 28px;
            letter-spacing: 0.01em;
        }

        .nav a.active,
        .nav a:hover {
            background: forestgreen;
            color: #fff;
            border-color: forestgreen;
        }

        .right-tools {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .main {
            padding: 15px;
        }

        .flash {
            margin-bottom: 14px;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid;
            font-size: 14px;
            font-weight: 600;
        }

        .flash.success {
            background: #e8ffe8;
            border-color: #399339;
            color: #105b10;
        }

        .flash.error {
            background: #ffe9e9;
            border-color: #a93f3f;
            color: #7d1111;
        }

        input,
        select,
        button,
        textarea {
            font-family: inherit;
            font-size: 19px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 10px;
            transition: all 0.18s ease;
            background: #fff;
            color: var(--text-main);
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #86efac;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.16);
        }

        button {
            cursor: pointer;
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            font-weight: 600;
            padding: 12px 14px;
        }

        button:hover {
            filter: brightness(0.96);
            transform: translateY(-1px);
        }

        button[style*="b91c1c"] {
            background: var(--danger) !important;
            border-color: var(--danger) !important;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-card);
        }

        th,
        td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            font-size: 14px;
            text-align: left;
        }

        .table-card {
            background: var(--bg-card);
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: var(--shadow-soft);
            max-width: 100%;
            overflow-x: auto;
        }

        /* lebar kolom */
        .app-table { 
            width: 100%;
            min-width: 680px;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid var(--line);
            border-radius: var(--radius-sm);
            overflow: hidden;
            background: var(--bg-card);
        }   

        .app-table thead th {
            background: var(--bg-soft);
            color: #334155;
            font-size: 16px;
            font-weight: 700;
            border-bottom: 1px solid var(--line);
            text-transform: uppercase;
            letter-spacing: 0.02em;
        } /* Table Daftar User */

        .app-table th,
        .app-table td {
            padding: 10px 12px;
            border: none;
            border-bottom: 1px solid var(--line);
            vertical-align: top;
        }

        .app-table tbody tr:nth-child(even) {
            background: #fbfdff;
        }

        .app-table tbody tr:hover {
            background: #f8fafc;
        }

        .app-table tbody tr:last-child td {
            border-bottom: none;
        }

        h1,
        h2,
        h3 {
            margin: 8px 0 12px;
            color: #0f172a;
            letter-spacing: -0.01em;
        }

        .logout-form {
            margin: 0;
        }

        aside a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 12px 10px;
            border-radius: 8px;
            margin-bottom: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 19px;
            line-height: 1.25;
            border: 1px solid transparent;
            transition: all 0.15s ease;
        } /* Sidebar Font*/

        aside a:hover {
            background: rgba(22, 101, 52, 0.95);
            border-color: rgba(187, 247, 208, 0.3);
        }

        aside a.active {
            background: #22c55e;
            color: #064e3b;
            border-color: rgba(187, 247, 208, 0.6);
        }

        .menu-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 17px;
            height: 20px;
            flex-shrink: 0;
        }

        .menu-icon i {
            font-size: 16px;
            line-height: 1;
        }

        .layout-shell {
            display: flex;
            min-height: 100vh;
        }

        .layout-sidebar {
            width: 300px;
            background: linear-gradient(180deg, #14532d 0%, #166534 100%);
            color: #fff;
            padding: 16px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            flex-shrink: 0;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .layout-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        img,
        video,
        canvas,
        svg {
            max-width: 100%;
            height: auto;
        }

        form {
            max-width: 100%;
        }

        input,
        select,
        textarea {
            max-width: 100%;
        }

        .grid-form {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) !important;
        }

        .inline-form {
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)) !important;
        }

        .inline {
            grid-template-columns: minmax(180px, 1fr) minmax(120px, 220px) minmax(180px, 1fr) auto !important;
        }

        .table-toolbar,
        .report-filter-form,
        .export-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: end;
        }

        .report-filter-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(180px, 1fr)) auto auto;
        }

        .report-filter-form label {
            display: block;
            margin-bottom: 5px;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 600;
        }

        .report-filter-form input {
            width: 100%;
        }

        .app-table td>div[style*="display:flex"] {
            flex-wrap: wrap;
        }

        .app-table td>div[style*="display:flex"] form {
            min-width: 0;
        }

        .app-table th,
        .app-table td {
            font-size: 16px;
        }

        .btn-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 9px 12px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            text-decoration: none;
            color: #1f2937;
            background: #fff;
            font-weight: 600;
        }

        .export-actions {
            padding: 12px 16px;
        }

        .export-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 8px 16px;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .export-link.excel {
            background: #14532d;
        }

        .export-link.pdf {
            background: #1e529b;
        }

        @media (max-width: 1024px) {
            .layout-sidebar {
                width: 250px;
            }

            aside a {
                font-size: 14px;
                padding: 10px 9px;
            }

            .brand {
                font-size: 21px;
            }
        }

        @media (max-width: 768px) {
            .layout-shell {
                display: block;
            }

            .layout-sidebar {
                width: 100%;
                position: sticky;
                top: 0;
                height: auto;
                min-height: 0;
                max-height: 42vh;
                overflow: auto;
                z-index: 20;
                padding: 12px;
            }

            .layout-sidebar h2 {
                font-size: 20px !important;
                margin-bottom: 10px !important;
            }

            aside a {
                display: inline-flex;
                width: auto;
                margin: 0 6px 8px 0;
                white-space: nowrap;
                font-size: 13px;
            }

            .topbar {
                height: auto;
                min-height: 64px;
                padding: 12px 14px;
                gap: 10px;
                align-items: flex-start;
            }

            .right-tools {
                flex-shrink: 0;
            }

            .main {
                padding: 12px;
            }

            .table-card {
                padding: 16px;
                border-radius: 10px;
            }

            .app-table {
                min-width: 620px;
            }

            .report-filter-form {
                grid-template-columns: 1fr;
                align-items: stretch;
            }

            .inline {
                grid-template-columns: 1fr !important;
            }

            .report-filter-form button,
            .report-filter-form .btn-link,
            .export-actions a,
            button {
                width: 100%;
            }

            .table-toolbar {
                justify-content: stretch !important;
            }

            .table-toolbar select {
                width: 100%;
            }

            .app-table td>div[style*="display:flex"],
            .app-table td>div[style*="display:flex"] form {
                width: 100%;
            }

            h1 {
                font-size: 22px;
            }

            h2 {
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {
            body {
                font-size: 13px;
            }

            .brand {
                font-size: 18px;
            }

            .topbar {
                flex-direction: column;
            }

            .right-tools,
            .logout-form,
            .logout-form button {
                width: 100%;
            }

            .app-table {
                min-width: 560px;
            }

            .app-table th,
            .app-table td {
                padding: 8px 9px;
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    @php($activeMenu = trim($__env->yieldContent('active_menu')))

    <div class="layout-shell">

        <!-- SIDEBAR -->
        <aside class="layout-sidebar">
            <h2 style="margin-top:0; margin-bottom:20px; color:#ffffff; font-size:26px; letter-spacing:-0.01em;">SIM
                Meubel</h2>

            <a href="{{ route('dashboard') }}" class="{{ $activeMenu === 'dashboard' ? 'active' : '' }}">
                <span class="menu-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
                <span>Dashboard</span>
            </a>

            @if (auth()->user()?->role === 'admin')
                <a href="{{ route('users.index') }}" class="{{ $activeMenu === 'users' ? 'active' : '' }}">
                    <span class="menu-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                    <span>User Management</span>
                </a>
            @endif

            @if (in_array(auth()->user()?->role, ['admin', 'owner'], true))
                <a href="{{ route('products.index') }}" class="{{ $activeMenu === 'barang' ? 'active' : '' }}">
                    <span class="menu-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
                    <span>Data Barang</span>
                </a>
            @endif

            @if (in_array(auth()->user()?->role, ['admin', 'owner'], true))
                <a href="{{ route('stock-movements.incoming') }}" class="{{ $activeMenu === 'masuk' ? 'active' : '' }}">
                    <span class="menu-icon"><i class="bi bi-box-arrow-in-down" aria-hidden="true"></i></span>
                    <span>Barang Masuk</span>
                </a>

                <a href="{{ route('stock-movements.outgoing') }}" class="{{ $activeMenu === 'keluar' ? 'active' : '' }}">
                    <span class="menu-icon"><i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></span>
                    <span>Barang Keluar</span>
                </a>
            @endif

            @if (auth()->user()?->role === 'admin')
                <a href="{{ route('stock-movements.lost') }}" class="{{ $activeMenu === 'hilang' ? 'active' : '' }}">
                    <span class="menu-icon"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></span>
                    <span>Input Barang Hilang</span>
                </a>

                <a href="{{ route('reports.barang-hilang') }}"
                    class="{{ $activeMenu === 'reports-barang-hilang' ? 'active' : '' }}">
                    <span class="menu-icon"><i class="bi bi-file-earmark-x" aria-hidden="true"></i></span>
                    <span>Laporan Barang Hilang</span>
                </a>
            @endif

            @if (in_array(auth()->user()?->role, ['admin', 'owner'], true))
                <a href="{{ route('reports.pembelian') }}"
                    class="{{ $activeMenu === 'reports-pembelian' ? 'active' : '' }}">
                    <span class="menu-icon"><i class="bi bi-receipt" aria-hidden="true"></i></span>
                    <span>Laporan Pembelian</span>
                </a>
                <a href="{{ route('reports.rekap-pembelian') }}"
                    class="{{ $activeMenu === 'reports-rekap-pembelian' ? 'active' : '' }}">
                    <span class="menu-icon"><i class="bi bi-bar-chart-line" aria-hidden="true"></i></span>
                    <span>Rekap Pembelian</span>
                </a>
                <a href="{{ route('reports.penjualan') }}"
                    class="{{ $activeMenu === 'reports-penjualan' ? 'active' : '' }}">
                    <span class="menu-icon"><i class="bi bi-cash-coin" aria-hidden="true"></i></span>
                    <span>Laporan Penjualan</span>
                </a>
                <a href="{{ route('reports.rekap-penjualan') }}"
                    class="{{ $activeMenu === 'reports-rekap-penjualan' ? 'active' : '' }}">
                    <span class="menu-icon"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i></span>
                    <span>Rekap Penjualan</span>
                </a>
            @endif
        </aside>

        <!-- CONTENT -->
        <div class="layout-content">

            <!-- TOPBAR -->
            <header class="topbar">
                <!-- <h1 class="brand">Dashboard</h1> -->
                <h1 class="brand">@yield('title')</h1>

                <div class="right-tools">
                    <form class="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                </div>
            </header>

            <main class="main">
                @if (session('success'))
                    <div class="flash success">{{ session('success') }}</div>
                @endif

                @if (session('warning'))
                    <div class="flash error">{{ session('warning') }}</div>
                @endif

                @if ($errors->any())
                    <div class="flash error">{{ $errors->first() }}</div>
                @endif

                @yield('content')
            </main>

        </div>
    </div>
</body>

</html>