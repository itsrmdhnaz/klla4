<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DebugController extends Controller
{
    private GoogleSheetsService $sheetsService;

    public function __construct()
    {
        $this->sheetsService = new GoogleSheetsService();
    }

    /**
     * Debug: Show raw data from Google Sheets
     */
    public function showRawData(Request $request): JsonResponse
    {
        try {
            // Ambil 10 row pertama untuk debugging
            $data = $this->sheetsService->readRange('Inputan RAW Data Lead', 'A2:I11');
            
            return response()->json([
                'success' => true,
                'message' => 'Raw data from Google Sheets (first 10 rows)',
                'data' => $data,
                'format_analysis' => $this->analyzeDateFormats($data)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function analyzeDateFormats(array $data): array
    {
        $analysis = [];
        
        foreach ($data as $index => $row) {
            if (isset($row[0]) && !empty($row[0])) {
                $analysis[] = [
                    'row' => $index + 2, // +2 karena mulai dari A2
                    'original' => $row[0],
                    'length' => strlen($row[0]),
                    'type' => gettype($row[0])
                ];
            }
        }
        
        return $analysis;
    }
}
