<?php

/**
 * UnitController
 * --------------
 * Controller ini digunakan untuk mengelola data Satuan Unit
 * pada Dashboard (Master Data Unit).
 * 
 * Fungsionalitas utama:
 * - Menampilkan data unit
 * - Menambahkan unit dengan kode otomatis
 * - Mengedit data unit
 * - Memperbarui data unit
 * - Menghapus data unit
 */

namespace App\Http\Controllers\Dashboard;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UnitController extends Controller
{
    /**
     * Menampilkan halaman utama master data Unit
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil semua data unit dari database
        $unitData = Unit::all();

        // Mengirim data unit ke view
        return view('content.Dashboard.Master.unit.index', compact('unitData'));
    }

    /**
     * Menyimpan data unit baru ke database
     * dengan kode unit yang dibuat secara otomatis
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Mengambil kode unit terakhir dari database
        $lastCode = DB::table('units')->orderBy('KdUnit', 'desc')->first();

        // Menentukan nomor urut kode unit berikutnya
        if ($lastCode) {
            $lastNumber = intval(substr($lastCode->KdUnit, 3));
            $newNumber = $lastNumber + 1;
        } else {
            // Jika belum ada data, mulai dari nomor 1
            $newNumber = 1;
        }

        // Membuat kode unit dengan format UNTXXX
        $KdUnit = 'UNT' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        // Validasi input data unit
        $data = $request->validate([
            'unitDescription'   => 'required'
        ]);

        // Menambahkan kode unit otomatis ke data
        $data['KdUnit'] = $KdUnit;

        // Menyimpan data unit ke database
        Unit::create($data);

        // Redirect ke halaman unit dengan pesan sukses
        return redirect('/Unit')->with('success', 'Anda Berhasil Menambahkan Satuan Unit');
    }

    /**
     * Menampilkan data unit yang akan diedit
     *
     * @param Unit $unit
     * @param string $KdUnit
     * @return \Illuminate\View\View
     */
    public function edit(Unit $unit, $KdUnit)
    {
        // Mengambil data unit berdasarkan kode unit
        $Unit = Unit::where('KdUnit', $KdUnit)->firstOrFail();

        // Mengambil seluruh data unit untuk ditampilkan di tabel
        $unitData = Unit::all();

        // Mengirim data ke view
        return view('content.Dashboard.Master.unit.index', compact('Unit', 'unitData'));
    }

    /**
     * Memperbarui data unit berdasarkan kode unit
     *
     * @param Request $request
     * @param string $KdUnit
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $KdUnit)
    {
        // Mengambil data unit berdasarkan kode unit
        $unitData = Unit::where('KdUnit', $KdUnit)->firstOrFail();

        // Validasi data input unit
        $data = $request->validate([
            'unitDescription'   => 'required'
        ]);

        // Melakukan update data unit
        $unitData->update($data);

        // Redirect ke halaman unit dengan pesan sukses
        return redirect('/Unit')->with('success', 'Anda Berhasil Mengupdate Unit Data');
    }

    /**
     * Menghapus data unit berdasarkan kode unit
     *
     * @param string $KdUnit
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($KdUnit)
    {
        // Mengambil data unit berdasarkan kode unit
        $unitData = Unit::where('KdUnit', $KdUnit)->firstOrFail();

        // Menghapus data unit dari database
        $unitData->delete();

        // Redirect ke halaman unit dengan pesan sukses
        return redirect('/Unit')->with('success', 'Anda Berhasil Menghapus Unit Data');
    }
}
