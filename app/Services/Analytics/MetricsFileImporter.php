<?php

namespace App\Services\Analytics;

use App\Models\MetricPoint;
use Illuminate\Support\Facades\DB;

class MetricsFileImporter
{
    /**
     * Backfill historical metric points from a CSV dump we missed in the live feed.
     * File columns: account_id,ad_id,metric,recorded_at,value
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

            [$accountId, $adId, $metric, $recordedAt, $value] = str_getcsv($line);

            $rows[] = [
                'account_id' => $accountId,
                'ad_id' => $adId,
                'metric' => $metric,
                'recorded_at' => $recordedAt,
                'value' => $value,
            ];
        }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $exists = MetricPoint::where('account_id', $row['account_id'])
                    ->where('ad_id', $row['ad_id'])
                    ->where('metric', $row['metric'])
                    ->where('recorded_at', $row['recorded_at'])
                    ->exists();

                if (! $exists) {
                    MetricPoint::create($row);
                }
            }
        });

        return count($rows);
    }
}
