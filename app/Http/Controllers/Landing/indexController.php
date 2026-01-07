<?php

/**
 * indexController
 * ----------------
 * Controller ini bertanggung jawab untuk menampilkan halaman utama (Landing Page)
 * aplikasi serta mengambil data produk terlaris berdasarkan riwayat transaksi.
 * 
 * Data produk terlaris dihitung berdasarkan:
 * - Total kuantitas penjualan (qty)
 * - Total nilai penjualan (subtotal)
 */

namespace App\Http\Controllers\Landing;

use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class indexController extends Controller
{
    /**
     * Menampilkan halaman landing page beserta produk terlaris
     *
     * Method ini mengambil 4 produk dengan jumlah penjualan terbanyak
     * dari tabel transaction_details dengan melakukan agregasi data
     * menggunakan SUM dan GROUP BY.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil data produk terlaris berdasarkan jumlah penjualan
        $bestSellingProducts = TransactionDetail::with('product')
            // Menentukan kolom yang digunakan beserta agregasi data
            ->select(
                'KdProduct',
                DB::raw('SUM(qty) as total_qty_sold'),
                DB::raw('SUM(subtotal) as total_sales_amount')
            )
            // Mengelompokkan data berdasarkan kode produk
            ->groupBy('KdProduct')
            // Mengurutkan dari produk dengan penjualan terbanyak
            ->orderBy('total_qty_sold', 'desc')
            // Membatasi jumlah data yang ditampilkan (Top 4)
            ->take(4)
            // Menjalankan query dan mengambil data
            ->get();

        // Mengirim data produk terlaris ke halaman landing page
        return view('content.Landing.index', compact('bestSellingProducts'));
    }
}
