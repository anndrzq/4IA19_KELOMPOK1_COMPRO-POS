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
            'status'    => 'required'
        ]);

        // Melakukan pembuatan members
        Members::Create($data);
        return redirect('/Member')->with('success', 'Anda Berhasil Menambahkan Users');
    }

    public function edit(Members $members, $uuid)
    {
        // Mengambil data berdasarkan UUID
        $Members        = Members::findOrFail($uuid);
        // Mengambil semua data member untuk tabel
        $dataMember     = Members::all();
        return view('content.Dashboard.Members.index', compact('Members', 'dataMember'));
    }

    public function update(Request $request, $uuid)
    {
        // Mencaari Data Members
        $Members    = Members::findOrFail($uuid);
        // Mengambil request inputan data
        $data = $request->validate([
            'name'      => 'required',
            'noWA'      => [
                'required',
                Rule::unique('members', 'noWA')->ignore($Members->uuid, 'uuid'),
            ],
            'email'     => [
                'required',
                'email:dns',
                Rule::unique('members', 'email')->ignore($Members->uuid, 'uuid'),
            ],
            'gender'    => 'required',
            'status'    => 'required',
        ]);
        // Melakukan Update
        $Members->update($data);
        return redirect('/Member')->with('success', 'Anda Berhasil Melakukan Update Members');
    }

    public function destroy($uuid)
    {
        // Melakukan pencarian data member bedasarkan uuid
        $Members = Members::findOrFail($uuid);
        // Melakukan Penghapusan
        $Members->delete();
        return redirect('/Member')->with('success', 'Anda Berhasil Menghapus Data Member');
    }
}
