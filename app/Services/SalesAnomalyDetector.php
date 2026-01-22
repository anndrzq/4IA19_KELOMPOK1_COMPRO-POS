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
    protected $persister;
    protected $modelPath;

    public function __construct()
    {
        $this->modelPath = storage_path('app/ml/isolation_forest.model');
        $this->persister = new Filesystem($this->modelPath);
    }

    /**
     * Ambil data training (LOG revenue ONLY)
     */
    protected function fetchTrainingData(): array
    {
        $samples = [];

        // 1️⃣ Ambil SEMUA transaksi paid
        $transactions = Transactions::where('status', 'paid')->get();

        foreach ($transactions as $trx) {
            $samples[] = [
                log10(max((float) $trx->total_amount, 1))
            ];
        }

        return $samples;
    }



    /**
     * Train model
     */
    public function train()
    {
        $samples = $this->fetchTrainingData();

        if (count($samples) < 3) {
            throw new \Exception("Data training belum cukup (min 3 transaksi)");
        }


        $estimator = new IsolationForest(
            200,   // trees
            0.7,   // subsample
            0.05   // contamination
        );

        $dataset = Unlabeled::build($samples);
        $estimator->train($dataset);

        (new PersistentModel($estimator, $this->persister))->save();
    }

    /**
     * Deteksi anomali
     */
    public function detect(float $revenue): array
    {
        if (!file_exists($this->modelPath)) {
            return ['status' => 'untrained', 'score' => 0];
        }

        $model = PersistentModel::load($this->persister);

        $revenue = max($revenue, 1);
        $logRevenue = log10($revenue);

        $sample = [[$logRevenue]];
        $scores = $model->base()->predict(Unlabeled::build($sample));

        return [
            'status' => 'success',
            'score'  => $scores[0] ?? 0
        ];
    }
}
