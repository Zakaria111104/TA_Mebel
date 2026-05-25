<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin();

        $opsiUrut = $request->input('urut', 'nama_asc');
        $query = Product::query();

        if ($opsiUrut === 'lama_baru') {
            $query->orderBy(Product::columnDibuat(), 'asc')->orderBy('id');
        } else {
            $opsiUrut = 'nama_asc';
            $query->orderBy('nama')->orderBy('id');
        }

        return view('products.index', [
            'products' => $query->get(),
            'opsiUrut' => $opsiUrut,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50', 'unique:barang,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'stok' => ['required', 'integer', 'min:0'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50', 'unique:barang,kode,' . $product->id],
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'stok' => ['required', 'integer', 'min:0'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->ensureAdmin();

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Barang berhasil dihapus.');
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }
}
