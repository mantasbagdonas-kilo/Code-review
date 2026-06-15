<?php

namespace App\Console\Commands;

use App\Services\Analytics\MetricsFileImporter;
use Illuminate\Console\Command;

class BackfillMetrics extends Command
{
    protected $signature = 'analytics:backfill {file}';

    protected $description = 'Backfill historical metric points from a CSV dump';

    public function handle(MetricsFileImporter $importer): int
    {
        $count = $importer->import($this->argument('file'));

        $this->info("Backfilled {$count} rows");

        return self::SUCCESS;
    }
}
