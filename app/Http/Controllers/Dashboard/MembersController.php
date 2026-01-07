<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Members;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

/**
 * Class MembersController
 *
 * Controller ini digunakan untuk mengelola data member
 * pada Dashboard, yang meliputi:
 * - Menampilkan daftar member
 * - Menambahkan member baru
 * - Mengedit data member
 * - Memperbarui data member
 * - Menghapus data member
 *
 * @package App\Http\Controllers\Dashboard
 */
class MembersController extends Controller
{
    /**
     * Menampilkan daftar member.
     *
     * Method ini mengambil seluruh data member dari database
     * dan mengirimkannya ke view untuk ditampilkan
     * pada halaman manajemen member.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil semua data member
        $dataMember = Members::all();
        return view('content.Dashboard.Members.index', compact('dataMember'));
    }

    /**
     * Menyimpan data member baru.
     *
     * Method ini berfungsi untuk:
     * - Melakukan validasi input data member
     * - Menyimpan data member ke database
     * - Redirect ke halaman member dengan pesan sukses
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Mengambil request inputan data dan melakukan validasi
        $data = $request->validate([
            'name'      => 'required',
            'noWA'      => 'required|unique:members',
            'email'     => 'required|email:dns',
            'gender'    => 'required',
        ]);

        // Melakukan pembuatan data member
        Members::create($data);

        // Redirect ke halaman member dengan pesan sukses
        return redirect('/Member')->with('success', 'Anda Berhasil Menambahkan Users');
    }

    /**
     * Menampilkan halaman edit member.
     *
     * Method ini:
     * - Mengambil data member berdasarkan ID
     * - Mengambil seluruh data member untuk keperluan tabel
     * - Mengirimkan data ke view
     *
     * @param Members $members
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(Members $members, $id)
    {
        // Mengambil data member berdasarkan ID
        $Members = Members::findOrFail($id);

        // Mengambil semua data member untuk ditampilkan di tabel
        $dataMember = Members::all();

        return view('content.Dashboard.Members.index', compact('Members', 'dataMember'));
    }

    /**
     * Memperbarui data member.
     *
     * Method ini digunakan untuk:
     * - Mencari data member berdasarkan ID
     * - Melakukan validasi data yang diperbarui (termasuk unique field)
     * - Memperbarui data member di database
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Mencari data member berdasarkan ID
        $Members = Members::findOrFail($id);

        // Validasi data input dengan pengecualian data yang sedang diedit
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

        // Melakukan update data member
        $Members->update($data);

        // Redirect ke halaman member dengan pesan sukses
        return redirect('/Member')->with('success', 'Anda Berhasil Melakukan Update Members');
    }

    /**
     * Menghapus data member.
     *
     * Method ini:
     * - Mencari data member berdasarkan ID
     * - Menghapus data member dari database
     * - Redirect ke halaman member dengan pesan sukses
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Melakukan pencarian data member berdasarkan ID
        $Members = Members::findOrFail($id);

        // Melakukan penghapusan data member
        $Members->delete();

        // Redirect ke halaman member dengan pesan sukses
        return redirect('/Member')->with('success', 'Anda Berhasil Menghapus Data Member');
    }
}
