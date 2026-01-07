<?php

/**
 * UsersDataController
 * -------------------
 * Controller ini digunakan untuk mengelola data pengguna (User)
 * pada Dashboard aplikasi.
 * 
 * Fitur utama:
 * - Menampilkan seluruh data user
 * - Menambah user baru
 * - Menampilkan detail user
 * - Mengedit data user
 * - Mengubah password user
 * - Menghapus data user
 */

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UsersDataController extends Controller
{

    /**
     * Menampilkan seluruh data user
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil seluruh data user dari database
        $UsersData = User::all();

        // Mengirim data user ke halaman index
        return view('content.Dashboard.UserData.index', compact('UsersData'));
    }

    /**
     * Menampilkan halaman form tambah user
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Menampilkan view form create user
        return view('content.Dashboard.UserData.create');
    }

    /**
     * Menyimpan data user baru ke database
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi data input user
        $data = $request->validate([
            'name'          => 'required|min:3',
            'email'         => 'required|email:dns|unique:users',
            'phoneNumber'   => 'required|unique:users',
            'password'      => [
                'required',
                'min:6',
                // Password harus mengandung huruf, angka, dan karakter khusus
                'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
            ],
            'role'          => 'required',
            'gender'        => 'required',
            'address'       => 'required|max:255',
        ]);

        // Mengenkripsi password sebelum disimpan
        $data['password'] = Hash::make($data['password']);

        // Menyimpan data user ke database
        User::create($data);

        // Redirect ke halaman data user dengan pesan sukses
        return redirect('/UserData')->with('success', 'Anda Berhasil Menambahkan Data User');
    }

    /**
     * Menampilkan detail data user
     *
     * @param User $user
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(User $user, $id)
    {
        // Mengambil data user berdasarkan ID
        $UserData = User::where('id', $id)->firstOrFail();

        // Menampilkan detail user
        return view('content.Dashboard.UserData.show', compact('UserData'));
    }

    /**
     * Menampilkan halaman edit data user
     *
     * @param User $user
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(User $user, $id)
    {
        // Mengambil data user berdasarkan ID
        $UserData = User::where('id', $id)->firstOrFail();

        // Menampilkan view edit user
        return view('content.Dashboard.UserData.edit', compact('UserData'));
    }

    /**
     * Memperbarui data user atau password user
     * Update dilakukan berdasarkan tipe form yang dikirim
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Mengambil data user berdasarkan ID
        $UserData = User::where('id', $id)->firstOrFail();

        // Jika form digunakan untuk update data user
        if ($request->input('form_type') == 'updateUser') {

            // Validasi data user
            $data = $request->validate([
                'name'          => 'required|min:3',
                'email'         => 'required|email|unique:users,email,' . $UserData->id,
                'phoneNumber'   => 'required|unique:users,phoneNumber,' . $UserData->id,
                'role'          => 'required',
                'gender'        => 'required',
                'address'       => 'required|max:255',
            ]);

            // Update data user
            $UserData->update($data);

            return redirect('/UserData')->with('success', 'Anda Berhasil Melakukan Update Data User');
        } 
        // Jika form digunakan untuk update password
        else {
            // Validasi password baru
            $data = $request->validate([
                'password' => [
                    'required',
                    'min:6',
                    // Password harus mengandung huruf, angka, dan karakter khusus
                    'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
                ],
                'confirmpassword' => 'required|same:password'
            ]);

            // Update password user dengan enkripsi
            $UserData->update([
                'password' => Hash::make($request->input('confirmpassword'))
            ]);

            return redirect('/UserData')->with('success', 'Anda Telah Mengubah Password Data User');
        }

        return back()->with('error', 'Periksa Kembali Data Yang Anda Berikan');
    }

    /**
     * Menghapus data user berdasarkan ID
     *
     * @param User $user
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user, $id)
    {
        // Mengambil data user berdasarkan ID
        $UserData = User::where('id', $id)->firstOrFail();

        // Jika user memiliki relasi lain, hapus relasi tersebut
        if ($UserData->user) {
            $UserData->user->delete();
        }

        // Menghapus data user
        $UserData->delete();

        // Redirect dengan pesan sukses
        return redirect('/UserData')->with('success', 'Anda Telah Berhasil Menghapus User');
    }
}
