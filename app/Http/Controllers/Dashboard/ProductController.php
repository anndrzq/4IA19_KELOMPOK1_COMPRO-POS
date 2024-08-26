<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $categoryData   = Category::all();
        $productData    = Product::all();
        $unitsData       = Unit::all();
        return view('content.Dashboard.Master.Product.index', compact('productData', 'categoryData', 'unitsData'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'KdProduct'     => 'required|unique:products,KdProduct',
            'nameProduct'   => 'required|unique:products,nameProduct',
            'Photo'         => 'required|mimes:jpg,png,jpeg,svg,webp|max:2048',
            'stok'         => 'required|numeric',
            'price'         => 'required|numeric',
            'status'        => 'required',
            'category_id'   => 'required',
            'unit_id'       => 'required'
        ]);

        if ($request->hasFile('Photo')) {
            $data['Photo'] = $request->file('Photo')->store('images/Products', 'public');
        }

        Product::create($data);
        return redirect('/Product')->with('success', 'Anda Berhasil Menambahkan Produk');
    }
}
