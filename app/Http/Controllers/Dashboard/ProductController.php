<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Unit;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProductController
 *
 * Controller ini bertanggung jawab dalam pengelolaan data produk
 * pada Dashboard, yang meliputi:
 * - Menampilkan daftar produk
 * - Menambahkan produk baru dengan kode otomatis
 * - Mengedit dan memperbarui data produk
 * - Menghapus data produk beserta file gambar
 *
 * @package App\Http\Controllers\Dashboard
 */
class ProductController extends Controller
{
    /**
     * Menampilkan halaman manajemen produk.
     *
     * Method ini berfungsi untuk:
     * - Mengambil data kategori (Kode dan Nama)
     * - Mengambil seluruh data produk
     * - Mengambil data unit (Kode dan Deskripsi)
     * - Mengirim data ke view produk
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil Data Id dan Nama Kategori dari models Category
        $categoryData       = Category::get(['KdCategory', 'categoryName']);
        // Mengambil Semua Data Product
        $productData        = Product::all();
        // Mengambil Id dan Deskripsi Unit dari Models Unit
        $unitsData          = Unit::get(['KdUnit', 'unitDescription']);
        return view('content.Dashboard.Master.Product.index', compact('productData', 'categoryData', 'unitsData'));
    }

    /**
     * Menyimpan data produk baru.
     *
     * Method ini melakukan:
     * - Validasi data input produk
     * - Pembuatan kode produk otomatis berdasarkan kategori
     * - Upload dan penyimpanan gambar produk (jika ada)
     * - Penyimpanan data produk ke database
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $data = $request->validate([
            'nameProduct'      => 'required|unique:products,nameProduct',
            'Photo'            => 'nullable|mimes:jpg,png,jpeg,svg,webp|max:2048',
            'stock'            => 'required|numeric|min:0',
            'KdCategory'      => 'required|exists:categories,KdCategory',
            'KdUnit'          => 'required|exists:units,KdUnit',
        ]);

        // Ambil nama kategori berdasarkan KdCategory
        $category = Category::find($data['KdCategory']);
        // Membuat prefix kode dari 3 huruf pertama nama kategori
        $prefix = strtoupper(substr($category->categoryName, 0, 3));

        // Mencari kode produk terakhir berdasarkan prefix kategori
        $lastProduct = Product::where('KdProduct', 'like', $prefix . '-%')
            ->orderBy('KdProduct', 'desc')
            ->first();

        // Menentukan kode produk baru
        if (!$lastProduct) {
            $kodeBaru = $prefix . '-0001';
        } else {
            $lastNumber = intval(substr($lastProduct->KdProduct, 4));
            $kodeBaru = $prefix . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        // Menambahkan kode produk ke data input
        $data['KdProduct'] = $kodeBaru;

        // Upload photo produk jika ada
        if ($request->hasFile('Photo')) {
            $data['Photo'] = $request->file('Photo')->store('images/Products', 'public');
        }

        // Menyimpan data produk ke database
        Product::create($data);

        // Redirect ke halaman produk dengan pesan sukses
        return redirect('/Product')->with('success', 'Produk berhasil ditambahkan dengan kode otomatis berdasarkan kategori.');
    }

    /**
     * Menampilkan halaman edit produk.
     *
     * Method ini:
     * - Mengambil data produk berdasarkan KdProduct
     * - Mengambil data kategori, unit, dan seluruh produk
     * - Mengirimkan data ke view untuk proses edit
     *
     * @param string $KdProduct
     * @return \Illuminate\View\View
     */
    public function edit($KdProduct)
    {
        // Mengambil data produk berdasarkan kode produk
        $product            = Product::findOrFail($KdProduct);
        // Mengambil Data Id dan Nama Kategori
        $categoryData       = Category::get(['KdCategory', 'categoryName']);
        // Mengambil Semua Data Product
        $productData        = Product::all();
        // Mengambil Id dan Deskripsi Unit
        $unitsData          = Unit::get(['KdUnit', 'unitDescription']);
        return view('content.Dashboard.Master.Product.index', compact('product', 'productData', 'categoryData', 'unitsData'));
    }

    /**
     * Memperbarui data produk.
     *
     * Method ini digunakan untuk:
     * - Melakukan validasi data produk
     * - Mengganti foto produk (jika ada)
     * - Menghapus foto lama saat diganti
     * - Memperbarui data produk di database
     *
     * @param Request $request
     * @param string $KdProduct
     * @return \Illuminate\Http\RedirectResponse
     */
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
            'stock'             => 'numeric|min:0',
            'KdCategory'       => 'required|exists:categories,KdCategory',
            'KdUnit'           => 'required|exists:units,KdUnit',
        ]);

        // Upload photo baru jika ada
        if ($request->hasFile('Photo')) {
            // Menghapus foto lama jika sebelumnya sudah ada
            if ($product->Photo) {
                Storage::disk('public')->delete($product->Photo);
            }
            $data['Photo'] = $request->file('Photo')->store('images/Products', 'public');
        }

        // Melakukan update data produk
        $product->update($data);

        // Redirect ke halaman produk dengan pesan sukses
        return redirect('/Product')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Menghapus data produk.
     *
     * Method ini:
     * - Mencari data produk berdasarkan KdProduct
     * - Menghapus file gambar produk jika ada
     * - Menghapus data produk dari database
     *
     * @param string $KdProduct
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($KdProduct)
    {
        // Melakukan pencarian data product
        $product  = Product::findOrFail($KdProduct);

        // Menghapus gambar produk jika tersedia
        if ($product->Photo) {
            Storage::delete($product->Photo);
        }

        // Menghapus data produk dari database
        $product->delete();

        // Redirect ke halaman produk dengan pesan sukses
        return redirect('/Product')->with('success', 'Anda Telah Menghapus Data Produk');
    }
}
