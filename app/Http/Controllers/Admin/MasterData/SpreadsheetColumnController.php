<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Controllers\Controller;
use App\Models\SpreadsheetColumn;
use App\Models\SpreadsheetSheet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SpreadsheetColumnController extends Controller
{
    public function index(Request $request)
    {
        $sheets = SpreadsheetSheet::with('spreadsheet')->where('is_active', true)->get();
        return view('admin.master-data.spreadsheet-column.index', compact('sheets'));
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $columns = SpreadsheetColumn::with(['sheet.spreadsheet', 'sheet.branch'])
                ->select('spreadsheet_columns.*');

            // Filter by sheet if specified
            if ($request->has('sheet_id') && !empty($request->sheet_id)) {
                $columns->where('spreadsheet_sheet_id', $request->sheet_id);
            }

            return DataTables::of($columns)
                ->addIndexColumn()
                ->addColumn('spreadsheet_name', function($column) {
                    return '<strong>' . e($column->sheet->spreadsheet->name) . '</strong>';
                })
                ->addColumn('sheet_name', function($column) {
                    $branchName = $column->sheet->branch ? ' (' . $column->sheet->branch->branch_name . ')' : '';
                    return '<span class="badge badge-info text-black">' . e($column->sheet->sheet_name) . $branchName . '</span>';
                })
                ->addColumn('data_type_badge', function($column) {
                    $badgeClass = match($column->data_type) {
                        'single' => 'badge badge-primary',
                        'range' => 'badge badge-warning',
                        'array' => 'badge badge-success',
                        default => 'badge badge-secondary'
                    };
                    
                    return '<span class=" text-black' . $badgeClass . '">' . ucfirst($column->data_type) . '</span>';
                })
                ->addColumn('column_range_display', function($column) {
                    return '<code class="bg-gray-100 px-2 py-1 rounded">' . e($column->column_range) . '</code>';
                })
                ->addColumn('status', function($column) {
                    $statusClass = $column->is_active ? 'badge badge-success' : 'badge badge-danger';
                    $statusText = $column->is_active ? 'Aktif' : 'Tidak Aktif';
                    
                    return '<span class="' . $statusClass . '">' . $statusText . '</span>';
                })
                ->addColumn('action', function($column) {
                    return '
                        <div class="d-flex gap-1">
                            <button type="button" 
                                    class="btn-action btn-edit" 
                                    onclick="editColumn(\'' . e($column->id) . '\')"
                                    data-bs-toggle="tooltip" 
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                    class="btn-action btn-delete" 
                                    onclick="deleteColumn(\'' . e($column->id) . '\')"
                                    data-bs-toggle="tooltip" 
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->editColumn('column_name', function($column) {
                    return '<strong>' . e($column->column_name) . '</strong><br><small class="text-muted">' . e($column->column_key) . '</small>';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->get('search')['value'])) {
                        $search = $request->get('search')['value'];
                        $query->where(function($q) use ($search) {
                            $q->where('column_name', 'like', "%{$search}%")
                              ->orWhere('column_key', 'like', "%{$search}%")
                              ->orWhere('column_range', 'like', "%{$search}%")
                              ->orWhere('description', 'like', "%{$search}%")
                              ->orWhereHas('sheet', function($sq) use ($search) {
                                  $sq->where('sheet_name', 'like', "%{$search}%")
                                     ->orWhereHas('spreadsheet', function($sps) use ($search) {
                                         $sps->where('name', 'like', "%{$search}%");
                                     });
                              });
                        });
                    }
                })
                ->rawColumns(['spreadsheet_name', 'sheet_name', 'column_name', 'data_type_badge', 'column_range_display', 'status', 'action'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'spreadsheet_sheet_id' => 'required|exists:spreadsheet_sheets,id',
                'column_key' => 'required|string|max:255',
                'column_name' => 'required|string|max:255',
                'column_range' => 'required|string|max:50',
                'data_type' => 'required|in:single,range,array',
                'description' => 'nullable|string',
                'validation_rules' => 'nullable|array',
                'roles' => 'nullable|array',
                'roles.*' => 'exists:roles,id'
            ], [
                'spreadsheet_sheet_id.required' => 'Sheet wajib dipilih',
                'spreadsheet_sheet_id.exists' => 'Sheet tidak valid',
                'column_key.required' => 'Column key wajib diisi',
                'column_name.required' => 'Nama kolom wajib diisi',
                'column_range.required' => 'Range kolom wajib diisi',
                'data_type.required' => 'Tipe data wajib dipilih',
                'data_type.in' => 'Tipe data tidak valid'
            ]);

            // Check unique constraint manually
            $exists = SpreadsheetColumn::where('spreadsheet_sheet_id', $validated['spreadsheet_sheet_id'])
                ->where('column_key', $validated['column_key'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => ['column_key' => ['Column key sudah digunakan untuk sheet ini']]
                ], 422);
            }

            $column = SpreadsheetColumn::create($validated);

            // Permission auto-create and assign to roles
            $sheet = $column->sheet;
            $spreadsheet = $sheet->spreadsheet;
            $key = $column->column_key;
            $range = $column->column_range;
            $permissionName = "{$spreadsheet->id}.{$sheet->id}." . strtolower($key) . ".{$range}.read";

            $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permissionName]);
            if (!empty($validated['roles'])) {
                $roles = \Spatie\Permission\Models\Role::whereIn('id', $validated['roles'])->get();
                foreach ($roles as $role) {
                    $role->givePermissionTo($permission);
                }
                // assfin to admin role
                $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
                if ($adminRole) {
                    $adminRole->givePermissionTo($permission);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Kolom berhasil ditambahkan!',
                'data' => $column->load(['sheet.spreadsheet', 'sheet.branch'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Store column error:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $column = SpreadsheetColumn::with(['sheet.spreadsheet', 'sheet.branch'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $column
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kolom tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $column = SpreadsheetColumn::findOrFail($id);

            $validated = $request->validate([
                'spreadsheet_sheet_id' => 'required|exists:spreadsheet_sheets,id',
                'column_key' => 'required|string|max:255',
                'column_name' => 'required|string|max:255',
                'column_range' => 'required|string|max:50',
                'data_type' => 'required|in:single,range,array',
                'description' => 'nullable|string',
                'validation_rules' => 'nullable|array',
                'roles' => 'nullable|array',
                'roles.*' => 'exists:roles,id'
            ], [
                'spreadsheet_sheet_id.required' => 'Sheet wajib dipilih',
                'spreadsheet_sheet_id.exists' => 'Sheet tidak valid',
                'column_key.required' => 'Column key wajib diisi',
                'column_name.required' => 'Nama kolom wajib diisi',
                'column_range.required' => 'Range kolom wajib diisi',
                'data_type.required' => 'Tipe data wajib dipilih',
                'data_type.in' => 'Tipe data tidak valid'
            ]);

            // Check unique constraint manually (excluding current record)
            $exists = SpreadsheetColumn::where('spreadsheet_sheet_id', $validated['spreadsheet_sheet_id'])
                ->where('column_key', $validated['column_key'])
                ->where('id', '!=', $column->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => ['column_key' => ['Column key sudah digunakan untuk sheet ini']]
                ], 422);
            }

            // Simpan nama permission lama sebelum update
            $oldSheet = $column->sheet;
            $oldSpreadsheet = $oldSheet->spreadsheet;
            $oldKey = $column->column_key;
            $oldRange = $column->column_range;
            $oldPermissionName = "{$oldSpreadsheet->id}.{$oldSheet->id}." . strtolower($oldKey) . ".{$oldRange}.read";

            $column->update($validated);

            // Permission update logic
            $sheet = $column->sheet;
            $spreadsheet = $sheet->spreadsheet;
            $key = $column->column_key;
            $range = $column->column_range;
            $newPermissionName = "{$spreadsheet->id}.{$sheet->id}." . strtolower($key) . ".{$range}.read";

            if ($oldPermissionName !== $newPermissionName) {
                // Hapus permission lama jika tidak digunakan role lain
                $oldPermission = \Spatie\Permission\Models\Permission::where('name', $oldPermissionName)->first();
                if ($oldPermission) {
                    $oldPermission->roles()->detach();
                    $oldPermission->delete();
                }
            }

            // Buat permission baru jika belum ada
            $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $newPermissionName]);

            // Assign ke role yang dipilih
            if (!empty($validated['roles'])) {
                $roles = \Spatie\Permission\Models\Role::whereIn('id', $validated['roles'])->get();
                foreach ($roles as $role) {
                    $role->givePermissionTo($permission);
                }

                // assfin to admin role
                $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
                if ($adminRole) {
                    $adminRole->givePermissionTo($permission);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Kolom berhasil diupdate!',
                'data' => $column->load(['sheet.spreadsheet', 'sheet.branch'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Update column error:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $column = SpreadsheetColumn::findOrFail($id);
            
            // Check if column has user permissions
            $permissionsCount = $column->userColumnPermissions()->count();
            if ($permissionsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Kolom tidak dapat dihapus karena masih memiliki {$permissionsCount} permission. Hapus permission terlebih dahulu."
                ], 422);
            }
            
            $column->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kolom berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSheets()
    {
        try {
            $sheets = SpreadsheetSheet::with(['spreadsheet', 'branch'])
                ->where('is_active', true)
                ->get()
                ->map(function($sheet) {
                    $branchName = $sheet->branch ? ' (' . $sheet->branch->branch_name . ')' : '';
                    return [
                        'id' => $sheet->id,
                        'name' => $sheet->spreadsheet->name . ' - ' . $sheet->sheet_name . $branchName,
                        'spreadsheet_name' => $sheet->spreadsheet->name,
                        'sheet_name' => $sheet->sheet_name,
                        'branch_name' => $sheet->branch ? $sheet->branch->branch_name : null
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $sheets
            ]);
        } catch (\Exception $e) {
            \Log::error('Get sheets error:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data sheet: ' . $e->getMessage()
            ], 500);
        }
    }
}
