<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM Stok Mebel</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: radial-gradient(circle at top right, #dcfce7 0%, #f1f5f9 40%, #f8fafc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .card {
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            border: 1px solid #dbe2ea;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
            padding: 30px 45px;
        }
        h1 {
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 40px;
            color: #14532d;
            letter-spacing: -0.02em;
        }
        p.subtitle {
            margin-top: 0;
            margin-bottom: 20px;
            color: #6b7280;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #374151;
        }
        input {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 11px 12px;
            margin-bottom: 14px;
            font-size: 16px;
            transition: all 0.18s ease;
        }
        input:focus {
            outline: none;
            border-color: #86efac;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.16);
        }
        button {
            width: 100%;
            background-color: forestgreen;
            color: #fff;
            border: 0;
            border-radius: 10px;
            padding: 12px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.16s ease;
        }
        button:hover {
            filter: brightness(0.96);
            transform: translateY(-1px);
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 14px;
        }
        .hint {
            margin-top: 16px;
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1><center>SIM Mebel</center></h1>
        <p class="hint" style="text-align:center; font-size:16px; color:grey; font-weight:700;"></p>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login.store') }}" method="POST">
            @csrf

            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>

            <button type="submit">Login</button>
        </form>

    </div>
</body>
</html>
