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
        $request->validate([
            'customer_type' => 'required|string|in:umum,grosir,member',
            'member_id' => 'nullable|required_if:customer_type,member|uuid|exists:members,id',
            'payment_method' => 'required|string|in:cash,transfer,qris,debit,credit',
            'payment_detail' => 'nullable|required_if:payment_method,debit|required_if:payment_method,credit|string|in:debit_bca,debit_lain,credit_bca,credit_lain',
            'pay' => 'required|string',
            'KdProduct' => 'required|array|min:1',
            'KdProduct.*' => 'required|string|exists:products,KdProduct',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1',
            'discount' => 'required|array|min:1',
            'discount.*' => 'required|numeric|min:0',
            'print_receipt' => 'required|in:true,false',
        ]);

        DB::beginTransaction();

        try {
            $paidAmount = (float) preg_replace('/[^\d]/', '', $request->pay);
            $membershipId = ($request->customer_type == 'member') ? $request->member_id : null;

            $paymentProvider = null;
            $taxPercent = 0;

            if ($request->payment_method == 'debit' || $request->payment_method == 'credit') {
                switch ($request->payment_detail) {
                    case 'debit_bca':
                        $paymentProvider = 'bca';
                        $taxPercent = 0.25;
                        break;
                    case 'debit_lain':
                        $paymentProvider = 'lain';
                        $taxPercent = 1;
                        break;
                    case 'credit_bca':
                        $paymentProvider = 'bca';
                        $taxPercent = 1;
                        break;
                    case 'credit_lain':
                        $paymentProvider = 'lain';
                        $taxPercent = 2.5;
                        break;
                }
            }

            $invoiceNumber = 'STRK-' . Carbon::now()->format('Ymd-His') . '-' . Str::random(2);

            $transaction = Transactions::create([
                'invoice_number' => $invoiceNumber,
                'transaction_date' => now(),
                'type_transaction' => $request->customer_type,
                'payment_method' => $request->payment_method,
                'payment_provider' => $paymentProvider,
                'status' => 'paid',
                'user_id' => Auth::id(),
                'membership_id' => $membershipId,
                'amount_paid' => $paidAmount,
                'total_amount' => 0,
                'tax_amount' => 0,
                'change_amount' => 0,
            ]);

            $runningSubtotal = 0;

            foreach ($request->KdProduct as $index => $kdProduct) {
                if (empty($kdProduct)) continue;

                $product = Product::where('KdProduct', $kdProduct)->first();
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

                TransactionDetail::create([
                    'transaction_id' => $invoiceNumber,
                    'KdProduct' => $kdProduct,
                    'qty' => $quantity,
                    'price' => $price,
                    'discount' => $discountAmount,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $quantity);
            }

            $taxAmount = ($runningSubtotal * $taxPercent) / 100;

            $grandTotal = $runningSubtotal - $taxAmount;

            if (round($paidAmount, 2) < round($runningSubtotal, 2) && abs($paidAmount - $runningSubtotal) > 0.01) {
                throw new \Exception("Jumlah bayar (Rp " . number_format($paidAmount) . ") tidak mencukupi. Total belanja: Rp " . number_format($runningSubtotal));
            }

            $changeAmount = $paidAmount - $runningSubtotal;
            if ($changeAmount < 0) $changeAmount = 0;


            $transaction->total_amount = $grandTotal;
            $transaction->tax_amount = $taxAmount;
            $transaction->change_amount = $changeAmount;
            $transaction->save();

            DB::commit();

            $redirect = redirect('/cashier')
                ->with('success', 'Transaksi berhasil disimpan! Invoice: ' . $invoiceNumber);

            if ($request->input('print_receipt') === 'true') {
                $redirect->with('print_invoice', $invoiceNumber);
            }

            return $redirect;
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function print($invoiceNumber)
    {
        $transaction = Transactions::where('invoice_number', $invoiceNumber)
            ->with('details.product')
            ->firstOrFail();

        return view('content.Dashboard.Cashier.receipt', compact('transaction'));
    }
}
