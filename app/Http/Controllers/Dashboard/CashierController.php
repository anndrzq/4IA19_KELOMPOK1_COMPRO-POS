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
        // 1. Validasi Input (Disesuaikan dengan field baru)
        $request->validate([
            'customer_type' => 'required|string|in:umum,grosir,member',
            'member_id' => 'nullable|required_if:customer_type,member|uuid|exists:members,id',
            'payment_method' => 'required|string|in:cash,transfer,qris,debit,credit', // Ditambah credit
            'payment_detail' => 'nullable|required_if:payment_method,debit|required_if:payment_method,credit|string|in:debit_bca,debit_lain,credit_bca,credit_lain', // Validasi baru
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
            $membershipId = ($request->customer_type == 'member') ? $request->member_id : null;

            // Tentukan payment_provider dan tax_percent berdasarkan input
            $paymentProvider = null;
            $taxPercent = 0;

            if ($request->payment_method == 'debit' || $request->payment_method == 'credit') {
                switch ($request->payment_detail) {
                    case 'debit_bca':
                        $paymentProvider = 'bca';
                        $taxPercent = 0; // 0%
                        break;
                    case 'debit_lain':
                        $paymentProvider = 'lain';
                        $taxPercent = 2; // 2%
                        break;
                    case 'credit_bca':
                        $paymentProvider = 'bca';
                        $taxPercent = 1; // 1%
                        break;
                    case 'credit_lain':
                        $paymentProvider = 'lain';
                        $taxPercent = 2.5; // 2.5%
                        break;
                }
            }

            $invoiceNumber = 'STRK-' . Carbon::now()->format('Ymd-His') . '-' . Str::random(2);

            // 4. Buat Transaksi Header (Disesuaikan dengan skema)
            $transaction = Transactions::create([
                'invoice_number' => $invoiceNumber,
                'transaction_date' => now(),
                'type_transaction' => $request->customer_type,
                'payment_method' => $request->payment_method,
                'payment_provider' => $paymentProvider, // Kolom baru
                'status' => 'paid',
                'user_id' => Auth::id(),
                'membership_id' => $membershipId,
                'amount_paid' => $paidAmount,
                'total_amount' => 0, // Placeholder, akan diupdate
                'tax_amount' => 0,   // Placeholder, akan diupdate
                'change_amount' => 0, // Placeholder, akan diupdate
            ]);

            $runningSubtotal = 0; // Ini adalah total *sebelum* pajak

            // 5. Loop semua produk
            foreach ($request->KdProduct as $index => $kdProduct) {
                if (empty($kdProduct)) continue;

                $product = Product::find($kdProduct);
                if (!$product) {
                    throw new \Exception("Produk dengan kode {$kdProduct} tidak ditemukan.");
                }

                $quantity = (int) $request->qty[$index];
                $discountInput = (float) $request->discount[$index];

                if ($product->stock < $quantity) {
                    throw new \Exception("Stok {$product->nameProduct} ({$kdProduct}) tidak cukup. Sisa: {$product->stock}");
                }

                $price = $product->price;
                $discountAmount = $discountInput <= 100 ? ($price * $quantity * $discountInput / 100) : $discountInput;
                $subtotal = ($price * $quantity) - $discountAmount;
                if ($subtotal < 0) $subtotal = 0;

                $runningSubtotal += $subtotal;

                // 6. Buat Transaksi Detail
                TransactionDetail::create([
                    'transaction_id' => $invoiceNumber,
                    'KdProduct' => $kdProduct,
                    'qty' => $quantity,
                    'price' => $price,
                    'discount' => $discountAmount,
                    'subtotal' => $subtotal,
                ]);

                // 7. Kurangi Stok
                $product->decrement('stock', $quantity);
            }

            // 8. Hitung Total, Pajak, dan Kembalian (setelah loop)
            $taxAmount = ($runningSubtotal * $taxPercent) / 100;
            $grandTotal = $runningSubtotal + $taxAmount; // Ini adalah total_amount akhir

            // Validasi jumlah bayar terhadap Grand Total
            // Beri toleransi 0.01 untuk pembulatan angka desimal/float
            if (round($paidAmount, 2) < round($grandTotal, 2) && abs($paidAmount - $grandTotal) > 0.01) {
                throw new \Exception("Jumlah bayar (Rp " . number_format($paidAmount) . ") tidak mencukupi. Total belanja: Rp " . number_format($grandTotal));
            }

            $changeAmount = $paidAmount - $grandTotal;
            if ($changeAmount < 0) $changeAmount = 0; // Pastikan kembalian tidak negatif


            // 9. Update Transaksi Header dengan nilai akhir
            $transaction->total_amount = $grandTotal;   // Total akhir (setelah pajak)
            $transaction->tax_amount = $taxAmount;     // Jumlah pajak
            $transaction->change_amount = $changeAmount; // Kembalian
            $transaction->save();

            // 10. Commit
            DB::commit();

            return redirect('/cashier') // Asumsi nama route halaman kasir
                ->with('success', 'Transaksi berhasil disimpan! Invoice: ' . $invoiceNumber);
        } catch (\Exception $e) {
            // 11. Rollback jika gagal
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
