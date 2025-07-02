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

            $response = Http::timeout(20)->get($this->scriptUrl, $params);

            if ($response->failed()) {
                Log::error('Failed fetching from Google Apps Script', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $data = $response->json();

            // Jika gagal parse JSON, pastikan return array kosong
            return is_array($data) ? $data : [];
        } catch (\Exception $e) {
            Log::error('Exception from Google Apps Script call', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }


    // METODE BARU: Satu request untuk semua data
    public function getAllAnalyticsData(?string $startDate = null, ?string $endDate = null, ?string $sales = null): array
    {
        // Ambil semua data A2:P dalam satu request
        $data = $this->getDataFromScript($startDate, $endDate, 'A2:P', $sales);

        // Preprocessing semua statistik sekaligus
        return [
            'payment_methods' => $this->calculatePaymentMethodStats($data, 5),
            'programs' => $this->calculateStats($data, 6),
            'models' => $this->calculateStats($data, 4),
            'status' => $this->calculateStatusSummary($data, 8),
            'available_sales' => $this->extractSalesFromData($data, $sales)
        ];
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
            if (isset($row[$columnIndex])) {
                $method = trim($row[$columnIndex]);
                if (in_array($method, ['Cash', 'Credit'])) {
                    $counts[$method]++;
                    $total++;
                }
            }
        }

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
            if (isset($row[$columnIndex])) {
                $value = trim($row[$columnIndex]);
                $counts[$value] = ($counts[$value] ?? 0) + 1;
                $total++;
            }
        }

        if ($total === 0) {
            return [
                'series' => [0],
                'labels' => ['No Data'],
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

        // ⛔ Jika tidak ada status, beri default "No Status" => 0
        if (empty($statusCounts)) {
            $statusCounts = ['No Status' => 0];
        }

        // Bangun struktur series bar chart
        $series = [[
            'name' => 'Jumlah',
            'data' => array_values($statusCounts),
        ]];

        $categories = array_keys($statusCounts); // Nama status sebagai kategori (label X)

        return [
            'series' => $series,
            'categories' => $categories,
        ];
    }
};
