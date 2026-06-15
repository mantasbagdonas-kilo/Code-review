<?php

namespace App\Services\Analytics;

use App\Models\MetricPoint;
use Illuminate\Support\Facades\DB;

class MetricsFileImporter
{
    /**
     * Backfill historical metric points from a CSV dump we missed in the live feed.
     * File columns: account_id,metric,date,value
     */
    public function import(string $path): int
    {
        $contents = file_get_contents($path);
        $lines = explode("\n", $contents);

        $rows = [];
        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }

            [$accountId, $metric, $date, $value] = str_getcsv($line);

            $rows[] = [
                'account_id' => $accountId,
                'metric' => $metric,
                'date' => $date,
                'value' => $value,
            ];
        }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $exists = MetricPoint::where('account_id', $row['account_id'])
                    ->where('metric', $row['metric'])
                    ->where('date', $row['date'])
                    ->exists();

                if (! $exists) {
                    MetricPoint::create($row);
                }
            }
        });

        return count($rows);
    }
}
