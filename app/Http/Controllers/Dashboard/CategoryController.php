<?php


namespace App\Http\Controllers\Dashboard;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categoryData = Category::all();
        return view('content.Dashboard.Master.category.index', compact('categoryData'));
    }

    public function store(Request $request)
    {
        // Generate kode otomatis
        $lastCode = DB::table('categories')->orderBy('KdCategory', 'desc')->first();
        if ($lastCode) {
            $lastNumber = intval(substr($lastCode->KdCategory, 3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        $KdCategory = 'CAT' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        $data = $request->validate([
            'categoryName'  => 'required'
        ]);

        $data['KdCategory'] = $KdCategory;

        Category::create($data);
        return redirect('/Category')->with('success', 'Anda Telah Berhasil Menambahkan Kategori');
    }

    public function edit(Category $category, $KdCategory)
    {
        $category = Category::where('KdCategory', $KdCategory)->firstOrFail();
        $categoryData = Category::all();
        return view('content.Dashboard.Master.category.index', compact('category', 'categoryData'));
    }

    public function update(Request $request, $KdCategory)
    {
        $categoryData = Category::where('KdCategory', $KdCategory)->firstOrFail();
        $data = $request->validate([
            'categoryName'  => 'required'
        ]);

        $categoryData->update($data);
        return redirect('/Category')->with('success', 'Anda Telah Berhasil Melakukan Update Kategori');
    }

    public function destroy($KdCategory)
    {
        $categoryData = Category::where('KdCategory', $KdCategory)->firstOrFail();
        $categoryData->delete();
        return redirect('/Category')->with('success', 'Anda Telah Berhasil Menghapus Data Kategori');
    }
}
