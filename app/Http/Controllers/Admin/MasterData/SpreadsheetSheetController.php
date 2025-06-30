<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Controllers\Controller;
use App\Models\SpreadsheetSheet;
use App\Models\Spreadsheet;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SpreadsheetSheetController extends Controller
{
    public function index(Request $request)
    {
        $spreadsheets = Spreadsheet::where('is_active', true)->get();
        return view('admin.master-data.spreadsheet-sheet.index', compact('spreadsheets'));
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $sheets = SpreadsheetSheet::with(['spreadsheet', 'branch'])
                ->select('spreadsheet_sheets.*');

            // Filter by spreadsheet if specified
            if ($request->has('spreadsheet_id') && !empty($request->spreadsheet_id)) {
                $sheets->where('spreadsheet_id', $request->spreadsheet_id);
            }

            return DataTables::of($sheets)
                ->addIndexColumn()
                ->addColumn('spreadsheet_name', function($sheet) {
                    return '<strong>' . e($sheet->spreadsheet->name) . '</strong>';
                })
                ->addColumn('branch_name', function($sheet) {
                    return $sheet->branch ? 
                        '<span class="badge badge-primary">' . e($sheet->branch->branch_name) . '</span>' : 
                        '<span class="badge badge-secondary">No Branch</span>';
                })
                ->addColumn('status', function($sheet) {
                    $statusClass = $sheet->is_active ? 'badge badge-success' : 'badge badge-danger';
                    $statusText = $sheet->is_active ? 'Aktif' : 'Tidak Aktif';
                    
                    return '<span class="' . $statusClass . '">' . $statusText . '</span>';
                })
                ->addColumn('columns_count', function($sheet) {
                    $count = $sheet->columns()->count();
                    return '<span class="badge badge-info text-black">' . $count . ' Kolom</span>';
                })
                ->addColumn('action', function($sheet) {
                    return '
                        <div class="d-flex gap-1">
                            <button type="button" 
                                    class="btn-action btn-edit" 
                                    onclick="editSheet(\'' . e($sheet->id) . '\')"
                                    data-bs-toggle="tooltip" 
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                    class="btn-action btn-delete" 
                                    onclick="deleteSheet(\'' . e($sheet->id) . '\')"
                                    data-bs-toggle="tooltip" 
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->editColumn('sheet_name', function($sheet) {
                    $displayName = $sheet->sheet_display_name ?: $sheet->sheet_name;
                    return '<strong>' . e($sheet->sheet_name) . '</strong>' . 
                           ($sheet->sheet_display_name ? '<br><small class="text-muted">' . e($displayName) . '</small>' : '');
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->get('search')['value'])) {
                        $search = $request->get('search')['value'];
                        $query->where(function($q) use ($search) {
                            $q->where('sheet_name', 'like', "%{$search}%")
                              ->orWhere('sheet_display_name', 'like', "%{$search}%")
                              ->orWhere('description', 'like', "%{$search}%")
                              ->orWhereHas('spreadsheet', function($sq) use ($search) {
                                  $sq->where('name', 'like', "%{$search}%");
                              })
                              ->orWhereHas('branch', function($bq) use ($search) {
                                  $bq->where('branch_name', 'like', "%{$search}%");
                              });
                        });
                    }
                })
                ->rawColumns(['spreadsheet_name', 'sheet_name', 'branch_name', 'status', 'columns_count', 'action'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'spreadsheet_id' => 'required|exists:spreadsheets,id',
                'branch_id' => 'nullable|exists:branches,id_branch',
                'sheet_name' => 'required|string|max:255',
                'sheet_display_name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
            ], [
                'spreadsheet_id.required' => 'Spreadsheet wajib dipilih',
                'spreadsheet_id.exists' => 'Spreadsheet tidak valid',
                'branch_id.exists' => 'Cabang tidak valid',
                'sheet_name.required' => 'Nama sheet wajib diisi',
                'sheet_name.max' => 'Nama sheet maksimal 255 karakter'
            ]);

            // Check unique constraint manually
            $exists = SpreadsheetSheet::where('spreadsheet_id', $validated['spreadsheet_id'])
                ->where('sheet_name', $validated['sheet_name'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => ['sheet_name' => ['Nama sheet sudah digunakan untuk spreadsheet ini']]
                ], 422);
            }

            $sheet = SpreadsheetSheet::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Sheet berhasil ditambahkan!',
                'data' => $sheet->load(['spreadsheet', 'branch'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Store sheet error:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $sheet = SpreadsheetSheet::with(['spreadsheet', 'branch'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $sheet
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sheet tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $sheet = SpreadsheetSheet::findOrFail($id);

            $validated = $request->validate([
                'spreadsheet_id' => 'required|exists:spreadsheets,id',
                'branch_id' => 'nullable|exists:branches,id_branch',
                'sheet_name' => 'required|string|max:255',
                'sheet_display_name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
            ], [
                'spreadsheet_id.required' => 'Spreadsheet wajib dipilih',
                'spreadsheet_id.exists' => 'Spreadsheet tidak valid',
                'branch_id.exists' => 'Cabang tidak valid',
                'sheet_name.required' => 'Nama sheet wajib diisi',
                'sheet_name.max' => 'Nama sheet maksimal 255 karakter'
            ]);

            // Check unique constraint manually (excluding current record)
            $exists = SpreadsheetSheet::where('spreadsheet_id', $validated['spreadsheet_id'])
                ->where('sheet_name', $validated['sheet_name'])
                ->where('id', '!=', $sheet->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => ['sheet_name' => ['Nama sheet sudah digunakan untuk spreadsheet ini']]
                ], 422);
            }

            $sheet->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Sheet berhasil diupdate!',
                'data' => $sheet->load(['spreadsheet', 'branch'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Update sheet error:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $sheet = SpreadsheetSheet::findOrFail($id);
            
            // Check if sheet has columns
            $columnsCount = $sheet->columns()->count();
            if ($columnsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Sheet tidak dapat dihapus karena masih memiliki {$columnsCount} kolom. Hapus kolom terlebih dahulu."
                ], 422);
            }
            
            $sheet->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sheet berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSpreadsheets()
    {
        try {
            $spreadsheets = Spreadsheet::where('is_active', true)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
            
            \Log::info('Spreadsheets found:', ['count' => $spreadsheets->count(), 'data' => $spreadsheets->toArray()]);
            
            return response()->json([
                'success' => true,
                'data' => $spreadsheets
            ]);
        } catch (\Exception $e) {
            \Log::error('Get spreadsheets error:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data spreadsheet: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBranches()
    {
        try {
            $branches = Branch::select('id_branch', 'branch_name')
                ->orderBy('branch_name')
                ->get();
            
            \Log::info('Branches found:', ['count' => $branches->count(), 'data' => $branches->toArray()]);
            
            return response()->json([
                'success' => true,
                'data' => $branches
            ]);
        } catch (\Exception $e) {
            \Log::error('Get branches error:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data cabang: ' . $e->getMessage()
            ], 500);
        }
    }
}
