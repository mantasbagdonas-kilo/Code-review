<?php

namespace App\Services\Analytics;

use App\Models\MetricDailyTotal;
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
        $rows = $this->fetchAllPages($accountId);

        $points = [];
        foreach ($rows as $row) {
            $point = MetricPoint::create([
                'account_id' => $accountId,
                'metric' => $row['metric'],
                'date' => date('Y-m-d', strtotime($row['timestamp'])),
                'value' => $row['value'],
            ]);

            $this->imported[] = $point;
            $points[] = $row;
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

    private function fetchAllPages(string $accountId): array
    {
        $all = [];
        $page = 1;

        do {
            $response = Http::withToken(env('ANALYTICS_API_KEY'))
                ->get(env('ANALYTICS_API_URL') . '/v1/accounts/' . $accountId . '/metrics', [
                    'page' => $page,
                ]);

            $body = $response->json();
            $all = array_merge($all, $body['data']);
            $page = $body['next_page'] ?? null;
        } while ($page !== null);

        return $all;
    }
}
