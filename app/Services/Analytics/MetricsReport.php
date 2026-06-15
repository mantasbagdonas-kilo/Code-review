<?php

namespace App\Services\Analytics;

use App\Models\MetricHourlyTotal;

class MetricsReport
{
    /**
     * Build a CSV of every hourly total for the finance dashboard download.
     */
    public function toCsv(): string
    {
        $rows = MetricHourlyTotal::all();

        $csv = "account_id,ad_id,metric,bucket,total\n";
        foreach ($rows as $row) {
            $csv .= $row->account_id . ','
                . $row->ad_id . ','
                . $row->metric . ','
                . $row->bucket . ','
                . $row->total . "\n";
        }

        return $csv;
    }
}
