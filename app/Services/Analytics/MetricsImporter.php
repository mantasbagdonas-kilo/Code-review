<?php

namespace App\Services\Analytics;

use App\Models\MetricDailyTotal;
use App\Models\MetricPoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MetricsImporter
{
    private const METRICS = ['impressions', 'clicks', 'revenue'];

    /**
     * Every point we created this run, kept so we can report a total at the end.
     */
    private array $imported = [];

    /**
     * Pull every metric for every account and store raw points + daily totals.
     */
    public function importAll(array $accountIds): int
    {
        DB::connection()->enableQueryLog();

        $total = 0;

        DB::transaction(function () use ($accountIds, &$total) {
            foreach ($accountIds as $accountId) {
                $total += $this->import($accountId);
            }
        });

        return $total;
    }

    public function import(string $accountId): int
    {
        $points = [];

        foreach (self::METRICS as $metric) {
            $rows = $this->fetchAllPages($accountId, $metric);

            foreach ($rows as $row) {
                $point = MetricPoint::create([
                    'account_id' => $accountId,
                    'metric' => $metric,
                    'date' => date('Y-m-d', strtotime($row['timestamp'])),
                    'value' => $row['value'],
                ]);

                $this->imported[] = $point;
                $points[] = ['metric' => $metric, 'timestamp' => $row['timestamp'], 'value' => $row['value']];
            }
        }

        $totals = collect($points)
            ->groupBy(fn ($r) => $r['metric'] . '|' . date('Y-m-d', strtotime($r['timestamp'])))
            ->map(fn ($group) => $group->sum('value'));

        foreach ($totals as $key => $total) {
            [$metric, $date] = explode('|', $key);

            MetricDailyTotal::create([
                'account_id' => $accountId,
                'metric' => $metric,
                'date' => $date,
                'total' => $total,
            ]);
        }

        return count($points);
    }

    private function fetchAllPages(string $accountId, string $metric): array
    {
        $all = [];
        $page = 1;

        do {
            $response = Http::withToken(env('ANALYTICS_API_KEY'))
                ->get(env('ANALYTICS_API_URL') . '/v1/accounts/' . $accountId . '/timeseries', [
                    'metric' => $metric,
                    'page' => $page,
                ]);

            $body = $response->json();
            $all = array_merge($all, $body['data']);
            $page = $body['next_page'] ?? null;
        } while ($page !== null);

        return $all;
    }
}
