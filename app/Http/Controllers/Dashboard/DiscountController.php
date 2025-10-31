<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Discount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::all();
        return view('content.Dashboard.Master.discount.index', compact('discounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Discount::create($data);
        return redirect('/Discount')->with('success', 'Diskon Berhasil Di Buat');
    }

    public function edit($id)
    {
        $discount = Discount::where('id', $id)->firstOrFail();
        $discounts = Discount::all();
        return view('content.Dashboard.Master.discount.index', compact('discount', 'discounts'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $discount = Discount::where('id', $id)->firstOrFail();
        $discount->update($data);
        return redirect('/Discount')->with('success', 'Diskon Berhasil Di Perbarui');
    }

    public function destroy($id)
    {
        $discount = Discount::where('id', $id)->firstOrFail();
        $discount->delete();
        return redirect('/Discount')->with('success', 'Diskon Berhasil Di Hapus');
    }
}
