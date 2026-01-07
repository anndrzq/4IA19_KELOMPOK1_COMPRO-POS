<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

/**
 * Class LoginController
 *
 * Controller ini bertanggung jawab untuk menangani
 * proses autentikasi pengguna, termasuk:
 * - Menampilkan halaman login
 * - Memproses login (dengan fitur remember me)
 * - Menyimpan waktu login terakhir pengguna
 * - Menangani logout pengguna
 *
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    /**
     * Menampilkan halaman login.
     *
     * Method ini akan dipanggil ketika pengguna
     * mengakses route login dan mengembalikan
     * tampilan form login.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('content.Auth.login');
    }

    /**
     * Memproses autentikasi pengguna.
     *
     * Method ini melakukan:
     * 1. Validasi input email dan password
     * 2. Mengecek opsi "remember me"
     * 3. Autentikasi menggunakan Auth::attempt()
     * 4. Regenerasi session untuk keamanan
     * 5. Menyimpan waktu login terakhir ke database
     * 6. Redirect ke dashboard jika berhasil
     * 7. Menampilkan pesan error jika gagal
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function authenticate(Request $request)
    {
        // Validasi data login dari form
        $dataLogin = $request->validate([
            'email'     => 'required|email:dns',
            'password'  => 'required|min:6'
        ]);

        // Mengambil nilai remember me (jika dicentang)
        $remember = $request->input('remember_me');

        // Proses autentikasi user
        if (Auth::attempt($dataLogin, $remember)) {

            // Regenerasi session untuk mencegah session fixation
            $request->session()->regenerate();

            // Mengambil data user yang sedang login
            $user = User::findOrFail(Auth::user()->id);

            // Menyimpan waktu login terakhir
            $user->last_login = Carbon::now();
            $user->save();

            // Redirect ke dashboard dengan pesan sukses
            return redirect()->intended('/Dashboard')
                ->with('success', 'Hallo Selamat Datang ' . Auth::user()->name);
        }

        // Mengecek apakah login berhasil melalui remember me
        if (Auth::viaRemember()) {
            return redirect()->intended('/Dashboard')
                ->with('success', 'Hallo Selamat Datang ' . Auth::user()->name);
        }

        // Jika autentikasi gagal, kembali ke halaman sebelumnya
        return back()->with('error', 'Periksa Kembali Data Yang Anda Masukan');
    }

    /**
     * Melakukan logout pengguna.
     *
     * Method ini akan:
     * 1. Logout user
     * 2. Menghapus session
     * 3. Regenerasi CSRF token
     * 4. Redirect ke halaman utama
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request)
    {
        // Logout user dari sistem
        Auth::logout();

        // Menginvalidasi session
        $request->session()->invalidate();

        // Regenerasi token CSRF
        $request->session()->regenerateToken();

        // Redirect ke halaman awal dengan pesan sukses
        return redirect('/')->with('success', 'Anda Berhasil Logout');
    }
}