<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Refunds;
use App\Models\StockIn;
use App\Models\Transactions;
use App\Models\TransactionDetail;
use App\Services\SalesAnomalyDetector;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsappService;

/**
 * Controller DashboardAdmin
 * Mengelola semua data ringkasan di halaman utama admin.
 */
class DashboardAdminController extends Controller
{
    public function index(Request $request)
    {
        // 1. ALUR FILTER WAKTU: Menentukan rentang data yang akan ditarik dari database
        $filter = $request->input('filter', '30_days');
        $dateInfo = $this->prepareDateRange($filter);

        $startDate = $dateInfo['start'];
        $endDate = $dateInfo['end'];
        $dateRange = $dateInfo['range'];
        $chartLabels = $dateInfo['labels'];

        // 2. STATISTIK HARIAN: Menghitung transaksi yang masuk khusus hari ini (Today)
        // Hitung Pendapatan Kotor (Semua transaksi 'paid' hari ini)
        $grossIncome = Transactions::where('status', 'paid')
            ->whereDate('transaction_date', Carbon::today())
            ->sum('total_amount');

        // Hitung Total Refund yang terjadi hari ini (berdasarkan tabel refunds)
        $todayRefunds = Refunds::whereDate('created_at', Carbon::today())
            ->sum('total_refund_amount');

        // Pendapatan Bersih (Net Income)
        $dailyIncome = $grossIncome - $todayRefunds;

        // Sesuai request: Menghitung biaya pengadaan stok hari ini (hanya purchase_price)
        $dailyExpenses = StockIn::whereDate('created_at', Carbon::today())
            ->sum('purchase_price');

        // 3. STATISTIK KESELURUHAN: Menghitung total semua data untuk laporan kumulatif
        $totalIncome = Transactions::where('status', 'paid')->sum('total_amount');
        $totalExpenses = StockIn::sum('purchase_price');

        // 4. PROFIT RATIO: Menghitung persentase keuntungan bersih
        $profitRatio = $totalIncome > 0
            ? round((($totalIncome - $totalExpenses) / $totalIncome) * 100, 2)
            : 0;

        // 5. ALUR AI: Mengirim data pendapatan hari ini ke AI Detector untuk cek ketidakwajaran
        // $fakeIncome = 5000000000;
        $anomalyAnalysis = $this->runAnomalyAnalysis($dailyIncome);

        // 6. ALUR GRAFIK: Mengambil data historis untuk divisualisasikan ke Chart
        $chartData = $this->fetchChartData($filter, $startDate, $endDate, $dateRange);

        // 7. PRODUK TERLARIS: Mencari 5 produk yang paling banyak dibeli (berdasarkan Qty)
        $bestSellingProducts = TransactionDetail::with('product')
            ->select('KdProduct', DB::raw('SUM(qty) as total_qty_sold'), DB::raw('SUM(subtotal) as total_sales_amount'))
            ->groupBy('KdProduct')
            ->orderBy('total_qty_sold', 'desc')
            ->take(5)
            ->get();

        // 8. ALERT STOK: Mengambil daftar barang yang stoknya sudah mau habis (<= 5 unit)
        $lowStockProducts = Product::where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->get();

        // 9. ALERT KADALUARSA: Mencari stok yang akan expired dalam 30 hari ke depan
        $expiringProducts = StockIn::with('products')
            ->whereNotNull('expired_date')
            ->where('expired_date', '<=', Carbon::now()->addDays(30))
            ->whereHas('products', fn($q) => $q->where('stock', '>', 0))
            ->orderBy('expired_date', 'asc')
            ->take(10)
            ->get();

        // 10. RESPONSE: Mengirim semua variabel di atas ke view Blade dashboard
        return view('content.dashboard.index', [
            'dailyIncome'         => $dailyIncome,
            'dailyExpenses'       => $dailyExpenses,
            'totalIncome'         => $totalIncome,
            'totalExpenses'       => $totalExpenses,
            'profitRatio'         => $profitRatio,
            'bestSellingProducts' => $bestSellingProducts,
            'lowStockProducts'    => $lowStockProducts,
            'expiringProducts'    => $expiringProducts,
            'chartLabels'         => $chartLabels,
            'chartIncomeData'     => $chartData['income'],
            'chartExpenseData'    => $chartData['expense'],
            'hasChartData'        => $chartData['hasData'],
            'filterText'          => $dateInfo['text'],
            'isAnomaly'           => $anomalyAnalysis['isAnomaly'],
            'anomalyMessage'      => $anomalyAnalysis['message']
        ]);
    }

    /**
     * Method untuk menjalankan logika deteksi anomali AI
     */
    private function runAnomalyAnalysis($dailyIncome)
    {
        $detector = new SalesAnomalyDetector();
        $result = $detector->detect($dailyIncome);

        if ($result['status'] !== 'success') {
            return [
                'isAnomaly' => false,
                'score' => 0,
                'message' => 'Model belum dilatih'
            ];
        }

        $score = $result['score'];
        $threshold = 0.65;

        \Log::info('AI ANOMALY CHECK', [
            'income' => $dailyIncome,
            'log_income' => log10(max($dailyIncome,1)),
            'score' => $score
        ]);

        if ($score >= $threshold) {
            WhatsappService::sendMessage(
                "628111720050",
                "🚨 *ANOMALI PENJUALAN*\n\n" .
                "Pendapatan: Rp " . number_format($dailyIncome,0,',','.') . "\n" .
                "Score: " . number_format($score, 2)
            );

            return [
                'isAnomaly' => true,
                'score' => $score,
                'message' => "⚠️ Anomali (" . number_format($score, 2) . ")"
            ];
        }

        return [
            'isAnomaly' => false,
            'score' => $score,
            'message' => "Normal (" . number_format($score, 2) . ")"
        ];
    }


    /**
     * Method untuk menentukan rentang tanggal berdasarkan filter dropdown di UI
     */
    private function prepareDateRange($filter)
    {
        $range = collect();
        $labels = [];
        $text = "30 Hari Terakhir";
        $start = Carbon::today()->subDays(29);
        $end = Carbon::today();

        // Logika untuk filter "Bulan Ini"
        if ($filter == 'this_month') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            $text = "Bulan Ini";
            for ($i = 0; $i < Carbon::now()->day; $i++) {
                $date = $start->copy()->addDays($i);
                $range->push($date->format('Y-m-d'));
                $labels[] = $date->format('d M');
            }
        // Logika untuk filter "Tahun Ini"
        } elseif ($filter == 'this_year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
            $text = "Tahun Ini";
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        } else {
            // Default: 30 Hari Terakhir
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $range->push($date->format('Y-m-d'));
                $labels[] = $date->format('d M');
            }
        }

        return ['start' => $start, 'end' => $end, 'range' => $range, 'labels' => $labels, 'text' => $text];
    }

    /**
     * Method untuk mengambil data histori Pendapatan vs Pengeluaran untuk grafik
     */
    private function fetchChartData($filter, $start, $end, $range)
    {
        if ($filter == 'this_year') {
            // Tarik data per Bulan untuk tampilan Tahun Ini
            $income = Transactions::where('status', 'paid')
                ->whereBetween('transaction_date', [$start, $end])
                ->select(DB::raw('MONTH(transaction_date) as m'), DB::raw('SUM(total_amount) as t'))
                ->groupBy('m')->pluck('t', 'm');

            $expense = StockIn::whereBetween('created_at', [$start, $end])
                ->select(DB::raw('MONTH(created_at) as m'), DB::raw('SUM(purchase_price) as t'))
                ->groupBy('m')->pluck('t', 'm');

            $incomeData = [];
            $expenseData = [];
            for ($m = 1; $m <= 12; $m++) {
                $incomeData[] = $income[$m] ?? 0;
                $expenseData[] = $expense[$m] ?? 0;
            }
        } else {
            // Tarik data per Hari untuk tampilan 30 Hari atau Bulan Ini
            $income = Transactions::where('status', 'paid')
                ->whereBetween('transaction_date', [$start, $end])
                ->select(DB::raw('DATE(transaction_date) as d'), DB::raw('SUM(total_amount) as t'))
                ->groupBy('d')->pluck('t', 'd');

            $expense = StockIn::whereBetween('created_at', [$start, $end])
                ->select(DB::raw('DATE(created_at) as d'), DB::raw('SUM(purchase_price) as t'))
                ->groupBy('d')->pluck('t', 'd');

            // Map data agar tanggal yang kosong di database tetap bernilai 0 di grafik
            $incomeData = $range->map(fn($d) => $income[$d] ?? 0)->toArray();
            $expenseData = $range->map(fn($d) => $expense[$d] ?? 0)->toArray();
        }

        return [
            'income'  => $incomeData,
            'expense' => $expenseData,
            'hasData' => (array_sum($incomeData) + array_sum($expenseData)) > 0
        ];
    }
}
