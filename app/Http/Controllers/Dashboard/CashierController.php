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

/**
 * Class CashierController
 *
 * Controller ini menangani seluruh proses kasir (Point of Sale),
 * meliputi:
 * - Menampilkan halaman kasir
 * - Memproses transaksi penjualan
 * - Mengelola pembayaran dan metode bayar
 * - Menghitung diskon, pajak, kembalian
 * - Mengurangi stok produk
 * - Menyimpan detail transaksi
 * - Mencetak struk transaksi
 *
 * @package App\Http\Controllers\dashboard
 */
class CashierController extends Controller
{
    /**
     * Menampilkan halaman kasir.
     *
     * Method ini mengambil seluruh data produk dan member
     * untuk ditampilkan pada halaman kasir sebagai
     * data transaksi.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $products = Product::all();
        $members = Members::all();

        return view('content.Dashboard.Cashier.index', compact('products', 'members'));
    }

    /**
     * Menyimpan transaksi penjualan.
     *
     * Method ini berfungsi untuk:
     * - Validasi data transaksi
     * - Menentukan tipe customer (umum, grosir, member)
     * - Menentukan metode dan provider pembayaran
     * - Menghitung subtotal, diskon, pajak, dan total
     * - Menyimpan transaksi utama dan detail transaksi
     * - Mengurangi stok produk
     * - Menangani transaksi database (commit & rollback)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi seluruh input transaksi
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

        // Memulai database transaction
        DB::beginTransaction();

        try {
            // Mengambil nilai pembayaran dan membersihkan format rupiah
            $paidAmount = (float) preg_replace('/[^\d]/', '', $request->pay);

            // Menentukan membership (jika customer member)
            $membershipId = ($request->customer_type == 'member') ? $request->member_id : null;

            $paymentProvider = null;
            $taxPercent = 0;

            // Menentukan provider dan pajak tambahan sesuai metode pembayaran
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

            // Membuat nomor invoice unik
            $invoiceNumber = 'STRK-' . Carbon::now()->format('Ymd-His') . '-' . Str::random(2);

            // Menyimpan data transaksi utama
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

            // Proses setiap produk dalam transaksi
            foreach ($request->KdProduct as $index => $kdProduct) {
                if (empty($kdProduct)) continue;

                $product = Product::where('KdProduct', $kdProduct)->first();
                if (!$product) {
                    throw new \Exception("Produk dengan kode {$kdProduct} tidak ditemukan.");
                }

                $quantity = (int) $request->qty[$index];
                $discountInput = (float) $request->discount[$index];

                // Validasi stok produk
                if ($product->stock < $quantity) {
                    throw new \Exception("Stok {$product->nameProduct} ({$kdProduct}) tidak cukup. Sisa: {$product->stock}");
                }

                // Perhitungan harga, diskon, dan subtotal
                $price = $product->price;
                $discountAmount = $discountInput <= 100
                    ? ($price * $quantity * $discountInput / 100)
                    : $discountInput;

                $subtotal = ($price * $quantity) - $discountAmount;
                if ($subtotal < 0) $subtotal = 0;

                $runningSubtotal += $subtotal;

                // Menyimpan detail transaksi
                TransactionDetail::create([
                    'transaction_id' => $invoiceNumber,
                    'KdProduct' => $kdProduct,
                    'qty' => $quantity,
                    'price' => $price,
                    'discount' => $discountAmount,
                    'subtotal' => $subtotal,
                ]);

                // Mengurangi stok produk
                $product->decrement('stock', $quantity);
            }

            // Perhitungan pajak
            $taxAmount = ($runningSubtotal * $taxPercent) / 100;

            // Total akhir transaksi
            $grandTotal = $runningSubtotal - $taxAmount;

            // Validasi kecukupan pembayaran
            if (round($paidAmount, 2) < round($runningSubtotal, 2) && abs($paidAmount - $runningSubtotal) > 0.01) {
                throw new \Exception("Jumlah bayar (Rp " . number_format($paidAmount) . ") tidak mencukupi. Total belanja: Rp " . number_format($runningSubtotal));
            }

            // Perhitungan kembalian
            $changeAmount = $paidAmount - $runningSubtotal;
            if ($changeAmount < 0) $changeAmount = 0;

            // Update nilai transaksi akhir
            $transaction->total_amount = $grandTotal;
            $transaction->tax_amount = $taxAmount;
            $transaction->change_amount = $changeAmount;
            $transaction->save();

            // Commit database transaction
            DB::commit();

            // Redirect ke halaman kasir
            $redirect = redirect('/cashier')
                ->with('success', 'Transaksi berhasil disimpan! Invoice: ' . $invoiceNumber);

            // Flag untuk mencetak struk
            if ($request->input('print_receipt') === 'true') {
                $redirect->with('print_invoice', $invoiceNumber);
            }

            return $redirect;
        } catch (\Exception $e) {
            // Rollback jika terjadi kesalahan
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Menampilkan dan mencetak struk transaksi.
     *
     * Method ini mengambil data transaksi berdasarkan
     * nomor invoice beserta detail produknya,
     * lalu menampilkannya dalam bentuk struk.
     *
     * @param string $invoiceNumber
     * @return \Illuminate\View\View
     */
    public function print($invoiceNumber)
    {
        $transaction = Transactions::where('invoice_number', $invoiceNumber)
            ->with('details.product')
            ->firstOrFail();

        return view('content.Dashboard.Cashier.receipt', compact('transaction'));
    }
}
