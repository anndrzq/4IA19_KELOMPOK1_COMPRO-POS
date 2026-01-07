<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Product;
use App\Models\StockOut;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class StockOutController
 *
 * Controller ini bertanggung jawab untuk mengelola proses **stok keluar (Stock Out)**,
 * yaitu pencatatan pengurangan stok produk akibat kerusakan, pemakaian internal,
 * penyesuaian stok, atau keperluan lainnya di luar transaksi penjualan.
 *
 * Fitur utama:
 * - Menampilkan data stok keluar
 * - Menambahkan stok keluar
 * - Mengedit data stok keluar
 * - Menghapus data stok keluar
 * - Otomatis menyesuaikan stok produk
 *
 * @package App\Http\Controllers\Dashboard
 */
class StockOutController extends Controller
{
    /**
     * Menampilkan halaman stok keluar.
     *
     * Method ini mengambil:
     * - Data produk (kode, nama, dan stok)
     * - Seluruh data stok keluar
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil Data Kode Product dan nama Product
        $productData            = Product::get(['KdProduct', 'nameProduct', 'stock']);

        // Mengambil Semua Data Di StockOut
        $StockData              = StockOut::all();

        return view('content.Dashboard.Report.StockOut.index', compact('productData', 'StockData'));
    }

    /**
     * Menyimpan data stok keluar.
     *
     * Proses yang dilakukan:
     * - Validasi input
     * - Mengurangi stok produk
     * - Menyimpan data StockOut
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Mengambil Semua Data Request
        $data = $request->validate([
            'user_id'       => 'required',
            'KdProduct'     => 'required',
            'date'          => 'date',
            'qty'           => 'required|min:1|numeric',
            'description'   => 'required'
        ]);

        // Jika tidak ada nilai 'date' dalam request, set tanggal hari ini
        $data['date'] = $data['date'] ?? now()->format('Y-m-d');

        // Mencari data yang ada di dalam form Kode Product
        $product = Product::find($data['KdProduct']);

        if ($product) {

            // Jika stok produk sudah habis, tidak dapat mengurangi stok
            if ($product->stock <= 0) {
                return back()->with('error', 'Stock Tidak Tersedia');
            }

            // Kurangi stok produk berdasarkan qty
            $product->stock -= $data['qty'];
            $product->save();

        } else {
            return back()->with('error', 'Produk Tidak Tersedia');
        }

        // Membuat Data Di StockOut
        StockOut::create($data);

        return back()->with('success', 'Anda Telah Berhasil Menambah Stock Masuk Produk');
    }

    /**
     * Menampilkan data stok keluar untuk proses edit.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Mengambil data Di StockOut berdasarkan UUID
        $stock              = StockOut::Where('id', $id)->firstOrFail();

        // Mengambil data product seperti kode dan nama
        $productData        = Product::get(['KdProduct', 'nameProduct']);

        // Mengambil Semua Data di StockOut
        $StockData          = StockOut::all();

        return view('content.Dashboard.Report.StockOut.index', compact('stock', 'productData', 'StockData'));
    }

    /**
     * Memperbarui data stok keluar.
     *
     * Alur proses:
     * 1. Kembalikan stok lama ke produk
     * 2. Validasi stok mencukupi
     * 3. Kurangi stok dengan qty baru
     * 4. Update data StockOut
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Mengambil Semua Data Di Form Input
        $data = $request->validate([
            'user_id'       => 'required',
            'KdProduct'     => 'required',
            'date'          => 'date',
            'qty'           => 'required',
            'description'   => 'required'
        ]);

        // Cari Berdasarkan UUID
        $StockData = StockOut::findOrFail($id);

        // Cari Kode Product
        $product = Product::find($data['KdProduct']);

        if ($product) {

            // Kembalikan stok lama ke produk
            $product->stock += $StockData->qty;

            // Jika stok tidak mencukupi untuk qty baru
            if ($product->stock < $data['qty']) {
                return back()->with('error', 'Stock Kurang Dari Yang Diinginkan');
            }

            // Kurangi stok dengan qty baru
            $product->stock -= $data['qty'];

            // Simpan perubahan stok
            $product->save();

        } else {
            return back()->with('error', 'Produk Tidak Ada');
        }

        // Update data stok keluar
        $StockData->update($data);

        return redirect('/StockOut')->with('success', 'Anda Telah Berhasil Mengupdate Stock Keluar Produk');
    }

    /**
     * Menghapus data stok keluar.
     *
     * Saat dihapus:
     * - Stok produk akan dikembalikan
     * - Data StockOut dihapus
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $StockData = StockOut::findOrFail($id);
        $product = Product::find($StockData->KdProduct);

        if ($product) {
            // Tambahkan kembali qty ke stok produk
            $product->stock += $StockData->qty;
            $product->save();
        } else {
            return back()->with('error', 'Alat Tidak Ada');
        }

        // Hapus data stok keluar
        $StockData->delete();

        return back()->with('success', 'Data Stok Masuk Berhasil Di Hapus');
    }
}
