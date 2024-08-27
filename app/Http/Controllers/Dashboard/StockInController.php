<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Product;
use App\Models\Suplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StockIn;

class StockInController extends Controller
{
    public function index()
    {
        $productData            = Product::get(['KdProduct', 'nameProduct']);
        $suppliersData          = Suplier::where('status', 1)->get(['kdSuppliers', 'suppliersName']);
        $StockData              = StockIn::all();
        return view('content.Dashboard.Report.StockIn.index', compact('productData', 'StockData', 'suppliersData'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'       => 'required',
            'KdProduct'     => 'required',
            'kdSuppliers'   => 'required',
            'date'          => 'date',
            'qty'           => 'required',
            'description'   => 'required'
        ]);

        // Jika tidak ada nilai 'date' dalam request, set tanggal hari ini
        $data['date'] = $data['date'] ?? now()->format('Y-m-d');

        $product = Product::find($data['KdProduct']);
        if ($product) {
            // Tambah stok alat
            $product->stok += $data['qty'];
            $product->save();
        } else {
            return back()->with('error', 'Produk Tidak Tersedia');
        }

        StockIn::create($data);
        return back()->with('success', 'Anda Telah Berhasil Menambah Stock Masuk Produk');
    }

    public function edit($uuid)
    {
        $stock              = StockIn::Where('uuid', $uuid)->firstOrFail();
        $productData        = Product::get(['KdProduct', 'nameProduct']);
        $suppliersData      = Suplier::where('status', 1)->get(['kdSuppliers', 'suppliersName']);
        $StockData          = StockIn::all();
        return view('content.Dashboard.Report.StockIn.index', compact('stock', 'productData', 'suppliersData', 'StockData'));
    }

    public function update(Request $request, $uuid)
    {
        $data = $request->validate([
            'user_id'       => 'required',
            'KdProduct'     => 'required',
            'kdSuppliers'   => 'required',
            'date'          => 'date',
            'qty'           => 'required',
            'description'   => 'required'
        ]);

        $StockData = StockIn::findOrFail($uuid);
        $product = Product::find($data['KdProduct']);
        if ($product) {
            // Tambah stok Produk dengan qty yang Lama
            $product->stok += $StockData->qty;

            // Kurang qty yang baru ke stok
            $product->stok -= $data['qty'];

            // Pastikan stok tidak menjadi negatif
            if ($product->stok < 0) {
                return back()->with('error', 'Tidak Boleh Kurang Dari 0');
            }
            // Simpan perubahan stok ke database
            $product->save();
        } else {
            return back()->with('error', 'Produk Tidak Ada');
        }
        // dd($data);
        $StockData->update($data);
        return redirect('/StockIn')->with('success', 'Anda Telah Berhasil Mengupdate Stock Masuk Produk');
    }

    public function destroy($uuid)
    {
        $StockData = StockIn::findOrFail($uuid);
        $product = Product::find($StockData->KdProduct);
        if ($product) {
            // Tambahkan kembali qty dari reservasi ke stok alat
            $product->stok -= $StockData->qty;
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
