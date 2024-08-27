<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Unit;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        // Mengambil Data Id dan Nama Kategori dari models Category
        $categoryData       = Category::get(['id', 'categoryName']);
        // Mengambil Semua Data Product
        $productData        = Product::all();
        // Mengambil Id dan Deskripsi Unit dari Models Unit
        $unitsData          = Unit::get(['id', 'unitDescription']);
        return view('content.Dashboard.Master.Product.index', compact('productData', 'categoryData', 'unitsData'));
    }

    public function store(Request $request)
    {

        // Melakukan Validasi Request dari Form
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
        // Kondisi dimana jika ada request file dari foto maka lakukan penyimpanan
        if ($request->hasFile('Photo')) {
            $data['Photo'] = $request->file('Photo')->store('images/Products', 'public');
        }
        // Melakukan Pembuatan Data Product
        Product::create($data);
        return redirect('/Product')->with('success', 'Anda Berhasil Menambahkan Produk');
    }

    public function edit($KdProduct)
    {
        $product            = Product::findOrFail($KdProduct);
        // Mengambil Data Id dan Nama Kategori dari models Category
        $categoryData       = Category::get(['id', 'categoryName']);
        // Mengambil Semua Data Product
        $productData        = Product::all();
        // Mengambil Id dan Deskripsi Unit dari Models Unit
        $unitsData          = Unit::get(['id', 'unitDescription']);
        return view('content.Dashboard.Master.Product.index', compact('product', 'productData', 'categoryData', 'unitsData'));
    }

    public function update(Request $request, $KdProduct)
    {
        // dd($request);
        // Melakukan Pencarian Data Product
        $product            = Product::findOrFail($KdProduct);
        // Melakuakan Validasi Request Input Ulang
        $data = $request->validate([
            'KdProduct'           => [
                'required',
                Rule::unique('products', 'KdProduct')->ignore($KdProduct, 'KdProduct'),
            ],
            'nameProduct'           => [
                'required',
                Rule::unique('products', 'nameProduct')->ignore($KdProduct, 'KdProduct'),
            ],
            'Photo'         => 'nullable|mimes:jpg,png,jpeg,svg,webp|max:2048',
            'stok'          => 'required|numeric',
            'price'         => 'required|numeric',
            'status'        => 'required',
            'category_id'   => 'required',
            'unit_id'       => 'required'
        ]);
        // Melakukan Validasi Gambar
        if ($request->hasFile('Photo')) {
            // Hapus Gambar Lama
            if ($product->Photo) {
                Storage::delete($product->Photo);
            }

            // Upload Gambar Baru
            $data['Photo'] = $request->file('Photo')->store('images/Products', 'public');
        }

        $product->update($data);
        return redirect('/Product')->with('success', 'Anda Berhasil Melakukan Update Data Produk');
    }


    public function destroy($KdProduct)
    {
        // Melakukan pencarian data product
        $product  = Product::findOrFail($KdProduct);
        // Menghapus Gambar jika dia punya gambar
        if ($product->Photo) {
            Storage::delete($product->Photo);
        }
        // Melakukan Penghapusan Data Product
        $product->delete();
        return redirect('/Product')->with('success', 'Anda Telah Menghapus Data Produk');
    }
}
