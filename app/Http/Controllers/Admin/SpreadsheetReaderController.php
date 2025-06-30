<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Spreadsheet;
use App\Models\SpreadsheetSheet;
use App\Models\SpreadsheetColumn;
use Google\Client as GoogleClient;
use Google\Service\Sheets as GoogleSheets;

class SpreadsheetReaderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $spreadsheets = Spreadsheet::where('is_active', true)->get();

        // Ambil hanya branch yang dimiliki user (dari relasi employee->branches)
        $branches = collect();
        if ($user && $user->employee) {
            $branches = $user->employee->branches;
        }

        return view('admin.spreadsheet.reader', compact('spreadsheets', 'branches'));
    }

    public function getAccessibleSheets(Request $request, $spreadsheetId)
    {
        $branchId = $request->query('branch_id');
        $user = $request->user();

        if (!$branchId) {
            return response()->json(['success' => false, 'message' => 'Branch required'], 400);
        }

        // Ambil sheet berdasarkan spreadsheet dan branch yang dimiliki user
        $userBranchIds = $user->employee && $user->employee->branches
            ? $user->employee->branches->pluck('id_branch')->toArray()
            : [];

        if (!in_array($branchId, $userBranchIds)) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $sheets = \App\Models\SpreadsheetSheet::where('spreadsheet_id', $spreadsheetId)
            ->where('branch_id', $branchId)
            ->get(['id', 'sheet_name', 'sheet_display_name']);

        return response()->json(['success' => true, 'data' => $sheets]);
    }

    public function getAccessibleColumns(Request $request, $sheetId)
    {
        $user = $request->user();
        $sheet = \App\Models\SpreadsheetSheet::findOrFail($sheetId);
        $spreadsheetId = $sheet->spreadsheet_id;

        // Ambil semua column config dari sheet
        $columns = $sheet->columns()->get();

        // Filter hanya yang bisa diakses user (menggunakan struktur permission terbaru)
        $accessible = [];
        foreach ($columns as $col) {
            $range = $col->column_range;
            $permissionName = "{$spreadsheetId}.{$sheetId}." . strtolower($col->column_key) . ".{$range}.read";
            if ($user->can($permissionName)) {
                $accessible[] = [
                    'id' => $col->id,
                    'name' => $col->column_name,
                    'range' => $col->column_range,
                    'key' => $col->column_key,
                ];
            }
        }
        return response()->json(['success' => true, 'data' => $accessible]);
    }

    public function readData(Request $request)
    {
        $validated = $request->validate([
            'spreadsheet_id' => 'required|exists:spreadsheets,id',
            'branch_id' => 'required|exists:branches,id_branch',
            'sheet_id' => 'required|exists:spreadsheet_sheets,id',
            'column_key' => 'required|string'
        ]);

        $spreadsheet = \App\Models\Spreadsheet::findOrFail($validated['spreadsheet_id']);
        $sheet = \App\Models\SpreadsheetSheet::findOrFail($validated['sheet_id']);
        $column = \App\Models\SpreadsheetColumn::where('spreadsheet_sheet_id', $sheet->id)
            ->where('column_key', $validated['column_key'])
            ->firstOrFail();

        // Load Google credentials
        $credentialsPath = $spreadsheet->credentials_path;
        if (!$credentialsPath || !\Storage::disk('credentials')->exists($credentialsPath)) {
            return response()->json(['success' => false, 'message' => 'Spreadsheet credentials not found'], 400);
        }
        $credentials = \Storage::disk('credentials')->get($credentialsPath);

        // Setup Google Client
        $client = new \Google\Client();
        $client->setAuthConfig(json_decode($credentials, true));
        $client->setScopes([\Google\Service\Sheets::SPREADSHEETS_READONLY]);
        $service = new \Google\Service\Sheets($client);

        $spreadsheetId = $spreadsheet->spreadsheet_id;
        $sheetName = $sheet->sheet_name;
        $range = "{$sheetName}!{$column->column_range}";

        try {
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to read data: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'column' => [
                    'id' => $column->id,
                    'name' => $column->column_name,
                    'range' => $column->column_range,
                    'key' => $column->column_key,
                ],
                'values' => $values ?? [],
            ]
        ]);
    }

    public function readBatchData(Request $request)
    {
        $validated = $request->validate([
            'spreadsheet_id' => 'required|exists:spreadsheets,id',
            'branch_id' => 'required|exists:branches,id_branch',
            'sheet_id' => 'required|exists:spreadsheet_sheets,id',
            'columns' => 'required|array',
            'columns.*' => 'string'
        ]);

        $spreadsheet = \App\Models\Spreadsheet::findOrFail($validated['spreadsheet_id']);
        $sheet = \App\Models\SpreadsheetSheet::findOrFail($validated['sheet_id']);
        $columns = \App\Models\SpreadsheetColumn::where('spreadsheet_sheet_id', $sheet->id)
            ->whereIn('column_key', $validated['columns'])
            ->get();

        // Load Google credentials
        $credentialsPath = $spreadsheet->credentials_path;
        if (!$credentialsPath || !\Storage::disk('credentials')->exists($credentialsPath)) {
            return response()->json(['success' => false, 'message' => 'Spreadsheet credentials not found'], 400);
        }
        $credentials = \Storage::disk('credentials')->get($credentialsPath);

        // Setup Google Client
        $client = new GoogleClient();
        $client->setAuthConfig(json_decode($credentials, true));
        $client->setScopes([GoogleSheets::SPREADSHEETS_READONLY]);
        $service = new GoogleSheets($client);

        $spreadsheetId = $spreadsheet->spreadsheet_id;
        $sheetName = $sheet->sheet_name;

        $result = [];
        foreach ($columns as $col) {
            $range = "{$sheetName}!{$col->column_range}";
            try {
                $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                $result[$col->column_key] = $response->getValues();
            } catch (\Exception $e) {
                $result[$col->column_key] = ['error' => $e->getMessage()];
            }
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function preview(Request $request)
    {
        // Implement preview logic
        return response()->json(['success' => true, 'data' => []]);
    }
}