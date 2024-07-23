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
            'jk'            => 'required'
        ]);

        $data['passowrd'] = Hash::make($data['password']);

        User::create($data);
        return redirect('/UserData')->with('success', 'Anda Berhasil Menambahkan Data User');
    }


    public function show(User $user)
    {
        //
    }

    public function edit(User $user)
    {
        //
    }

    public function update(Request $request, User $user)
    {
        //
    }


    public function destroy(User $user)
    {
        //
    }
}
