<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SalesAnomalyDetector;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;

class TrainAnomalyDetector extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ml:train-anomaly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Train the Isolation Forest model for sales anomaly detection.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Memulai pelatihan detektor anomali...');

        try {
            // Pastikan folder 'ml' di storage sudah ada
            if (!is_dir(storage_path('app/ml'))) {
                mkdir(storage_path('app/ml'), 0777, true);
            }

            $detector = new SalesAnomalyDetector();
            $detector->train();

            $this->info('Pelatihan selesai! Model disimpan di storage/app/ml/isolation_forest.model');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Pelatihan gagal: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
