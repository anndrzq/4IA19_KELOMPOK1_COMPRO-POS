<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\Suplier;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

/**
 * Class StockInController
 *
 * Controller ini mengelola proses **stok masuk (Stock In)**,
 * termasuk pencatatan barang masuk dari supplier,
 * pembaruan stok produk, serta penyesuaian harga jual.
 *
 * Fitur utama:
 * - Menampilkan data stok masuk
 * - Menambahkan stok produk
 * - Mengedit data stok masuk
 * - Menghapus data stok masuk
 * - Update stok dan harga produk secara otomatis
 *
 * @package App\Http\Controllers\Dashboard
 */
class StockInController extends Controller
{
    /**
     * Menampilkan halaman laporan stok masuk.
     *
     * Method ini mengambil:
     * - Data produk (kode & nama)
     * - Data supplier aktif
     * - Seluruh data stok masuk
     *
     * @return \Illuminate\View\View
     */
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

    /**
     * Menyimpan data stok masuk baru.
     *
     * Proses yang dilakukan:
     * - Validasi input stok
     * - Menyimpan data stok masuk (StockIn)
     * - Menambah stok produk
     * - Memperbarui harga jual produk
     * - Seluruh proses dijalankan dalam DB Transaction
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
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

        // DB Transaction untuk menjaga konsistensi data
        DB::transaction(function () use ($validated, $supplier) {
            foreach ($validated['KdProduct'] as $index => $kdProduct) {

                $finalPrice = $validated['final_price'][$index];
                $quantity = $validated['quantity'][$index];

                // Simpan data stok masuk
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

                // Update stok dan harga produk
                Product::where('KdProduct', $kdProduct)->update([
                    'stock' => DB::raw("stock + {$quantity}"),
                    'price' => $finalPrice,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Stock berhasil ditambahkan dan harga produk diperbarui!');
    }

    /**
     * Generate kode batch unik untuk stok masuk.
     *
     * Format:
     * KODEPRODUK-YYYYMMDDHHMMSS-RANDOM
     *
     * @param string $kdProduct
     * @return string
     */
    private function generateBatchCode(string $kdProduct): string
    {
        return strtoupper($kdProduct . '-' . date('YmdHis') . '-' . Str::random(4));
    }

    /**
     * Menampilkan data stok masuk untuk proses edit.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Mengambil data Di Stock In berdasarkan id
        $stock              = StockIn::Where('id', $id)->firstOrFail();

        // Mengambil data product seperti kode dan nama
        $productData        = Product::get(['KdProduct', 'nameProduct']);

        // Mengambil data Suplier Aktif yaitu Kode dan nama
        $suppliersData      = Suplier::where('status', 1)->get(['kdSuppliers', 'suppliersName']);

        // Mengambil Semua Data di stock in
        $StockData          = StockIn::all();

        return view('content.Dashboard.Report.StockIn.index', compact('stock', 'productData', 'suppliersData', 'StockData'));
    }

    /**
     * Memperbarui data stok masuk.
     *
     * Alur update:
     * 1. Mengurangi stok lama produk
     * 2. Update data StockIn
     * 3. Menambahkan stok baru ke produk
     * 4. Update harga jual produk
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'KdProduct.*' => 'required|exists:products,KdProduct',
            'KdSuppliers' => 'required|exists:supliers,kdSuppliers',
            'quantity.*' => 'required|numeric|min:1',
            'expired_date.*' => 'nullable|date',
            'purchase_price.*' => 'required|numeric|min:0',
            'markup_percentage.*' => 'nullable|numeric|min:0',
            'final_price.*' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $id) {
            $stock = StockIn::findOrFail($id);

            // Ambil data produk (edit hanya satu baris)
            $kdProduct = $validated['KdProduct'][0];
            $quantity  = $validated['quantity'][0];
            $expired   = $validated['expired_date'][0] ?? null;
            $purchase  = $validated['purchase_price'][0];
            $markup    = $validated['markup_percentage'][0] ?? 0;
            $final     = $validated['final_price'][0];

            // Kurangi stok lama
            Product::where('KdProduct', $stock->KdProduct)->update([
                'stock' => DB::raw("stock - {$stock->quantity}")
            ]);

            // Update data StockIn
            $stock->update([
                'KdProduct' => $kdProduct,
                'KdSuppliers' => $validated['KdSuppliers'],
                'quantity' => $quantity,
                'expired_date' => $expired,
                'purchase_price' => $purchase,
                'markup_percentage' => $markup,
                'final_price' => $final,
            ]);

            // Tambahkan stok baru & update harga
            Product::where('KdProduct', $kdProduct)->update([
                'stock' => DB::raw("stock + {$quantity}"),
                'price' => $final,
            ]);
        });

        return redirect()->route('StockIn.index')->with('success', 'Stock berhasil diupdate!');
    }

    /**
     * Menghapus data stok masuk.
     *
     * - Mengurangi stok produk sesuai quantity
     * - Menghapus data StockIn
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $stock = StockIn::findOrFail($id);
        $product = Product::find($stock->KdProduct);

        if ($product) {
            // Kurangi stok produk sesuai quantity StockIn yang dihapus
            $product->stock = max(0, $product->stock - $stock->quantity);
            $product->save();
        } else {
            return back()->with('error', 'Produk Tidak Ada');
        }

        // Hapus StockIn
        $stock->delete();

        return back()->with('success', 'Data Stok Masuk berhasil dihapus');
    }
}
