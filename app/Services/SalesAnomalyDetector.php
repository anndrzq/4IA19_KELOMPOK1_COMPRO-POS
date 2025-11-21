<?php

namespace App\Services;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\AnomalyDetectors\IsolationForest;
use App\Models\Transactions;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesAnomalyDetector
{
    protected $model;
    protected $persister;

    public function __construct()
    {
        $this->model = new IsolationForest(200, 0.05);
        $this->persister = new Filesystem(storage_path('app/ml/isolation_forest.model'));
    }

    protected function fetchTrainingData(): array
    {
        $startDate = Carbon::now()->subDays(90);
        $endDate = Carbon::now()->subDay();

        $data = Transactions::where('status', 'paid')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('date')
            ->pluck('revenue')
            ->toArray();

        return array_map(fn($rev) => [$rev], $data);
    }

    public function train()
    {
        $samples = $this->fetchTrainingData();

        if (empty($samples)) {
            throw new \Exception("Tidak ada data penjualan yang cukup untuk pelatihan.");
        }

        $dataset = Unlabeled::build($samples);

        $this->model->train($dataset);

        $persistentModel = new PersistentModel($this->model, $this->persister);
        $persistentModel->save();
    }

    public function detect(float $todaysRevenue): float
    {
        try {
            $model = PersistentModel::load($this->persister);

            $sample = [[$todaysRevenue]];

            $scores = $model->base()->predict(Unlabeled::build($sample));

            return $scores[0] ?? 0.0;
        } catch (\DivisionByZeroError $e) {
            return 0.0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }
}
