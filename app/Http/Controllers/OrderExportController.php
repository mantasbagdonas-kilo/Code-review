<?php

namespace App\Http\Controllers;

use App\Services\OrderExporter;

class OrderExportController extends Controller
{
    public function __invoke(OrderExporter $exporter)
    {
        $csv = $exporter->toCsv();

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders.csv"',
        ]);
    }
}
