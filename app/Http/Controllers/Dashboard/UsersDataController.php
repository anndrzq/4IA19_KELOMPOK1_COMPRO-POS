<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UsersDataController extends Controller
{

    public function index()
    {
        $UsersData = User::all();
        return view('content.Dashboard.UserData.index', compact('UsersData'));
    }


    public function create()
    {
        return view('content.Dashboard.UserData.create');
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|min:3',
            'email'         => 'required|email:dns|unique:users',
            'phoneNumber'   => 'required|unique:users',
            'password'      => [
                'required',
                'min:6',
                'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
            ],
            'role'          => 'required',
            'jk'            => 'required',
            'address'       => 'required|max:255',
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);
        return redirect('/UserData')->with('success', 'Anda Berhasil Menambahkan Data User');
    }


    public function show(User $user, $uuid)
    {
        $UserData = User::where('uuid', $uuid)->firstOrFail();
        return view('content.Dashboard.UserData.show', compact('UserData'));
    }

    public function edit(User $user, $uuid)
    {
        $UserData = User::where('uuid', $uuid)->firstOrFail();
        return view('content.Dashboard.UserData.edit', compact('UserData'));
    }

    public function update(Request $request, $uuid)
    {
        $UserData = User::where('uuid', $uuid)->firstOrFail();
        if ($request->input('form_type') == 'updateUser') {
            $data = $request->validate([
                'name'          => 'required|min:3',
                'email'         => 'required|email|unique:users,email,' . $UserData->id,
                'phoneNumber'   => 'required|unique:users,phoneNumber,' . $UserData->id,
                'role'          => 'required',
                'jk'            => 'required',
                'address'       => 'required|max:255',
            ]);

            $UserData->update($data);
            return redirect('/UserData')->with('success', 'Anda Berhasil Melakukan Update Data User');
        } else {
            $data = $request->validate([
                'password'              => [
                    'required',
                    'min:6',
                    'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
                ],
                'confirmpassword'       => 'required|same:password'
            ]);

            $UserData->update(['password' => Hash::make($request->input('confirmpassword'))]);
            return redirect('/UserData')->with('success', 'Anda Telah Mengubah Password Data User');
        }
        return back()->with('error', 'Periksa Kembali Data Yang Anda Berikan');
    }


    public function destroy(User $user, $uuid)
    {
        $UserData = User::where('uuid', $uuid)->firstOrFail();
        if ($UserData->user) {
            $UserData->user->delete();
        }
        $UserData->delete();
        return redirect('/UserData')->with('success', 'Anda Telah Berhasil Menghapus User');
    }
}
