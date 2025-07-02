<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoogleSheetsService
{
    private string $scriptUrl;

    public function __construct()
    {
        $this->scriptUrl = config('services.google_sheets.script_url');
    }

    private function getDataFromScript(?string $startDate, ?string $endDate, string $range): array
    {
        try {
            $response = Http::timeout(20)->get($this->scriptUrl, [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'range' => $range,
            ]);

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


    public function getPaymentMethodStats(?string $startDate = null, ?string $endDate = null): array
    {
        $data = $this->getDataFromScript($startDate, $endDate, 'A2:F');
        return $this->calculatePaymentMethodStats($data, 5);
    }

    public function getProgramStats(?string $startDate = null, ?string $endDate = null): array
    {
        $data = $this->getDataFromScript($startDate, $endDate, 'A2:G');
        return $this->calculateStats($data, 6);
    }

    public function getModelStats(?string $startDate = null, ?string $endDate = null): array
    {
        $data = $this->getDataFromScript($startDate, $endDate, 'A2:E');
        return $this->calculateStats($data, 4);
    }

    public function getStatusOverTimeStats(?string $startDate = null, ?string $endDate = null): array
    {
        $data = $this->getDataFromScript($startDate, $endDate, 'A2:I');
        return $this->calculateStatusOverTime($data, 8, 0, $startDate, $endDate);
    }

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
}
