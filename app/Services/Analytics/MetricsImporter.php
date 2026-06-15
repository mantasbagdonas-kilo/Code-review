<?php

namespace App\Services\Analytics;

use App\Models\MetricHourlyTotal;
use App\Models\MetricPoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MetricsImporter
{
    /**
     * Every point we created this run, kept so we can report a total at the end.
     */
    private array $imported = [];

    /**
     * Pull every ad's metrics for every account and store raw points + hourly totals.
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
        $ads = $this->fetchAllPages($accountId);

        $points = [];
        foreach ($ads as $ad) {
            foreach ($ad['metrics'] as $metric) {
                $point = MetricPoint::create([
                    'account_id' => $accountId,
                    'ad_id' => $ad['ad_id'],
                    'metric' => $metric['metric'],
                    'recorded_at' => $metric['timestamp'],
                    'value' => $metric['value'],
                ]);

                $this->imported[] = $point;
                $points[] = [
                    'ad_id' => $ad['ad_id'],
                    'metric' => $metric['metric'],
                    'timestamp' => $metric['timestamp'],
                    'value' => $metric['value'],
                ];
            }
        }

        $totals = collect($points)
            ->groupBy(fn ($r) => $r['ad_id'] . '|' . $r['metric'] . '|' . date('Y-m-d H:00:00', strtotime($r['timestamp'])))
            ->map(fn ($group) => $group->sum('value'));

        foreach ($totals as $key => $total) {
            [$adId, $metric, $bucket] = explode('|', $key);

            MetricHourlyTotal::create([
                'account_id' => $accountId,
                'ad_id' => $adId,
                'metric' => $metric,
                'bucket' => $bucket,
                'total' => $total,
            ]);
        }

        return count($points);
    }

    private function fetchAllPages(string $accountId): array
    {
        $all = [];
        $page = 1;

        do {
            $response = Http::withToken(env('ANALYTICS_API_KEY'))
                ->get(env('ANALYTICS_API_URL') . '/v1/accounts/' . $accountId . '/ads/metrics', [
                    'page' => $page,
                ]);

            // Example response body (one page):
            // {
            //   "data": [
            //     {
            //       "ad_id": "ad_1001",
            //       "metrics": [
            //         { "metric": "impressions", "timestamp": "2026-06-15T14:00:00Z", "value": 1820 },
            //         { "metric": "clicks",      "timestamp": "2026-06-15T14:00:00Z", "value": 47 },
            //         { "metric": "revenue",     "timestamp": "2026-06-15T14:00:00Z", "value": 215.40 }
            //       ]
            //     },
            //     {
            //       "ad_id": "ad_1002",
            //       "metrics": [
            //         { "metric": "impressions", "timestamp": "2026-06-15T14:00:00Z", "value": 990 }
            //       ]
            //     }
            //   ],
            //   "next_page": 2
            // }

            $body = $response->json();
            $all = array_merge($all, $body['data']);
            $page = $body['next_page'] ?? null;
        } while ($page !== null);

        return $all;
    }
}
