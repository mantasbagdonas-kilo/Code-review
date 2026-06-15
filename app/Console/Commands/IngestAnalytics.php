<?php

namespace App\Console\Commands;

use App\Services\Analytics\MetricsImporter;
use Illuminate\Console\Command;

class IngestAnalytics extends Command
{
    protected $signature = 'analytics:ingest';

    protected $description = 'Pull daily timeseries metrics for all accounts from the analytics provider';

    public function handle(MetricsImporter $importer): int
    {
        $accounts = config('analytics.accounts', []);

        $total = $importer->importAll($accounts);

        $this->info("Imported {$total} metric points");

        return self::SUCCESS;
    }
}
