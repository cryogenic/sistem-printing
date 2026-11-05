<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Paparkan senarai produk
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    // Paparkan borang tambah produk baru
    public function create()
    {
        return view('products.create');
    }

    // Simpan data produk ke pangkalan data
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pricing_type' => 'required|in:per_sqft,per_piece',
            'base_price' => 'required|numeric|min:0',
        ]);

        Product::create([
            'name' => $request->name,
            'pricing_type' => $request->pricing_type,
            'base_price' => $request->base_price,
            'is_active' => true,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berjaya ditambah!');
    }
}