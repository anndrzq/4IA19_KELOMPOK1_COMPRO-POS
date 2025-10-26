<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\Suplier;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StockInController extends Controller
{
    public function index()
    {
        // Mengambil Data Kode Product dan nama Product
        $productData            = Product::get(['KdProduct', 'nameProduct']);
        // Mengambil  data Suplier Aktif dan Kode Suplier dan nama
        $suppliersData          = Suplier::where('status', 1)->get(['kdSuppliers', 'suppliersName']);
        // Mengambil Semua Data Di Stock In
        $StockData              = StockIn::all();
        return view('content.Dashboard.Report.StockIn.index', compact('productData', 'StockData', 'suppliersData'));
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $validated = $request->validate([
            'KdProduct.*' => 'required|exists:products,KdProduct',
            'KdSuppliers' => 'required|exists:supliers,kdSuppliers',
            'quantity.*' => 'required|numeric|min:1',
            'expired_date.*' => 'nullable|date',
            'purchase_price.*' => 'required|numeric|min:0',
            'markup_percentage.*' => 'nullable|numeric|min:0',
            'final_price.*' => 'required|numeric|min:0',
        ]);
        $supplier = $validated['KdSuppliers'];

        DB::transaction(function () use ($validated, $supplier) {
            foreach ($validated['KdProduct'] as $index => $kdProduct) {
                $finalPrice = $validated['final_price'][$index];
                $quantity = $validated['quantity'][$index];

                StockIn::create([
                    'user_id' => auth()->id(),
                    'KdProduct' => $kdProduct,
                    'KdSuppliers' => $supplier,
                    'batch_code' => $this->generateBatchCode($kdProduct),
                    'quantity' => $quantity,
                    'expired_date' => $validated['expired_date'][$index] ?? null,
                    'purchase_price' => $validated['purchase_price'][$index],
                    'markup_percentage' => $validated['markup_percentage'][$index] ?? 0,
                    'final_price' => $finalPrice,
                ]);

                Product::where('KdProduct', $kdProduct)->update([
                    'stock' => DB::raw("stock + {$quantity}"),
                    'price' => $finalPrice,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Stock berhasil ditambahkan dan harga produk diperbarui!');
    }

    private function generateBatchCode(string $kdProduct): string
    {
        return strtoupper($kdProduct . '-' . date('YmdHis') . '-' . Str::random(4));
    }

    public function edit($uuid)
    {
        // Mengambil data Di Stock In berdasarkan UUID
        $stock              = StockIn::Where('uuid', $uuid)->firstOrFail();
        // Mengambil data product seperti kode dan nama
        $productData        = Product::get(['KdProduct', 'nameProduct']);
        // Mengambil data Suplier Aktif yaitu Kode dan nama
        $suppliersData      = Suplier::where('status', 1)->get(['kdSuppliers', 'suppliersName']);
        // Mengambil Semua Data di stock in
        $StockData          = StockIn::all();
        return view('content.Dashboard.Report.StockIn.index', compact('stock', 'productData', 'suppliersData', 'StockData'));
    }

    public function update(Request $request, $uuid)
    {
        // Mengambil Semua Data Di Form Input
        $data = $request->validate([
            'user_id'       => 'required',
            'KdProduct'     => 'required',
            'kdSuppliers'   => 'required',
            'date'          => 'date',
            'qty'           => 'required',
            'description'   => 'required'
        ]);
        // Cari Berdasarkan UUID
        $StockData = StockIn::findOrFail($uuid);
        // Caru Kode Product
        $product = Product::find($data['KdProduct']);
        if ($product) {
            // Kurang stok Produk dengan qty yang Lama
            $product->stok -= $StockData->qty;

            // Tambah qty yang baru ke stok
            $product->stok += $data['qty'];

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
