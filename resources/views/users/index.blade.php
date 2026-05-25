@extends('layouts.sidebar')

@section('title', 'User Management')
@section('active_menu', 'users')

@section('content')
    <style>
        .inline-user-form {
            display: grid;
            grid-template-columns: 1fr 1fr 120px 1fr auto;
            gap: 8px;
        }
    </style>

    <div class="table-card">
        <h2>Tambah User</h2>
        <form action="{{ route('users.store') }}" method="POST" style="display:grid; gap:10px;">
            @csrf
            <input type="text" name="name" placeholder="Nama" value="{{ old('name') }}" required>
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
            <input type="password" name="password" placeholder="Password (min. 6 karakter)" required>
            <select name="role" required>
                <option value="admin">Admin</option>
                <option value="owner">Owner</option>
            </select>
            <div>
                <button type="submit">Simpan</button>
            </div>
        </form>
    </div>

    <div class="table-card">
        <h2>Daftar User</h2>
        <table class="app-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $item)
                    <tr>
                        <td colspan="4">
                            <div style="display:flex; align-items:start; gap:8px;">
                                <form action="{{ route('users.update', $item) }}" method="POST" class="inline-user-form" style="flex:1;">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $item->name }}" required>
                                    <input type="email" name="email" value="{{ $item->email }}" required>
                                    <select name="role" required>
                                        <option value="admin" @selected($item->role === 'admin')>Admin</option>
                                        <option value="owner" @selected($item->role === 'owner')>Owner</option>
                                    </select>
                                    <input type="password" name="password" placeholder="Password baru (opsional)">
                                    <button type="submit">Update</button>
                                </form>

                                <form action="{{ route('users.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin hapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:#b91c1c; border-color:#b91c1c;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Belum ada user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
