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

class RefundsControllers extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi dasar
        $request->validate([
            'transaction_id' => 'required|string|exists:transactions,invoice_number',
            'items' => 'required|array',
            'items.*.qty' => 'numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $itemsToRefund = $request->input('items', []);
        $transactionId = $request->input('transaction_id');
        $notes = $request->input('notes');
        $userId = Auth::id(); // Mengambil ID kasir yang login
        $totalRefundAmount = 0;

        // 2. Gunakan DB Transaction untuk keamanan data
        DB::beginTransaction();

        try {
            // 3. Buat header Refund
            $refund = Refunds::create([
                'total_refund_amount' => 0, // Akan kita update nanti
                'notes' => $notes,
                'user_id' => $userId,
                'transaction_id' => $transactionId,
            ]);

            // 4. Loop setiap item yang di-refund
            foreach ($itemsToRefund as $transactionDetailId => $item) {
                $refundQty = (int) $item['qty'];

                // Hanya proses jika qty > 0
                if ($refundQty > 0) {
                    // Cari detail transaksi aslinya
                    $transactionDetail = TransactionDetail::findOrFail($transactionDetailId);

                    // 5. Validasi penting: Cek sisa qty yang bisa di-refund
                    $availableToRefund = $transactionDetail->qty - $transactionDetail->refunded_qty;
                    if ($refundQty > $availableToRefund) {
                        // Jika tidak valid, batalkan semua
                        throw new \Exception("Jumlah refund untuk produk '{$transactionDetail->product->nameProduct}' melebihi batas yang tersedia.");
                    }

                    // Hitung subtotal refund
                    $price = (float) $item['price'];
                    $subtotal = $price * $refundQty;
                    $totalRefundAmount += $subtotal;

                    // 6. Buat record di refunds_details
                    RefundsDetail::create([
                        'refund_id' => $refund->id,
                        'KdProduct' => $item['KdProduct'],
                        'qty' => $refundQty,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ]);

                    // 7. Update kolom 'refunded_qty' di transaction_details
                    $transactionDetail->increment('refunded_qty', $refundQty);

                    // 8. Kembalikan stok produk
                    $product = Product::where('KdProduct', $item['KdProduct'])->first();
                    if ($product) {
                        $product->increment('stock', $refundQty);
                    }
                }
            }

            // Jika tidak ada item yang di-refund sama sekali
            if ($totalRefundAmount == 0) {
                throw new \Exception("Tidak ada barang yang dipilih untuk di-refund.");
            }

            // 9. Update total amount di header Refund
            $refund->total_refund_amount = $totalRefundAmount;
            $refund->save();

            // 10. Jika semua sukses, commit transaksi
            DB::commit();

            return redirect()->back()->with('success', 'Refund berhasil diproses.');
        } catch (\Exception $e) {
            // 11. Jika ada error, batalkan semua (rollback)
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses refund: ' . $e->getMessage());
        }
    }
}
