<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Product;
use App\Models\Refunds;
use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Models\RefundsDetail;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Class RefundsControllers
 *
 * Controller ini menangani proses refund transaksi penjualan.
 * Proses refund dilakukan secara aman menggunakan database transaction
 * untuk menjaga konsistensi data antara:
 * - Refund header
 * - Detail refund
 * - Detail transaksi
 * - Stok produk
 *
 * @package App\Http\Controllers\Dashboard
 */
class RefundsControllers extends Controller
{
    /**
     * Menyimpan data refund transaksi.
     *
     * Method ini digunakan untuk:
     * 1. Melakukan validasi input refund
     * 2. Membuat data header refund
     * 3. Memproses setiap item yang di-refund
     * 4. Memastikan jumlah refund tidak melebihi batas transaksi
     * 5. Mengembalikan stok produk
     * 6. Menghitung total nilai refund
     * 7. Menyimpan seluruh proses secara atomik menggunakan DB Transaction
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validasi dasar input refund
        $request->validate([
            'transaction_id' => 'required|string|exists:transactions,invoice_number',
            'items' => 'required|array',
            'items.*.qty' => 'numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Mengambil data input dari request
        $itemsToRefund = $request->input('items', []);
        $transactionId = $request->input('transaction_id');
        $notes = $request->input('notes');
        $userId = Auth::id(); // ID user/kasir yang sedang login
        $totalRefundAmount = 0;

        // 2. Memulai Database Transaction untuk menjaga konsistensi data
        DB::beginTransaction();

        try {
            // 3. Membuat data header refund
            $refund = Refunds::create([
                'total_refund_amount' => 0, // Nilai awal, akan diperbarui di akhir proses
                'notes' => $notes,
                'user_id' => $userId,
                'transaction_id' => $transactionId,
            ]);

            // 4. Memproses setiap item yang akan di-refund
            foreach ($itemsToRefund as $transactionDetailId => $item) {
                $refundQty = (int) $item['qty'];

                // Hanya memproses item dengan qty refund > 0
                if ($refundQty > 0) {

                    // Mengambil data detail transaksi asli
                    $transactionDetail = TransactionDetail::findOrFail($transactionDetailId);

                    // 5. Validasi jumlah refund agar tidak melebihi sisa qty transaksi
                    $availableToRefund = $transactionDetail->qty - $transactionDetail->refunded_qty;
                    if ($refundQty > $availableToRefund) {
                        // Batalkan seluruh proses jika jumlah tidak valid
                        throw new \Exception(
                            "Jumlah refund untuk produk '{$transactionDetail->product->nameProduct}' melebihi batas yang tersedia."
                        );
                    }

                    // Menghitung subtotal refund
                    $price = (float) $item['price'];
                    $subtotal = $price * $refundQty;
                    $totalRefundAmount += $subtotal;

                    // 6. Menyimpan detail refund
                    RefundsDetail::create([
                        'refund_id' => $refund->id,
                        'KdProduct' => $item['KdProduct'],
                        'qty' => $refundQty,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ]);

                    // 7. Update qty refund pada detail transaksi
                    $transactionDetail->increment('refunded_qty', $refundQty);

                    // 8. Mengembalikan stok produk
                    $product = Product::where('KdProduct', $item['KdProduct'])->first();
                    if ($product) {
                        $product->increment('stock', $refundQty);
                    }
                }
            }

            // Validasi jika tidak ada item yang di-refund
            if ($totalRefundAmount == 0) {
                throw new \Exception("Tidak ada barang yang dipilih untuk di-refund.");
            }

            // 9. Update total nilai refund pada header refund
            $refund->total_refund_amount = $totalRefundAmount;
            $refund->save();

            // 10. Commit database transaction jika semua proses berhasil
            DB::commit();

            return redirect()->back()->with('success', 'Refund berhasil diproses.');
        } catch (\Exception $e) {
            // 11. Rollback database transaction jika terjadi kesalahan
            DB::rollBack();

            return redirect()->back()->with(
                'error',
                'Gagal memproses refund: ' . $e->getMessage()
            );
        }
    }
}
