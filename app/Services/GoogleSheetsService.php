<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoogleSheetsService
{
    private string $scriptUrl;
    private string $salesScriptUrl;

    public function __construct()
    {
        $this->scriptUrl = config('services.google_sheets.script_url');
        // Asumsikan sama dengan script_url tapi dengan parameter berbeda
        $this->salesScriptUrl = config('services.google_sheets.script_url');
    }

    private function getDataFromScript(?string $startDate, ?string $endDate, string $range, ?string $sales = null): array
    {
        try {
            $params = [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'range' => $range,
            ];

            // Tambah parameter sales jika ada
            if ($sales && $sales !== '') {
                $params['sales'] = $sales;
            }

            $response = Http::timeout(30)->get($this->scriptUrl, $params);

            if ($response->failed()) {
                Log::error('Failed fetching from Google Apps Script', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => $this->scriptUrl,
                    'params' => $params
                ]);
                throw new \Exception("Google Sheets API returned status {$response->status()}: {$response->body()}");
            }

            $data = $response->json();

            // Jika gagal parse JSON atau bukan array, throw exception
            if (!is_array($data)) {
                Log::error('Invalid JSON response from Google Apps Script', [
                    'response_body' => $response->body(),
                    'parsed_data' => $data
                ]);
                throw new \Exception('Invalid response format from Google Sheets API');
            }

            Log::info('Successfully fetched data from Google Sheets', [
                'rows_count' => count($data),
                'params' => $params
            ]);

            return $data;
        } catch (\Exception $e) {
            Log::error('Exception from Google Apps Script call', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'params' => $params ?? []
            ]);
            throw $e; // Re-throw untuk proper error handling
        }
    }

    // METODE BARU: Satu request untuk semua data
    public function getAllAnalyticsData(?string $startDate = null, ?string $endDate = null, ?string $sales = null): array
    {
        try {
            // Ambil semua data A2:P dalam satu request
            $data = $this->getDataFromScript($startDate, $endDate, 'A2:P', $sales);

            // Log untuk debugging
            Log::info('Processing analytics data', [
                'total_rows' => count($data),
                'date_range' => "{$startDate} to {$endDate}",
                'sales_filter' => $sales
            ]);

            // Preprocessing semua statistik sekaligus
            $result = [
                'payment_methods' => $this->calculatePaymentMethodStats($data, 5),
                'programs' => $this->calculateStats($data, 6),
                'models' => $this->calculateStats($data, 4),
                'status' => $this->calculateStatusSummary($data, 8),
                'available_sales' => $this->extractSalesFromData($data, $sales)
            ];

            // Log summary
            Log::info('Analytics data processed successfully', [
                'payment_methods_total' => $result['payment_methods']['total'],
                'programs_total' => $result['programs']['total'],
                'models_total' => $result['models']['total'],
                'status_categories' => count($result['status']['categories']),
                'available_sales_count' => count($result['available_sales'])
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Error in getAllAnalyticsData', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e; // Re-throw untuk proper error handling di controller
        }
    }

    // Helper method untuk extract sales dari data yang sudah ada
    private function extractSalesFromData(array $data, ?string $currentSales = null): array
    {
        // Hanya extract sales jika tidak ada filter sales
        if ($currentSales && $currentSales !== '') {
            return [];
        }

        $salesSet = [];
        foreach ($data as $row) {
            // Column P = index 15
            if (isset($row[15]) && !empty(trim($row[15]))) {
                $salesName = trim($row[15]);
                if (!in_array($salesName, $salesSet)) {
                    $salesSet[] = $salesName;
                }
            }
        }

        sort($salesSet);
        return $salesSet;
    }

    // HAPUS atau DEPRECATE method-method lama ini (keep untuk backward compatibility jika diperlukan)
    // public function getPaymentMethodStats() - DEPRECATED
    // public function getProgramStats() - DEPRECATED
    // public function getModelStats() - DEPRECATED
    // public function getStatusOverTimeStats() - DEPRECATED
    // public function extractSalesFromStatusData() - DEPRECATED

    // --- Statistik processing di bawah ini sama seperti sebelumnya ---

    private function calculatePaymentMethodStats(array $data, int $columnIndex): array
    {
        $counts = ['Cash' => 0, 'Credit' => 0];
        $total = 0;

        foreach ($data as $row) {
            if (isset($row[$columnIndex]) && !empty(trim($row[$columnIndex]))) {
                $method = trim($row[$columnIndex]);
                if (in_array($method, ['Cash', 'Credit'])) {
                    $counts[$method]++;
                    $total++;
                }
            }
        }

        // Return structure bahkan jika empty
        return [
            'series' => array_values($counts),
            'labels' => array_keys($counts),
            'percentages' => [
                'Cash' => $total ? round($counts['Cash'] / $total * 100, 1) : 0,
                'Credit' => $total ? round($counts['Credit'] / $total * 100, 1) : 0,
            ],
            'total' => $total
        ];
    }

    private function calculateStats(array $data, int $columnIndex): array
    {
        $counts = [];
        $total = 0;

        foreach ($data as $row) {
            if (isset($row[$columnIndex]) && !empty(trim($row[$columnIndex]))) {
                $value = trim($row[$columnIndex]);
                $counts[$value] = ($counts[$value] ?? 0) + 1;
                $total++;
            }
        }

        // Return structure bahkan jika empty
        if ($total === 0) {
            return [
                'series' => [],
                'labels' => [],
                'total' => 0
            ];
        }

        arsort($counts);
        $counts = array_slice($counts, 0, 10, true);

        return [
            'series' => array_values($counts),
            'labels' => array_keys($counts),
            'total' => $total
        ];
    }

    private function calculateStatusOverTime(array $data, int $statusColumnIndex, int $dateColumnIndex, ?string $startDate = null, ?string $endDate = null): array
    {
        $statusByDate = [];

        foreach ($data as $row) {
            if (!isset($row[$statusColumnIndex], $row[$dateColumnIndex])) continue;

            try {
                $date = Carbon::parse($row[$dateColumnIndex])->format('Y-m-d');
                $status = trim($row[$statusColumnIndex]);

                if ($status === '') continue;

                $statusByDate[$date][$status] = ($statusByDate[$date][$status] ?? 0) + 1;
            } catch (\Exception $e) {
                continue;
            }
        }

        // ➕ Ambil rentang tanggal dari startDate-endDate
        $categories = [];

        if ($startDate && $endDate) {
            $from = Carbon::parse($startDate);
            $to = Carbon::parse($endDate);
            while ($from->lte($to)) {
                $categories[] = $from->format('Y-m-d');
                $from->addDay();
            }
        } else {
            $categories = array_keys($statusByDate);
            sort($categories);
        }

        $statuses = [];

        foreach ($statusByDate as $dailyStatuses) {
            foreach (array_keys($dailyStatuses) as $status) {
                $statuses[] = $status;
            }
        }

        $statuses = array_unique($statuses);


        // Pastikan setidaknya ada satu status untuk ditampilkan
        if (empty($statusByDate)) {
            $statuses = ['No Data'];
        }

        // Bangun struktur series berdasarkan kategori (tanggal)
        $series = [];
        foreach ($statuses as $status) {
            $series[] = [
                'name' => $status,
                'data' => array_map(function ($date) use ($statusByDate, $status) {
                    return isset($statusByDate[$date][$status]) ? $statusByDate[$date][$status] : 0;
                }, $categories),
            ];
        }

        // format categories to 'd F Y' format
        $categories = array_map(function ($date) {
            return Carbon::parse($date)->format('d F Y');
        }, $categories);

        return [
            'series' => $series,
            'categories' => $categories,
        ];
    }

    private function calculateStatusSummary(array $data, int $statusColumnIndex): array
    {
        $statusCounts = [];

        foreach ($data as $row) {
            if (!isset($row[$statusColumnIndex])) continue;

            $status = trim($row[$statusColumnIndex]);
            if ($status === '') continue;

            $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
        }

        // Return structure bahkan jika empty
        if (empty($statusCounts)) {
            return [
                'series' => [],
                'categories' => [],
            ];
        }

        // Bangun struktur series bar chart
        $series = [[
            'name' => 'Jumlah',
            'data' => array_values($statusCounts),
        ]];

        $categories = array_keys($statusCounts);

        return [
            'series' => $series,
            'categories' => $categories,
        ];
    }
}
