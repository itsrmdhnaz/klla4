<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class LeadAnalyticsController extends Controller
{
    private GoogleSheetsService $sheetsService;

    public function __construct()
    {
        $this->sheetsService = new GoogleSheetsService();
    }

    /**
     * Get payment method data for pie chart 1
     */
    public function getPaymentMethodData(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            // Debug: Log received parameters
            Log::info('Payment Method Request Received', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'all_params' => $request->all()
            ]);
            
            $data = $this->sheetsService->getPaymentMethodStats($startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'debug' => [
                    'received_start_date' => $startDate,
                    'received_end_date' => $endDate
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Payment Method Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get program data for pie chart 2
     */
    public function getProgramData(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            $data = $this->sheetsService->getProgramStats($startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get model data for pie chart 3
     */
    public function getModelData(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            $data = $this->sheetsService->getModelStats($startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status data for line chart
     */
    public function getStatusData(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            $data = $this->sheetsService->getStatusOverTimeStats($startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
