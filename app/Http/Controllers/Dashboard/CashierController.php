<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Members;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class CashierController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $members = Members::all();

        return view('content.Dashboard.Cashier.index', compact('products', 'members'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // 1. Validasi Input (sesuai nama di form & tipe data di DB)
        $request->validate([
            'customer_type' => 'required|string|in:umum,grosir,member',
            'member_id' => 'nullable|required_if:customer_type,member|uuid|exists:members,id',
            'payment_method' => 'required|string|in:cash,transfer,qris,debit',
            'pay' => 'required|string',
            'KdProduct' => 'required|array|min:1',
            'KdProduct.*' => 'required|string|exists:products,KdProduct',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1',
            'discount' => 'required|array|min:1',
            'discount.*' => 'required|numeric|min:0',
        ]);

        // 2. Mulai Database Transaction
        DB::beginTransaction();

        try {
            // 3. Siapkan Data
            $paidAmount = (float) preg_replace('/[^\d]/', '', $request->pay);
            $membershipId = null;

            if ($request->customer_type == 'member') {
                $membershipId = $request->member_id; // Ambil UUID member dari form
            }

            // Buat Invoice Number Unik (Primary Key Anda)
            $invoiceNumber = 'STRK-' . Carbon::now()->format('Ymd-His') . '-' . Str::random(2); // Lebih unik

            // 4. Buat Transaksi Header
            $transaction = Transactions::create([
                'invoice_number' => $invoiceNumber, // PK
                'transaction_date' => now(),
                'type_transaction' => $request->customer_type, // Map dari form
                'payment_method' => $request->payment_method,
                'status' => 'paid',
                'user_id' => Auth::id(), // Ambil UUID user yang login
                'membership_id' => $membershipId, // UUID member atau null
                'amount_paid' => $paidAmount, // Map dari form 'pay'
                'total_amount' => 0, // Akan diupdate
                'change_amount' => 0, // Akan diupdate
            ]);

            $runningTotal = 0;

            // 5. Loop semua produk
            foreach ($request->KdProduct as $index => $kdProduct) {
                if (empty($kdProduct)) continue;

                // Cari produk berdasarkan PK (KdProduct)
                $product = Product::find($kdProduct);
                if (!$product) { // Tambahan: Cek jika produk benar-benar ada
                    throw new \Exception("Produk dengan kode {$kdProduct} tidak ditemukan.");
                }

                $quantity = (int) $request->qty[$index];
                $discountInput = (float) $request->discount[$index]; // Bisa jadi desimal

                if ($product->stock < $quantity) {
                    throw new \Exception("Stok {$product->nameProduct} ({$kdProduct}) tidak cukup. Sisa: {$product->stock}");
                }

                $price = $product->price; // Ambil harga dari DB
                // Hitung diskon dalam Rupiah
                $discountAmount = $discountInput <= 100 ? ($price * $quantity * $discountInput / 100) : $discountInput;
                $subtotal = ($price * $quantity) - $discountAmount;
                if ($subtotal < 0) $subtotal = 0;

                $runningTotal += $subtotal;

                // 6. Buat Transaksi Detail
                TransactionDetail::create([
                    // 'id' (UUID) akan di-generate otomatis oleh trait HasUuids
                    'transaction_id' => $invoiceNumber, // FK ke transactions.invoice_number
                    'KdProduct' => $kdProduct, // FK ke products.KdProduct
                    'qty' => $quantity,
                    'price' => $price,
                    'discount' => $discountAmount, // Diskon dalam Rupiah
                    'subtotal' => $subtotal,
                ]);

                // 7. Kurangi Stok
                $product->decrement('stock', $quantity);
            }

            if ($paidAmount < $runningTotal) {
                throw new \Exception("Jumlah bayar (Rp " . number_format($paidAmount) . ") tidak mencukupi. Total belanja: Rp " . number_format($runningTotal));
            }

            // 8. Update Transaksi Header
            $transaction->total_amount = $runningTotal;
            $transaction->change_amount = $paidAmount - $runningTotal;
            $transaction->save();

            // 9. Commit
            DB::commit();

            // Redirect dengan pesan sukses
            return redirect('/cashier') // Asumsi nama route halaman kasir
                ->with('success', 'Transaksi berhasil disimpan! Invoice: ' . $invoiceNumber);
        } catch (\Exception $e) {
            // 10. Rollback jika gagal
            DB::rollBack();

            // Redirect kembali dengan error dan input lama
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
