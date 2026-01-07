<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Transactions;
use Illuminate\Http\Request;

/**
 * Class salesHistoryController
 *
 * Controller ini bertanggung jawab untuk menampilkan
 * riwayat transaksi penjualan (Sales History).
 *
 * Data transaksi yang ditampilkan sudah dilengkapi dengan:
 * - Data user (kasir/admin)
 * - Detail transaksi
 * - Informasi produk pada setiap detail transaksi
 *
 * @package App\Http\Controllers\Dashboard
 */
class salesHistoryController extends Controller
{
    /**
     * Menampilkan halaman riwayat penjualan.
     *
     * Method ini akan:
     * - Mengambil seluruh data transaksi
     * - Melakukan eager loading relasi user, detail transaksi,
     *   dan produk untuk menghindari masalah N+1 query
     * - Mengirim data transaksi ke view laporan riwayat penjualan
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil seluruh data transaksi beserta relasinya
        $transactions = Transactions::with('user', 'details.product')->get();

        // Mengirim data transaksi ke halaman Sales History
        return view('content.Dashboard.Report.SalesHistory.index', compact('transactions'));
    }
}
