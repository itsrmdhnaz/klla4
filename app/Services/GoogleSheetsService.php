<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoogleSheetsService
{
    private Client $client;
    private Sheets $service;
    private string $spreadsheetId;

    public function __construct()
    {
        $this->spreadsheetId = config('services.google_sheets.spreadsheet_id');
        $this->initializeClient();
    }

    private function initializeClient(): void
    {
        $this->client = new Client();

        // Get credentials from storage/app/private/
        $credentialsPath = storage_path('app/private/' . config('services.google_sheets.credentials_file'));

        if (!file_exists($credentialsPath)) {
            throw new \Exception('Google Sheets credentials file not found at: ' . $credentialsPath);
        }

        $this->client->setAuthConfig($credentialsPath);
        $this->client->addScope(Sheets::SPREADSHEETS_READONLY);
        $this->service = new Sheets($this->client);
    }

    /**
     * Read data from spreadsheet
     */
    public function readRange(string $sheetName, string $range): array
    {
        try {
            $fullRange = "{$sheetName}!{$range}";

            $response = $this->service->spreadsheets_values->get(
                $this->spreadsheetId,
                $fullRange
            );

            $values = $response->getValues() ?? [];

            return $values;
        } catch (\Exception $e) {
            Log::error('Google Sheets API Error', [
                'spreadsheet_id' => $this->spreadsheetId,
                'range' => $fullRange,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Get payment method statistics with date filtering
     * 
     * Mengambil data dari Column F (Payment Method) dan filter berdasarkan Column A (Timestamp)
     * Range: A2:F1000 (sampai row 1000, bisa disesuaikan jika data lebih banyak)
     */
    public function getPaymentMethodStats(?string $startDate = null, ?string $endDate = null): array
    {
        // Ambil data dari Column A sampai F (A=timestamp, F=payment method)
        $data = $this->readRange('Inputan RAW Data Lead', 'A2:F1000');

        // Filter berdasarkan tanggal di Column A (index 0)
        $filteredData = $this->filterDataByDate($data, $startDate, $endDate, 0);

        // Analisis Payment Method dari Column F (index 5)
        return $this->calculatePaymentMethodStats($filteredData, 5);
    }

    /**
     * Get program statistics with date filtering
     * 
     * Mengambil data dari Column G (Program) dan filter berdasarkan Column A (Timestamp)
     */
    public function getProgramStats(?string $startDate = null, ?string $endDate = null): array
    {
        // Ambil data dari Column A sampai G (A=timestamp, G=program)
        $data = $this->readRange('Inputan RAW Data Lead', 'A2:G1000');

        // Filter berdasarkan tanggal di Column A (index 0)
        $filteredData = $this->filterDataByDate($data, $startDate, $endDate, 0);

        // Analisis Program dari Column G (index 6)
        return $this->calculateStats($filteredData, 6);
    }

    /**
     * Get model statistics with date filtering
     * 
     * Mengambil data dari Column E (Model) dan filter berdasarkan Column A (Timestamp)
     */
    public function getModelStats(?string $startDate = null, ?string $endDate = null): array
    {
        // Ambil data dari Column A sampai E (A=timestamp, E=model)
        $data = $this->readRange('Inputan RAW Data Lead', 'A2:E1000');

        // Filter berdasarkan tanggal di Column A (index 0)
        $filteredData = $this->filterDataByDate($data, $startDate, $endDate, 0);

        // Analisis Model dari Column E (index 4)
        return $this->calculateStats($filteredData, 4);
    }

    /**
     * Get status over time statistics
     * 
     * Mengambil data dari Column I (Status) dan Column A (Timestamp)
     * Mengelompokkan status berdasarkan tanggal untuk line chart
     */
    public function getStatusOverTimeStats(?string $startDate = null, ?string $endDate = null): array
    {
        // Ambil data dari Column A sampai I (A=timestamp, I=status)
        $data = $this->readRange('Inputan RAW Data Lead', 'A2:I1000');

        // Filter berdasarkan tanggal di Column A (index 0)
        $filteredData = $this->filterDataByDate($data, $startDate, $endDate, 0);

        // Analisis Status over time (status=index 8, date=index 0)
        return $this->calculateStatusOverTime($filteredData, 8, 0);
    }

    /**
     * Filter data by date range
     * 
     * Cara kerja filtering:
     * 1. Ambil semua data sekaligus dari Google Sheets (1 API call)
     * 2. Filter berdasarkan tanggal di Column A (index 0)
     * 3. Hanya data yang masuk dalam range tanggal yang diproses
     * 
     * Format tanggal dari Google Sheets: 23/03/2025 0:28:10 (DD/MM/YYYY HH:MM:SS)
     * Harus diparse dengan benar!
     */
    private function filterDataByDate(array $data, ?string $startDate, ?string $endDate, int $dateColumnIndex): array
    {
        // Jika tidak ada filter tanggal, kembalikan semua data
        if (!$startDate || !$endDate) {
            return $data;
        }

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Filter setiap row berdasarkan tanggal di column A
        return array_filter($data, function ($row) use ($start, $end, $dateColumnIndex) {
            // Pastikan column tanggal ada dan tidak kosong

            if (!isset($row[$dateColumnIndex]) || empty($row[$dateColumnIndex])) {
                return false; // Skip row if date is not set or empty
            }

            // Parse tanggal dari format Google Sheets: 23/03/2025 0:28:10
            $dateString = trim($row[$dateColumnIndex]);

            $dateString = explode(" ", $dateString)[0]; // m/d/y
            // change to d/m/Y
            $day = explode("/", $dateString)[1];
            $month = explode("/", $dateString)[0];
            $year = "20" . explode("/", $dateString)[2];
            $dateString = "{$day}/{$month}/{$year}";

            $rowDate = Carbon::createFromFormat('d/m/Y', $dateString); // Hanya ambil tanggal, abaikan waktu

            // Jika parsing gagal, coba format lain
            if (!$rowDate) {
                // Coba format DD/MM/YYYY saja
                $rowDate = Carbon::createFromFormat('d/m/Y', $dateString);
            }

            // Jika masih gagal, coba parsing otomatis
            if (!$rowDate) {
                $rowDate = Carbon::parse($dateString);
            }

            return $rowDate->between($start, $end);
        });
    }

    /**
     * Calculate payment method statistics
     */
    private function calculatePaymentMethodStats(array $data, int $columnIndex): array
    {
        $counts = ['Cash' => 0, 'Credit' => 0];
        $total = 0;

        foreach ($data as $row) {
            if (isset($row[$columnIndex]) && !empty($row[$columnIndex])) {
                $method = trim($row[$columnIndex]);
                if (in_array($method, ['Cash', 'Credit'])) {
                    $counts[$method]++;
                    $total++;
                }
            }
        }

        // Jika tidak ada data, return default values
        if ($total === 0) {
            return [
                'series' => [0, 0],
                'labels' => ['Cash', 'Credit'],
                'percentages' => ['Cash' => 0, 'Credit' => 0],
                'total' => 0
            ];
        }

        $percentages = [];
        foreach ($counts as $method => $count) {
            $percentages[$method] = $total > 0 ? round(($count / $total) * 100, 1) : 0;
        }

        return [
            'series' => array_values($counts),
            'labels' => array_keys($counts),
            'percentages' => $percentages,
            'total' => $total
        ];
    }

    /**
     * Calculate general statistics
     */
    private function calculateStats(array $data, int $columnIndex): array
    {
        $counts = [];
        $total = 0;

        foreach ($data as $row) {
            if (isset($row[$columnIndex]) && !empty($row[$columnIndex])) {
                $value = trim($row[$columnIndex]);
                $counts[$value] = ($counts[$value] ?? 0) + 1;
                $total++;
            }
        }

        // Jika tidak ada data, return default values berdasarkan column
        if ($total === 0) {
            // Default values untuk program
            if ($columnIndex === 6) { // Column G = Program
                return [
                    'series' => [0],
                    'labels' => ['No Program'],
                    'total' => 0
                ];
            }
            // Default values untuk model
            if ($columnIndex === 4) { // Column E = Model
                return [
                    'series' => [0],
                    'labels' => ['No Model'],
                    'total' => 0
                ];
            }

            // Default generic
            return [
                'series' => [0],
                'labels' => ['No Data'],
                'total' => 0
            ];
        }

        // Sort by count descending
        arsort($counts);

        // Limit to top 10 to avoid cluttered charts
        $counts = array_slice($counts, 0, 10, true);

        return [
            'series' => array_values($counts),
            'labels' => array_keys($counts),
            'total' => $total
        ];
    }

    /**
     * Calculate status over time for line chart
     */
    private function calculateStatusOverTime(array $data, int $statusColumnIndex, int $dateColumnIndex): array
    {
        $statusByDate = [];

        foreach ($data as $row) {
            if (
                isset($row[$dateColumnIndex]) && isset($row[$statusColumnIndex])
                && !empty($row[$dateColumnIndex]) && !empty($row[$statusColumnIndex])
            ) {

                try {
                    // Parse tanggal dari format Google Sheets: 23/03/2025 0:28:10
                    $dateString = trim($row[$dateColumnIndex]);

                    $dateString = trim($row[$dateColumnIndex]);

                    $dateString = explode(" ", $dateString)[0]; // m/d/y
                    // change to d/m/Y
                    $day = explode("/", $dateString)[1];
                    $month = explode("/", $dateString)[0];
                    $year = "20" . explode("/", $dateString)[2];
                    $dateString = "{$day}/{$month}/{$year}";

                    $parsedDate = Carbon::createFromFormat('d/m/Y', $dateString); // Hanya ambil tanggal, abaikan waktu
                  
                    // Jika parsing gagal, coba format lain
                    if (!$parsedDate) {
                        // Coba format DD/MM/YYYY saja
                        $parsedDate = Carbon::createFromFormat('d/m/Y', $dateString);
                    }

                    // Jika masih gagal, coba parsing otomatis
                    if (!$parsedDate) {
                        $parsedDate = Carbon::parse($dateString);
                    }

                    $date = $parsedDate->format('Y-m-d');
                    $status = trim($row[$statusColumnIndex]);

                    if (!isset($statusByDate[$date])) {
                        $statusByDate[$date] = [];
                    }

                    $statusByDate[$date][$status] = ($statusByDate[$date][$status] ?? 0) + 1;
                } catch (\Exception $e) {
                    Log::warning('Date parsing failed in status calculation', [
                        'date_string' => $row[$dateColumnIndex] ?? 'null',
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }
        }

        // Jika tidak ada data, return default values
        if (empty($statusByDate)) {
            return [
                'series' => [
                    [
                        'name' => 'Suspect',
                        'data' => [0]
                    ],
                    [
                        'name' => 'Hot Prospect',
                        'data' => [0]
                    ]
                ],
                'categories' => [date('Y-m-d')]
            ];
        }

        // Sort by date
        ksort($statusByDate);

        // Get all unique statuses
        $allStatuses = [];
        foreach ($statusByDate as $statuses) {
            $allStatuses = array_merge($allStatuses, array_keys($statuses));
        }
        $allStatuses = array_unique($allStatuses);

        // Prepare data for line chart
        $categories = array_keys($statusByDate);
        $series = [];

        foreach ($allStatuses as $status) {
            $statusData = [];
            foreach ($categories as $date) {
                $statusData[] = $statusByDate[$date][$status] ?? 0;
            }
            $series[] = [
                'name' => $status,
                'data' => $statusData
            ];
        }

        return [
            'series' => $series,
            'categories' => $categories
        ];
    }

    /**
     * Debug: Get all sheet names in the spreadsheet
     */
    public function getSheetNames(): array
    {
        try {
            $spreadsheet = $this->service->spreadsheets->get($this->spreadsheetId);
            $sheets = $spreadsheet->getSheets();

            $sheetNames = [];
            foreach ($sheets as $sheet) {
                $sheetNames[] = $sheet->getProperties()->getTitle();
            }

            Log::info('Available sheets:', $sheetNames);
            return $sheetNames;
        } catch (\Exception $e) {
            Log::error('Error getting sheet names', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
