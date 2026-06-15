<?php

namespace App\Services\Analytics;

use App\Models\MetricDailyTotal;

class MetricsReport
{
    /**
     * Build a CSV of every daily total for the finance dashboard download.
     */
    public function toCsv(): string
    {
        $rows = MetricDailyTotal::all();

        $csv = "account_id,metric,date,total\n";
        foreach ($rows as $row) {
            $csv .= $row->account_id . ','
                . $row->metric . ','
                . $row->date . ','
                . $row->total . "\n";
        }

        return $csv;
    }
}
