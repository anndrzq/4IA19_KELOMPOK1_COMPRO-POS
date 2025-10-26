<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Members;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class MembersController extends Controller
{
    public function index()
    {
        // Mengambil semua data member
        $dataMember = Members::all();
        return view('content.Dashboard.Members.index', compact('dataMember'));
    }

    public function store(Request $request)
    {

        // Mengambil request inputan data
        $data = $request->validate([
            'name'      => 'required',
            'noWA'      => 'required|unique:members',
            'email'     => 'required|email:dns',
            'gender'    => 'required',
        ]);

        // Melakukan pembuatan members
        Members::Create($data);
        return redirect('/Member')->with('success', 'Anda Berhasil Menambahkan Users');
    }

    public function edit(Members $members, $id)
    {
        // Mengambil data berdasarkan id
        $Members        = Members::findOrFail($id);
        // Mengambil semua data member untuk tabel
        $dataMember     = Members::all();
        return view('content.Dashboard.Members.index', compact('Members', 'dataMember'));
    }

    public function update(Request $request, $id)
    {
        // Mencaari Data Members
        $Members    = Members::findOrFail($id);
        // Mengambil request inputan data
        $data = $request->validate([
            'name'      => 'required',
            'noWA'      => [
                'required',
                Rule::unique('members', 'noWA')->ignore($Members->id, 'id'),
            ],
            'email'     => [
                'required',
                'email:dns',
                Rule::unique('members', 'email')->ignore($Members->id, 'id'),
            ],
            'gender'    => 'required',
        ]);
        // Melakukan Update
        $Members->update($data);
        return redirect('/Member')->with('success', 'Anda Berhasil Melakukan Update Members');
    }

    public function destroy($id)
    {
        // Melakukan pencarian data member bedasarkan id
        $Members = Members::findOrFail($id);
        // Melakukan Penghapusan
        $Members->delete();
        return redirect('/Member')->with('success', 'Anda Berhasil Menghapus Data Member');
    }
}
