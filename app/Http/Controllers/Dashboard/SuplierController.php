<?php


namespace App\Http\Controllers\Dashboard;

use App\Models\Suplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class SuplierController extends Controller
{

    public function index()
    {
        // Mengambil Semua Data Di Supliers
        $SuppliersData = Suplier::all();
        return view('content.Dashboard.Master.Suppliers.index', compact('SuppliersData'));
    }


    public function store(Request $request)
    {
        // Generate kode otomatis
        $lastCode = DB::table('supliers')->orderBy('kdSuppliers', 'desc')->first();
        if ($lastCode) {
            $lastNumber = intval(substr($lastCode->kdSuppliers, 3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        $kdSuppliers = 'SUP' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        // Validasi data selain kdSuppliers
        $data = $request->validate([
            'suppliersName'   => 'required|unique:supliers,suppliersName',
            'contactWhatsapp' => 'required|unique:supliers,contactWhatsapp',
            'contactEmail'    => 'required|email:dns|unique:supliers,contactEmail',
            'address'         => 'required',
            'status'          => 'required'
        ]);

        // Tambahkan kode otomatis ke data yang akan disimpan
        $data['kdSuppliers'] = $kdSuppliers;

        // Simpan data ke database
        Suplier::create($data);

        return back()->with('success', 'Anda Berhasil Menambahkan Data Suppliers dengan kode otomatis!');
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

        // Validasi data (kdSuppliers tidak perlu divalidasi karena tidak diubah user)
        $data = $request->validate([
            'suppliersName' => [
                'required',
                Rule::unique('supliers', 'suppliersName')->ignore($supplier->kdSuppliers, 'kdSuppliers'),
            ],
            'contactWhatsapp' => [
                'required',
                Rule::unique('supliers', 'contactWhatsapp')->ignore($supplier->kdSuppliers, 'kdSuppliers'),
            ],
            'contactEmail' => [
                'required',
                'email:dns',
                Rule::unique('supliers', 'contactEmail')->ignore($supplier->kdSuppliers, 'kdSuppliers'),
            ],
            'address' => 'required',
            'status' => 'required'
        ]);

        // Update data ke database
        $supplier->update($data);

        return redirect('/Suplier')->with('success', 'Anda Berhasil Melakukan Update Data Supplier!');
    }

    public function destroy(Suplier $suplier, $kdSuppliers)
    {
        // Mengambil Data Berdasarkan Kode Supplier
        $SuppliersData = Suplier::where('kdSuppliers', $kdSuppliers)->firstOrFail();
        // Menghapus Data Suppliers
        $SuppliersData->delete();
        return redirect('/Suplier')->with('success', 'Anda Berhasil Menghapus Data Suppliers');
    }
}
