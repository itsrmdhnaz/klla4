<?php

namespace App\Services;

use App\Models\Spreadsheet;
use App\Models\SpreadsheetAccessLog;
use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    private Client $client;
    private Sheets $service;
    private Spreadsheet $spreadsheet;

    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
        $this->initializeClient();
    }

    private function initializeClient(): void
    {
        $this->client = new Client();
        
        // Set credentials from secure storage
        $credentials = $this->spreadsheet->getCredentials();
        
        if (!$credentials) {
            throw new \Exception('Invalid or missing credentials for spreadsheet: ' . $this->spreadsheet->name);
        }
        
        $this->client->setAuthConfig($credentials);
        $this->client->addScope(Sheets::SPREADSHEETS_READONLY);
        
        $this->service = new Sheets($this->client);
    }

    /**
     * Read data from spreadsheet
     */
    public function readRange(string $sheetName, string $range, int $userId): array
    {
        try {
            $fullRange = "{$sheetName}!{$range}";
            
            $response = $this->service->spreadsheets_values->get(
                $this->spreadsheet->spreadsheet_id,
                $fullRange
            );
            
            $values = $response->getValues() ?? [];
            
            // Log successful access
            $this->logAccess($userId, 'read', [
                'sheet' => $sheetName,
                'range' => $range,
                'rows_returned' => count($values)
            ], $values);
            
            return $values;
            
        } catch (\Exception $e) {
            // Log failed access
            $this->logAccess($userId, 'read_failed', [
                'sheet' => $sheetName,
                'range' => $range,
                'error' => $e->getMessage()
            ]);
            
            Log::error('Google Sheets API Error', [
                'spreadsheet_id' => $this->spreadsheet->spreadsheet_id,
                'range' => $fullRange,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Get single cell value
     */
    public function getCellValue(string $sheetName, string $cell, int $userId): mixed
    {
        $data = $this->readRange($sheetName, $cell, $userId);
        return $data[0][0] ?? null;
    }

    /**
     * Log access for audit trail
     */
    private function logAccess(int $userId, string $action, array $requestData, array $responseData = null): void
    {
        SpreadsheetAccessLog::create([
            'user_id' => $userId,
            'spreadsheet_id' => $this->spreadsheet->id,
            'action' => $action,
            'request_data' => $requestData,
            'response_data' => $responseData ? ['row_count' => count($responseData)] : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    /**
     * Read data from spreadsheet with optional processor
     */
    public function readRangeWithProcessor(string $sheetName, string $range, int $userId, callable $processor = null): mixed
    {
        $data = $this->readRange($sheetName, $range, $userId);
        
        if ($processor && is_callable($processor)) {
            // Get all sheet data for context if needed
            $allData = $this->readRange($sheetName, 'A:Z', $userId);
            return $processor($data, $allData);
        }
        
        return $data;
    }

    /**
     * Get column data with built-in data type handling
     */
    public function getColumnData(string $sheetName, string $range, string $dataType, int $userId): mixed
    {
        return match($dataType) {
            'single' => $this->getCellValue($sheetName, $range, $userId),
            'range', 'array' => $this->readRange($sheetName, $range, $userId),
            default => null
        };
    }

    /**
     * Get multiple columns data in one request (batch)
     */
    public function getBatchData(string $sheetName, array $ranges, int $userId): array
    {
        try {
            $batchRanges = array_map(fn($range) => "{$sheetName}!{$range}", $ranges);
            
            $response = $this->service->spreadsheets_values->batchGet(
                $this->spreadsheet->spreadsheet_id,
                ['ranges' => $batchRanges]
            );
            
            $results = [];
            $valueRanges = $response->getValueRanges();
            
            foreach ($valueRanges as $index => $valueRange) {
                $results[$ranges[$index]] = $valueRange->getValues() ?? [];
            }
            
            // Log successful batch access
            $this->logAccess($userId, 'batch_read', [
                'sheet' => $sheetName,
                'ranges' => $ranges,
                'total_ranges' => count($ranges)
            ]);
            
            return $results;
            
        } catch (\Exception $e) {
            // Log failed access
            $this->logAccess($userId, 'batch_read_failed', [
                'sheet' => $sheetName,
                'ranges' => $ranges,
                'error' => $e->getMessage()
            ]);
            
            Log::error('Google Sheets Batch API Error', [
                'spreadsheet_id' => $this->spreadsheet->spreadsheet_id,
                'ranges' => $batchRanges,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
}
