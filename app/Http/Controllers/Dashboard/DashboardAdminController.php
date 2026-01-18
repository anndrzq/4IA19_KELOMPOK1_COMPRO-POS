<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Product;
use App\Models\Refunds;
use App\Models\StockIn;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\SalesAnomalyDetector;

/**
 * Class DashboardAdminController
 *
 * Controller ini menangani halaman utama Dashboard Admin yang berfungsi
 * untuk menampilkan ringkasan kinerja sistem, antara lain:
 * - Grafik pendapatan dan pengeluaran
 * - Statistik pendapatan harian dan total
 * - Rasio keuntungan (profit ratio)
 * - Deteksi anomali penjualan menggunakan Machine Learning
 * - Produk terlaris, stok menipis, dan produk mendekati kadaluarsa
 *
 * @package App\Http\Controllers\Dashboard
 */
class DashboardAdminController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin.
     *
     * Method ini:
     * - Mengelola filter waktu (30 hari, bulan ini, tahun ini)
     * - Menghasilkan data grafik pendapatan dan pengeluaran
     * - Menghitung statistik bisnis utama
     * - Menjalankan deteksi anomali pendapatan harian
     * - Menyediakan insight stok dan performa produk
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Mengambil filter waktu (default: 30 hari terakhir)
        $filter = $request->input('filter', '30_days');
        $filterText = "30 Hari Terakhir";

        // Inisialisasi variabel grafik
        $chartLabels = [];
        $chartIncomeData = [];
        $chartExpenseData = [];
        $startDate = Carbon::today()->subDays(29);
        $endDate = Carbon::today();
        $dateRange = collect();

        /**
         * Penentuan rentang tanggal dan label grafik
         * berdasarkan filter yang dipilih.
         */
        if ($filter == 'this_month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            $filterText = "Bulan Ini";

            for ($i = 0; $i < $endDate->day; $i++) {
                $date = $startDate->copy()->addDays($i);
                $dateRange->push($date->format('Y-m-d'));
                $chartLabels[] = $date->format('d M');
            }
        } elseif ($filter == 'this_year') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
            $filterText = "Tahun Ini";

            $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $chartIncomeData = array_fill(0, 12, 0);
            $chartExpenseData = array_fill(0, 12, 0);
        } else {
            $filterText = "30 Hari Terakhir";

            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dateRange->push($date->format('Y-m-d'));
                $chartLabels[] = $date->format('d M');
            }
        }

        /**
         * Pengambilan data pendapatan dan pengeluaran
         * berdasarkan rentang waktu yang dipilih.
         */
        if ($filter == 'this_year') {
            // Pendapatan per bulan
            $incomeData = Transactions::where('status', 'paid')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->select(DB::raw('MONTH(transaction_date) as month'), DB::raw('SUM(total_amount) as total'))
                ->groupBy('month')
                ->pluck('total', 'month');

            // Biaya stok masuk per bulan
            $expenseStockData = StockIn::whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(purchase_price * quantity) as total'))
                ->groupBy('month')
                ->pluck('total', 'month');

            // Biaya refund per bulan
            $expenseRefundData = Refunds::whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_refund_amount) as total'))
                ->groupBy('month')
                ->pluck('total', 'month');

            // Mapping data ke array grafik
            foreach ($incomeData as $month => $total) {
                $chartIncomeData[$month - 1] = $total;
            }
            foreach ($expenseStockData as $month => $total) {
                $chartExpenseData[$month - 1] += $total;
            }
            foreach ($expenseRefundData as $month => $total) {
                $chartExpenseData[$month - 1] += $total;
            }
        } else {
            // Pendapatan harian
            $incomeData = Transactions::where('status', 'paid')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->groupBy('date')
                ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(total_amount) as total'))
                ->pluck('total', 'date');

            // Biaya stok masuk harian
            $expenseStockData = StockIn::whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(purchase_price * quantity) as total'))
                ->pluck('total', 'date');

            // Biaya refund harian
            $expenseRefundData = Refunds::whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_refund_amount) as total'))
                ->pluck('total', 'date');

            foreach ($dateRange as $date) {
                $chartIncomeData[] = $incomeData[$date] ?? 0;
                $dailyExpenseStock = $expenseStockData[$date] ?? 0;
                $dailyExpenseRefund = $expenseRefundData[$date] ?? 0;
                $chartExpenseData[] = $dailyExpenseStock + $dailyExpenseRefund;
            }
        }

        // Mengecek apakah grafik memiliki data
        $hasChartData = (array_sum($chartIncomeData) + array_sum($chartExpenseData)) > 0;

        // Pendapatan dan pengeluaran hari ini
        $dailyIncome = Transactions::where('status', 'paid')
            ->whereDate('transaction_date', Carbon::today())
            ->sum('total_amount');

        $dailyStockInCost = StockIn::whereDate('created_at', Carbon::today())
            ->sum(DB::raw('purchase_price'));
        $dailyExpenses = $dailyStockInCost;

        // Total pendapatan dan pengeluaran keseluruhan
        $totalIncome = Transactions::where('status', 'paid')->sum('total_amount');
        $totalStockInCost = StockIn::sum(DB::raw('purchase_price'));
        $totalExpenses = $totalStockInCost;

        // Menghitung rasio keuntungan
        $profitRatio = 0;
        if ((float) $totalIncome > 0) {
            $profitRatio = round((($totalIncome - $totalExpenses) / $totalIncome) * 100, 2);
        }

        /**
         * Proses deteksi anomali pendapatan harian
         * menggunakan model Machine Learning (Isolation Forest).
         */
        $anomalyScore = 0.0;
        $isAnomaly = false;
        $anomalyMessage = "";
        $threshold = 0.55;

        $modelPath = storage_path('app/ml/isolation_forest.model');

        if (file_exists($modelPath)) {
            try {
                $detector = new SalesAnomalyDetector();
                $anomalyScore = $detector->detect($dailyIncome);

                $isAnomaly = $anomalyScore >= $threshold;
                $anomalyMessage = "Skor Anomali: " . number_format($anomalyScore, 2);

                if (!$isAnomaly && $anomalyScore > 0.0) {
                    $anomalyMessage = "Pendapatan hari ini normal. " . $anomalyMessage;
                } elseif ($anomalyScore == 0.0) {
                    if ($dailyIncome == 0) {
                        // Kondisi normal: tidak ada penjualan
                        $anomalyMessage = "Pendapatan hari ini normal. Skor Anomali: 0.00 (Rp 0).";
                    } else {
                        // Indikasi masalah pada model data latih
                        $anomalyMessage = "Deteksi anomali tidak dapat dijalankan. Skor 0.0 menunjukkan model tidak dapat mendeteksi pola yang jelas. (Kemungkinan data latih degenerate).";
                    }
                }
            } catch (\Exception $e) {
                $anomalyMessage = "Gagal memuat model deteksi anomali. Pastikan file model tidak rusak.";
            }
        } else {
            $anomalyMessage = "Model Anomali belum dilatih. Jalankan perintah 'php artisan ml:train-anomaly'.";
        }

        // Produk terlaris
        $bestSellingProducts = TransactionDetail::with('product')
            ->select(
                'KdProduct',
                DB::raw('SUM(qty) as total_qty_sold'),
                DB::raw('SUM(subtotal) as total_sales_amount')
            )
            ->groupBy('KdProduct')
            ->orderBy('total_qty_sold', 'desc')
            ->take(5)
            ->get();

        // Produk dengan stok menipis
        $lowStockProducts = Product::where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->get();

        // Produk yang mendekati tanggal kadaluarsa
        $expiringProducts = StockIn::with('products')
            ->whereNotNull('expired_date')
            ->where('expired_date', '<=', Carbon::now()->addDays(30))
            ->whereHas('products', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->orderBy('expired_date', 'asc')
            ->take(10)
            ->get();

        // Mengirim seluruh data ke view dashboard
        return view('content.dashboard.index', compact(
            'dailyIncome',
            'dailyExpenses',
            'totalIncome',
            'totalExpenses',
            'bestSellingProducts',
            'chartLabels',
            'chartIncomeData',
            'chartExpenseData',
            'filterText',
            'hasChartData',
            'lowStockProducts',
            'isAnomaly',
            'anomalyMessage',
            'expiringProducts'
        ));
    }
}
