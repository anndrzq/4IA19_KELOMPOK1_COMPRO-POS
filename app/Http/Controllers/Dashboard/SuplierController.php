<?php

/**
 * SuplierController
 * -----------------
 * Controller ini bertanggung jawab untuk mengelola data supplier
 * pada halaman Dashboard (Master Data Suppliers).
 * 
 * Fitur utama:
 * - Menampilkan data supplier
 * - Menambah supplier dengan kode otomatis
 * - Mengedit data supplier
 * - Menghapus data supplier
 */

namespace App\Http\Controllers\Dashboard;

use App\Models\Suplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SuplierController extends Controller
{
    /**
     * Menampilkan halaman utama data supplier
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil semua data supplier dari database
        $SuppliersData = Suplier::all();

        // Mengirim data supplier ke view
        return view('content.Dashboard.Master.Suppliers.index', compact('SuppliersData'));
    }

    /**
     * Menyimpan data supplier baru ke database
     * dengan kode supplier otomatis
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Mengambil kode supplier terakhir berdasarkan urutan descending
        $lastCode = DB::table('supliers')->orderBy('kdSuppliers', 'desc')->first();

        // Jika data supplier sudah ada, ambil nomor terakhir dan tambahkan 1
        if ($lastCode) {
            $lastNumber = intval(substr($lastCode->kdSuppliers, 3));
            $newNumber = $lastNumber + 1;
        } else {
            // Jika belum ada data, mulai dari 1
            $newNumber = 1;
        }

        // Membuat kode supplier baru dengan format SUPXXX
        $kdSuppliers = 'SUP' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        // Validasi input data supplier
        $data = $request->validate([
            'suppliersName'   => 'required|unique:supliers,suppliersName',
            'contactWhatsapp' => 'required|unique:supliers,contactWhatsapp',
            'contactEmail'    => 'required|email:dns|unique:supliers,contactEmail',
            'address'         => 'required',
            'status'          => 'required'
        ]);

        // Menambahkan kode supplier otomatis ke data yang akan disimpan
        $data['kdSuppliers'] = $kdSuppliers;

        // Menyimpan data supplier ke database
        Suplier::create($data);

        // Redirect kembali dengan pesan sukses
        return back()->with('success', 'Anda Berhasil Menambahkan Data Suppliers dengan kode otomatis!');
    }

    /**
     * Menampilkan data supplier yang akan diedit
     *
     * @param Suplier $suplier
     * @param string $kdSuppliers
     * @return \Illuminate\View\View
     */
    public function edit(Suplier $suplier, $kdSuppliers)
    {
        // Mencari data supplier berdasarkan kode supplier
        $supplier = Suplier::findOrFail($kdSuppliers);

        // Mengambil seluruh data supplier untuk ditampilkan pada tabel
        $SuppliersData = Suplier::all();

        // Mengirim data ke view
        return view('content.Dashboard.Master.Suppliers.index', compact('supplier', 'SuppliersData'));
    }

    /**
     * Memperbarui data supplier berdasarkan kode supplier
     *
     * @param Request $request
     * @param string $kdSuppliers
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $kdSuppliers)
    {
        // Mengambil data supplier berdasarkan kode supplier
        $supplier = Suplier::where('kdSuppliers', $kdSuppliers)->firstOrFail();

        // Validasi data input (kode supplier tidak diubah)
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

        // Melakukan update data supplier
        $supplier->update($data);

        // Redirect ke halaman supplier dengan pesan sukses
        return redirect('/Suplier')->with('success', 'Anda Berhasil Melakukan Update Data Supplier!');
    }

    /**
     * Menghapus data supplier berdasarkan kode supplier
     *
     * @param Suplier $suplier
     * @param string $kdSuppliers
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Suplier $suplier, $kdSuppliers)
    {
        // Mencari data supplier berdasarkan kode supplier
        $SuppliersData = Suplier::where('kdSuppliers', $kdSuppliers)->firstOrFail();

        // Menghapus data supplier dari database
        $SuppliersData->delete();

        // Redirect ke halaman supplier dengan pesan sukses
        return redirect('/Suplier')->with('success', 'Anda Berhasil Menghapus Data Suppliers');
    }
}
