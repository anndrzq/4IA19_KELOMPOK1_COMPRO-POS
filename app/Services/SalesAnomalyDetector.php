<?php

namespace App\Services;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\AnomalyDetectors\IsolationForest;
use App\Models\Transactions;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service SalesAnomalyDetector
 * * Menggunakan algoritma Isolation Forest untuk mendeteksi penyimpangan (anomali)
 * pada data pendapatan harian berdasarkan tren historis.
 */
class SalesAnomalyDetector
{
    protected $persister;
    protected $modelPath;

    public function __construct()
    {
        // Menentukan lokasi penyimpanan file model hasil training (binary)
        $this->modelPath = storage_path('app/ml/isolation_forest.model');

        // Persister menangani proses simpan/muat model dari penyimpanan fisik
        $this->persister = new Filesystem($this->modelPath);
    }

    /**
     * Mengambil data historis untuk bahan ajar AI.
     * Menggunakan fitur multidimensi: Indeks Hari + Total Pendapatan.
     */
    protected function fetchTrainingData(): array
    {
        // Mengambil data transaksi 'paid' selama 6 bulan terakhir (180 hari)
        $data = Transactions::where('status', 'paid')
            ->whereBetween('transaction_date', [Carbon::now()->subDays(180), Carbon::now()->subDay()])
            ->select(
                DB::raw('DAYOFWEEK(transaction_date) as day_index'), // Fitur 1: Konteks hari (1-7)
                DB::raw('SUM(total_amount) as revenue')             // Fitur 2: Nominal pendapatan
            )
            // Grouping berdasarkan tanggal unik agar AI belajar pola pendapatan per hari
            ->groupBy(DB::raw('DATE(transaction_date)'), DB::raw('DAYOFWEEK(transaction_date)'))
            ->get();

        if ($data->isEmpty()) return [];

        $samples = [];
        foreach ($data as $row) {
            $revenue = (float) $row->revenue;

            /**
             * PENANGANAN ERROR:
             * Jika pendapatan 0, diubah menjadi 0.01.
             * Algoritma Isolation Forest membutuhkan variasi data. Angka 0 yang terlalu banyak
             * pada dataset kecil dapat menyebabkan error matematika "Division by Zero".
             */
            if ($revenue <= 0) {
                $revenue = 0.01;
            }

            // Memasukkan fitur ke dalam array dua dimensi untuk diproses Rubix ML
            $samples[] = [(int) $row->day_index, $revenue];
        }

        return $samples;
    }

    /**
     * Melatih model AI berdasarkan data yang tersedia di database.
     */
    public function train()
    {
        $samples = $this->fetchTrainingData();

        // Syarat minimal data agar AI memiliki referensi pola yang cukup (7 hari unik)
        if (count($samples) < 7) {
            throw new \Exception("Data tidak cukup. Butuh minimal 7 hari data transaksi untuk melatih AI.");
        }

        /**
         * Konfigurasi Isolation Forest:
         * - 100: Jumlah 'pohon' keputusan. Semakin banyak semakin akurat, tapi beban CPU naik.
         * - 0.1: Contamination (rasio anomali). Asumsi bahwa 10% data di masa lalu mungkin adalah anomali.
         */
        $estimator = new IsolationForest(100, 0.1);

        // Membangun dataset tanpa label (Unlabeled) karena ini adalah Unsupervised Learning
        $dataset = Unlabeled::build($samples);

        // Proses pembelajaran dimulai di sini
        $estimator->train($dataset);

        // Membungkus model agar bisa disimpan secara permanen di server
        $persistentModel = new PersistentModel($estimator, $this->persister);
        $persistentModel->save();
    }

    /**
     * Mendeteksi skor anomali untuk pendapatan hari ini.
     * * @param float $todaysRevenue Total pendapatan yang masuk hari ini.
     * @return array Score (0.0 - 1.0) dan Status deteksi.
     */
    public function detect(float $todaysRevenue): array
    {
        // Jika model belum ada (belum pernah ditrain), jangan jalankan deteksi
        if (!file_exists($this->modelPath)) {
            return ['score' => 0.0, 'status' => 'untrained'];
        }

        try {
            // Memuat kembali model yang sudah terlatih dari file storage
            $model = PersistentModel::load($this->persister);

            // Konsistensi data: gunakan jitter 0.01 jika pendapatan hari ini nol
            $inputRevenue = $todaysRevenue <= 0 ? 0.01 : $todaysRevenue;

            // Mendapatkan indeks hari saat ini (1-7) agar relevan dengan data training
            $currentDayIndex = Carbon::now()->dayOfWeekIso;

            // Format input harus sama dengan format saat training (Array 2D)
            $sample = [[$currentDayIndex, $inputRevenue]];

            // Melakukan prediksi (semakin tinggi skor mendekati 1.0, semakin aneh datanya)
            $scores = $model->base()->predict(Unlabeled::build($sample));

            return [
                'score' => $scores[0] ?? 0.0,
                'status' => 'success'
            ];
        } catch (\DivisionByZeroError $e) {
            // Penanganan jika data training terlalu identik sehingga gagal hitung rasio
            return ['score' => 0.0, 'status' => 'error'];
        } catch (\Exception $e) {
            // Penanganan error umum (file korup, library error, dsb)
            return ['score' => 0.0, 'status' => 'error'];
        }
    }
}
