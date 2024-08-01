<?php


namespace App\Http\Controllers\Dashboard;

use App\Models\Category;
use Illuminate\Http\Request;
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
        $data = $request->validate([
            'kdCategory'    => 'required|min:1|unique:categories,kdCategory',
            'categoryName'  => 'required'
        ]);

        Category::create($data);
        return redirect('/Category')->with('success', 'Anda Telah Berhasil Menambahkan Kategori');
    }

    public function edit(Category $category, $kdCategory)
    {
        $category = Category::where('kdCategory', $kdCategory)->firstOrFail();
        $categoryData = Category::all();
        return view('content.Dashboard.Master.category.index', compact('category', 'categoryData'));
    }

    public function update(Request $request, $kdCategory)
    {
        $categoryData = Category::where('kdCategory', $kdCategory)->firstOrFail();
        $data = $request->validate([
            'kdCategory'    => 'required|min:1|unique:categories,kdCategory,' . $categoryData->id,
            'categoryName'  => 'required'
        ]);

        $categoryData->update($data);
        return redirect('/Category')->with('success', 'Anda Telah Berhasil Melakukan Update Kategori');
    }

    public function destroy($kdCategory)
    {
        $categoryData = Category::where('kdCategory', $kdCategory)->firstOrFail();
        $categoryData->delete();
        return redirect('/Category')->with('success', 'Anda Telah Berhasil Menghapus Data Kategori');
    }
}
