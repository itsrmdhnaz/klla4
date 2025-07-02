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
            $sales = $request->input('sales'); // Tambah parameter sales

            // Debug: Log received parameters
            Log::info('Payment Method Request Received', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'sales' => $sales,
                'all_params' => $request->all()
            ]);

            $data = $this->sheetsService->getPaymentMethodStats($startDate, $endDate, $sales);

            return response()->json([
                'success' => true,
                'data' => $data,
                'debug' => [
                    'received_start_date' => $startDate,
                    'received_end_date' => $endDate,
                    'received_sales' => $sales
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
            $sales = $request->input('sales'); // Tambah parameter sales

            $data = $this->sheetsService->getProgramStats($startDate, $endDate, $sales);

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
            $sales = $request->input('sales'); // Tambah parameter sales

            $data = $this->sheetsService->getModelStats($startDate, $endDate, $sales);

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
            $sales = $request->input('sales'); // Tambah parameter sales

            $data = $this->sheetsService->getStatusOverTimeStats($startDate, $endDate, $sales);

            // Tambah sales data ke response jika tidak ada sales filter
            if (!$sales || $sales === '') {
                $salesList = $this->sheetsService->extractSalesFromStatusData($startDate, $endDate);
                $data['available_sales'] = $salesList;
            }

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

    /**
     * Get sales data for select options
     */
    public function getSalesData(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            Log::info('Sales Data Request Received', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            $data = $this->sheetsService->getSalesData($startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Sales Data Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ALL analytics data in one request - NEW UNIFIED ENDPOINT
     */
    public function getAllAnalyticsData(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $sales = $request->input('sales');

            Log::info('Unified Analytics Request Received', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'sales' => $sales
            ]);

            $data = $this->sheetsService->getAllAnalyticsData($startDate, $endDate, $sales);

            return response()->json([
                'success' => true,
                'data' => $data,
                'debug' => [
                    'received_start_date' => $startDate,
                    'received_end_date' => $endDate,
                    'received_sales' => $sales
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Unified Analytics Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
