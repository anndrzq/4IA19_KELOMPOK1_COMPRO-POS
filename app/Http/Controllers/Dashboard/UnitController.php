<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UnitController extends Controller
{
    public function index()
    {
        // Mengambil Semua Data Di Dalam Model Unit
        $unitData = Unit::all();
        return view('content.Dashboard.Master.unit.index', compact('unitData'));
    }

    public function store(Request $request)
    {
        // Mengambil Request untuk Validasi
        $data = $request->validate([
            'kdUnit'            => 'required|min:1|unique:units,kdUnit',
            'unitDescription'   => 'required'
        ]);
        // Melakukan Create Unit Berdasarkan Data Validasi
        Unit::create($data);
        return redirect('/Unit')->with('success', 'Anda Berhasil Menambahkan Satuan Unit');
    }

    public function edit(Unit $unit, $kdUnit)
    {
        // Mengambil Data Dimana Data Berdasarkan Kode Unit
        $Unit = Unit::where('kdUnit', $kdUnit)->firstOrFail();
        // Mengambil Semua Data Di Model Unit
        $unitData = Unit::all();
        return view('content.Dashboard.Master.unit.index', compact('Unit', 'unitData'));
    }

    public function update(Request $request, $kdUnit)
    {
        // Mengambil data Kode Unit
        $unitData = Unit::where('kdUnit', $kdUnit)->firstOrFail();
        // Melakukan Validasi Data
        $data = $request->validate([
            'kdUnit'            => 'required|min:1|unique:units,kdUnit,' . $unitData->id,
            'unitDescription'   => 'required'
        ]);
        // Melakukan Update Data
        $unitData->update($data);
        return redirect('/Unit')->with('success', 'Anda Berhasil Mengupdate Unit Data');
    }

    public function destroy($kdUnit)
    {
        // Mengambil data Kode Unit yang sesuai
        $unitData = Unit::where('kdUnit', $kdUnit)->firstOrFail();
        // Menghapus Data
        $unitData->delete();
        return redirect('/Unit')->with('success', 'Anda Berhasil Menghapus Unit Data');
    }
}
