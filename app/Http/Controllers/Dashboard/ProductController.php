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
        // Validasi input
        $data = $request->validate([
            'nameProduct'      => 'required|unique:products,nameProduct',
            'Photo'            => 'nullable|mimes:jpg,png,jpeg,svg,webp|max:2048',
            'stock'            => 'required|numeric|min:0',
            'purchase_price'   => 'required|numeric|min:0',
            'markup_percentage' => 'required|numeric|min:0',
            'category_id'      => 'required|exists:categories,id',
            'unit_id'          => 'required|exists:units,id',
        ]);

        // Ambil nama kategori berdasarkan category_id
        $category = Category::find($data['category_id']);
        $prefix = strtoupper(substr($category->categoryName, 0, 3)); // 3 huruf pertama kategori

        // Cari kode terakhir berdasarkan prefix kategori
        $lastProduct = Product::where('KdProduct', 'like', $prefix . '-%')
            ->orderBy('KdProduct', 'desc')
            ->first();

        if (!$lastProduct) {
            $kodeBaru = $prefix . '-0001';
        } else {
            $lastNumber = intval(substr($lastProduct->KdProduct, 4));
            $kodeBaru = $prefix . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        // Tambahkan kode produk ke data
        $data['KdProduct'] = $kodeBaru;

        // Hitung harga otomatis
        $data['price'] = $data['purchase_price'] + ($data['purchase_price'] * $data['markup_percentage'] / 100);

        // Upload photo jika ada
        if ($request->hasFile('Photo')) {
            $data['Photo'] = $request->file('Photo')->store('images/Products', 'public');
        }

        Product::create($data);

        return redirect('/Product')->with('success', 'Produk berhasil ditambahkan dengan kode otomatis berdasarkan kategori.');
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
        // Cari produk berdasarkan primary key KdProduct
        $product = Product::findOrFail($KdProduct);

        // Validasi input
        $data = $request->validate([
            'nameProduct' => [
                'required',
                Rule::unique('products', 'nameProduct')->ignore($KdProduct, 'KdProduct'),
            ],
            'Photo'             => 'nullable|mimes:jpg,png,jpeg,svg,webp|max:2048',
            'stock'             => 'required|numeric|min:0',
            'purchase_price'    => 'required|numeric|min:0',
            'markup_percentage' => 'required|numeric|min:0',
            'category_id'       => 'required|exists:categories,id',
            'unit_id'           => 'required|exists:units,id',
        ]);

        // Hitung ulang harga jual berdasarkan harga beli & persentase
        $data['price'] = $data['purchase_price'] + ($data['purchase_price'] * $data['markup_percentage'] / 100);

        // Upload photo jika ada
        if ($request->hasFile('Photo')) {
            // Hapus foto lama jika ada
            if ($product->Photo) {
                Storage::disk('public')->delete($product->Photo);
            }
            $data['Photo'] = $request->file('Photo')->store('images/Products', 'public');
        }

        // Update data produk
        $product->update($data);

        return redirect('/Product')->with('success', 'Produk berhasil diperbarui.');
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
