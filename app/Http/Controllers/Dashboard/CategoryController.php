<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

/**
 * Class CategoryController
 *
 * Controller ini digunakan untuk mengelola data kategori produk
 * pada sistem dashboard, meliputi:
 * - Menampilkan daftar kategori
 * - Menambahkan kategori baru
 * - Mengubah data kategori
 * - Menghapus data kategori
 *
 * @package App\Http\Controllers\Dashboard
 */
class CategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori.
     *
     * Method ini mengambil seluruh data kategori dari database
     * dan menampilkannya pada halaman master kategori.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categoryData = Category::all();
        return view('content.Dashboard.Master.category.index', compact('categoryData'));
    }

    /**
     * Menyimpan data kategori baru.
     *
     * Method ini akan:
     * - Membuat kode kategori otomatis (CATxxx)
     * - Melakukan validasi input
     * - Menyimpan data kategori ke database
     * - Melakukan redirect dengan pesan sukses
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Generate kode kategori secara otomatis
        $lastCode = DB::table('categories')->orderBy('KdCategory', 'desc')->first();
        if ($lastCode) {
            $lastNumber = intval(substr($lastCode->KdCategory, 3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format kode kategori (CAT001, CAT002, dst)
        $KdCategory = 'CAT' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        // Validasi input data kategori
        $data = $request->validate([
            'categoryName'  => 'required'
        ]);

        // Menambahkan kode kategori ke data yang akan disimpan
        $data['KdCategory'] = $KdCategory;

        // Menyimpan kategori ke database
        Category::create($data);

        // Redirect ke halaman kategori dengan pesan sukses
        return redirect('/Category')
            ->with('success', 'Anda Telah Berhasil Menambahkan Kategori');
    }

    /**
     * Menampilkan halaman edit kategori.
     *
     * Method ini mengambil data kategori berdasarkan kode kategori
     * serta menampilkan seluruh data kategori untuk kebutuhan tampilan.
     *
     * @param Category $category
     * @param string $KdCategory
     * @return \Illuminate\View\View
     */
    public function edit(Category $category, $KdCategory)
    {
        $category = Category::where('KdCategory', $KdCategory)->firstOrFail();
        $categoryData = Category::all();

        return view('content.Dashboard.Master.category.index', compact('category', 'categoryData'));
    }

    /**
     * Memperbarui data kategori.
     *
     * Method ini melakukan:
     * - Validasi input
     * - Pencarian data kategori berdasarkan kode
     * - Update data kategori di database
     *
     * @param Request $request
     * @param string $KdCategory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $KdCategory)
    {
        $categoryData = Category::where('KdCategory', $KdCategory)->firstOrFail();

        // Validasi data yang akan diupdate
        $data = $request->validate([
            'categoryName'  => 'required'
        ]);

        // Update data kategori
        $categoryData->update($data);

        // Redirect ke halaman kategori dengan pesan sukses
        return redirect('/Category')
            ->with('success', 'Anda Telah Berhasil Melakukan Update Kategori');
    }

    /**
     * Menghapus data kategori.
     *
     * Method ini akan:
     * - Mencari kategori berdasarkan kode kategori
     * - Menghapus data kategori dari database
     * - Melakukan redirect dengan pesan sukses
     *
     * @param string $KdCategory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($KdCategory)
    {
        $categoryData = Category::where('KdCategory', $KdCategory)->firstOrFail();

        // Menghapus data kategori
        $categoryData->delete();

        // Redirect ke halaman kategori dengan pesan sukses
        return redirect('/Category')
            ->with('success', 'Anda Telah Berhasil Menghapus Data Kategori');
    }
}
