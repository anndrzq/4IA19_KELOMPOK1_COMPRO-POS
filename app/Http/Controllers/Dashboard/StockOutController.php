<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Product;
use App\Models\StockOut;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockOutController extends Controller
{
    public function index()
    {
        // Mengambil Data Kode Product dan nama Product
        $productData            = Product::get(['KdProduct', 'nameProduct', 'stock']);
        // Mengambil Semua Data Di StockOut
        $StockData              = StockOut::all();
        return view('content.Dashboard.Report.StockOut.index', compact('productData', 'StockData'));
    }

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
            // Jika Stok Produk Sudah 0 Maka Tidak bisa mengurangi lagi
            if ($product->stock <= 0) {
                return back()->with('error', 'Stock Tidak Tersedia');
            }
            // Kurangi Stok Produk
            $product->stock -= $data['qty'];
            $product->save();
        } else {
            return back()->with('error', 'Produk Tidak Tersedia');
        }
        // Membuat Data Di StockOut
        StockOut::create($data);
        return back()->with('success', 'Anda Telah Berhasil Menambah Stock Masuk Produk');
    }

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
        // Caru Kode Product
        $product = Product::find($data['KdProduct']);
        if ($product) {

            // Tambah stock Produk dengan qty yang Lama
            $product->stock += $StockData->qty;
            // Jika Stock Kurang dari yang di inputkan maka error
            if ($product->stock < $data['qty']) {
                return back()->with('error', 'Stock Kurang Dari Yang Diinginkan');
            }

            // Kurangi qty yang baru ke stock
            $product->stock -= $data['qty'];

            // Simpan perubahan stock ke database
            $product->save();
        } else {
            return back()->with('error', 'Produk Tidak Ada');
        }
        $StockData->update($data);
        return redirect('/StockOut')->with('success', 'Anda Telah Berhasil Mengupdate Stock Keluar Produk');
    }

    public function destroy($id)
    {
        $StockData = StockOut::findOrFail($id);
        $product = Product::find($StockData->KdProduct);
        if ($product) {
            // Tambahkan kembali qty dari reservasi ke stok alat
            $product->stock += $StockData->qty;
            // Simpan perubahan stok ke database
            $product->save();
        } else {
            return back()->with('error', 'Alat Tidak Ada');
        }
        // Hapus reservasi
        $StockData->delete();
        return back()->with('success', 'Data Stok Masuk Berhasil Di Hapus');
    }
}
