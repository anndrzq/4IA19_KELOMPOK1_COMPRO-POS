<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Suplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;


class SuplierController extends Controller
{

    public function index()
    {
        // Mengambil Semua Data Di Supliers
        $SuppliersData = Suplier::all();
        return view('content.Dashboard.Master.Suppliers.index', compact('SuppliersData'));
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // Melakukan Validasi Data
        $data = $request->validate([
            'kdSuppliers'           => 'required|min:6|unique:supliers,kdSuppliers',
            'suppliersName'         => 'required|unique:supliers,suppliersName',
            'contactWhatsapp'       => 'required|unique:supliers,contactWhatsapp',
            'contactEmail'          => 'required|email:dns|unique:supliers,contactEmail',
            'address'               => 'required',
            'status'                => 'required'
        ]);
        // Melakukan Create Data
        Suplier::create($data);
        return back()->with('success', 'Anda Berhasil Menambahkan Data Suppliers');
    }

    public function show(Suplier $suplier)
    {
        //
    }

    public function edit(Suplier $suplier, $kdSuppliers)
    {
        // Melakukan pencarian berdasarkan Kode Supliers
        $supplier = Suplier::findOrFail($kdSuppliers);
        // Mengambil semua data yang ada pada suppliers
        $SuppliersData = Suplier::all();
        return view('content.Dashboard.Master.Suppliers.index', compact('supplier', 'SuppliersData'));
    }

    public function update(Request $request, $kdSuppliers)
    {
        // Ambil data supplier berdasarkan kdSuppliers
        $supplier = Suplier::where('kdSuppliers', $kdSuppliers)->firstOrFail();

        // Validasi data dengan pengecualian pada aturan unique
        $data = $request->validate([
            'kdSuppliers'           => [
                'required',
                'min:6',
                Rule::unique('supliers', 'kdSuppliers')->ignore($kdSuppliers, 'kdSuppliers'),
            ],
            'suppliersName'         => [
                'required',
                Rule::unique('supliers', 'suppliersName')->ignore($kdSuppliers, 'kdSuppliers'),
            ],
            'contactWhatsapp'       => [
                'required',
                Rule::unique('supliers', 'contactWhatsapp')->ignore($kdSuppliers, 'kdSuppliers'),
            ],
            'contactEmail'          => [
                'required',
                'email:dns',
                Rule::unique('supliers', 'contactEmail')->ignore($kdSuppliers, 'kdSuppliers'),
            ],
            'address'               => 'required',
            'status'                => 'required'
        ]);

        // Perbarui data supplier
        $supplier->update($data);
        return redirect('/Suplier')->with('success', 'Anda Berhasil Melakukan Update Data Suppliers');
    }

    public function destroy(Suplier $suplier)
    {
        //
    }
}
