<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Discount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class DiscountController
 *
 * Controller ini digunakan untuk mengelola data diskon
 * pada menu Master Data Dashboard, yang meliputi:
 * - Menampilkan daftar diskon
 * - Menambahkan diskon baru
 * - Mengedit data diskon
 * - Memperbarui diskon
 * - Menghapus diskon
 *
 * @package App\Http\Controllers\Dashboard
 */
class DiscountController extends Controller
{
    /**
     * Menampilkan daftar diskon.
     *
     * Method ini mengambil seluruh data diskon dari database
     * dan mengirimkannya ke view untuk ditampilkan
     * pada halaman master diskon.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $discounts = Discount::all();
        return view('content.Dashboard.Master.discount.index', compact('discounts'));
    }

    /**
     * Menyimpan data diskon baru.
     *
     * Method ini berfungsi untuk:
     * - Melakukan validasi input diskon
     * - Menyimpan data diskon ke database
     * - Melakukan redirect dengan pesan sukses
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi data diskon
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Menyimpan diskon ke database
        Discount::create($data);

        // Redirect ke halaman diskon dengan pesan sukses
        return redirect('/Discount')->with('success', 'Diskon Berhasil Di Buat');
    }

    /**
     * Menampilkan halaman edit diskon.
     *
     * Method ini:
     * - Mengambil data diskon berdasarkan ID
     * - Mengambil seluruh data diskon untuk ditampilkan
     * - Mengirimkan data ke view
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $discount = Discount::where('id', $id)->firstOrFail();
        $discounts = Discount::all();

        return view('content.Dashboard.Master.discount.index', compact('discount', 'discounts'));
    }

    /**
     * Memperbarui data diskon.
     *
     * Method ini berfungsi untuk:
     * - Melakukan validasi input data diskon
     * - Mencari data diskon berdasarkan ID
     * - Memperbarui data diskon di database
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validasi data diskon yang akan diperbarui
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Mengambil data diskon berdasarkan ID
        $discount = Discount::where('id', $id)->firstOrFail();

        // Memperbarui data diskon
        $discount->update($data);

        // Redirect ke halaman diskon dengan pesan sukses
        return redirect('/Discount')->with('success', 'Diskon Berhasil Di Perbarui');
    }

    /**
     * Menghapus data diskon.
     *
     * Method ini:
     * - Mencari data diskon berdasarkan ID
     * - Menghapus data diskon dari database
     * - Melakukan redirect dengan pesan sukses
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $discount = Discount::where('id', $id)->firstOrFail();

        // Menghapus data diskon
        $discount->delete();

        // Redirect ke halaman diskon dengan pesan sukses
        return redirect('/Discount')->with('success', 'Diskon Berhasil Di Hapus');
    }
}
