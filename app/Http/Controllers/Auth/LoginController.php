<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    public function index()
    {
        return view('content.Auth.login');
    }

    public function authenticate(Request $request)
    {
        $dataLogin = $request->validate([
            'email'     => 'required|email:dns',
            'password'  => 'required|min:6'
        ]);

        $remember = $request->input('remember_me');

        if (Auth::attempt($dataLogin, $remember)) {
            $request->session()->regenerate();
            $user = User::findOrFail(Auth::user()->id);
            $user->last_login = Carbon::now();
            $user->save();
            return redirect()->intended('/UserData')->with('succes', 'Anda Berhasil Login');
        }

        if (Auth::viaRemember()) {
            return redirect()->intended('/UserData')->with('succes', 'Anda Berhasil Login');
        }

        return back()->with('error', 'The provided credentials do not match our records.');
    }
}
