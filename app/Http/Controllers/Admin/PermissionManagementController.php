<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Spreadsheet;
use App\Models\SpreadsheetSheet;
use App\Models\SpreadsheetColumn;
use App\Models\UserSpreadsheetPermission;
use App\Models\UserSheetPermission;
use App\Models\UserColumnPermission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PermissionManagementController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email')->get();
        $spreadsheets = Spreadsheet::where('is_active', true)->get();
        
        return view('admin.permission-management.index', compact('users', 'spreadsheets'));
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = User::with(['spreadsheetPermissions.spreadsheet', 'employee'])
                ->select('users.*');

            // Filter by user if specified
            if ($request->has('user_id') && !empty($request->user_id)) {
                $query->where('id', $request->user_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('employee_info', function($user) {
                    if ($user->employee) {
                        return '<div class="text-sm">
                                    <strong>' . e($user->employee->nama_pegawai) . '</strong><br>
                                    <span class="text-gray-500">' . e($user->employee->nip) . '</span>
                                </div>';
                    }
                    return '<span class="text-gray-500">No Employee Data</span>';
                })
                ->addColumn('spreadsheet_count', function($user) {
                    $count = $user->spreadsheetPermissions()->count();
                    return '<span class="badge badge-info">' . $count . ' Spreadsheet</span>';
                })
                ->addColumn('sheet_count', function($user) {
                    $count = $user->sheetPermissions()->count();
                    return '<span class="badge badge-success">' . $count . ' Sheet</span>';
                })
                ->addColumn('column_count', function($user) {
                    $count = $user->columnPermissions()->count();
                    return '<span class="badge badge-warning">' . $count . ' Column</span>';
                })
                ->addColumn('action', function($user) {
                    return '
                        <div class="d-flex gap-1">
                            <button type="button" 
                                    class="btn-action btn-edit" 
                                    onclick="manageUserPermissions(' . $user->id . ')"
                                    data-bs-toggle="tooltip" 
                                    title="Manage Permissions">
                                <i class="fas fa-shield-alt"></i>
                            </button>
                            <button type="button" 
                                    class="btn-action btn-secondary" 
                                    onclick="viewUserPermissions(' . $user->id . ')"
                                    data-bs-toggle="tooltip" 
                                    title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    ';
                })
                ->editColumn('name', function($user) {
                    return '<div class="text-sm">
                                <strong>' . e($user->name) . '</strong><br>
                                <span class="text-gray-500">' . e($user->email) . '</span>
                            </div>';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->get('search')['value'])) {
                        $search = $request->get('search')['value'];
                        $query->where(function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%")
                              ->orWhereHas('employee', function($eq) use ($search) {
                                  $eq->where('nama_pegawai', 'like', "%{$search}%")
                                     ->orWhere('nip', 'like', "%{$search}%");
                              });
                        });
                    }
                })
                ->rawColumns(['name', 'employee_info', 'spreadsheet_count', 'sheet_count', 'column_count', 'action'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function getUserPermissions($userId)
    {
        try {
            $user = User::with([
                'spreadsheetPermissions.spreadsheet',
                'sheetPermissions.sheet.spreadsheet',
                'columnPermissions.column.sheet.spreadsheet'
            ])->findOrFail($userId);

            $permissions = [
                'spreadsheets' => $user->spreadsheetPermissions->map(function($perm) {
                    return [
                        'id' => $perm->id,
                        'spreadsheet_id' => $perm->spreadsheet_id,
                        'spreadsheet_name' => $perm->spreadsheet->name,
                        'permission_level' => $perm->permission_level
                    ];
                }),
                'sheets' => $user->sheetPermissions->map(function($perm) {
                    return [
                        'id' => $perm->id,
                        'sheet_id' => $perm->spreadsheet_sheet_id,
                        'sheet_name' => $perm->sheet->sheet_name,
                        'spreadsheet_name' => $perm->sheet->spreadsheet->name,
                        'permission_level' => $perm->permission_level
                    ];
                }),
                'columns' => $user->columnPermissions->map(function($perm) {
                    return [
                        'id' => $perm->id,
                        'column_id' => $perm->spreadsheet_column_id,
                        'column_name' => $perm->column->column_name,
                        'sheet_name' => $perm->column->sheet->sheet_name,
                        'spreadsheet_name' => $perm->column->sheet->spreadsheet->name,
                        'can_read' => $perm->can_read,
                        'can_write' => $perm->can_write
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'user' => $user,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }
    }

    public function assignSpreadsheetPermission(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'spreadsheet_id' => 'required|exists:spreadsheets,id',
                'permission_level' => 'required|in:read,write,admin'
            ]);

            $permission = UserSpreadsheetPermission::updateOrCreate(
                [
                    'user_id' => $validated['user_id'],
                    'spreadsheet_id' => $validated['spreadsheet_id']
                ],
                [
                    'permission_level' => $validated['permission_level']
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Permission spreadsheet berhasil di-assign!',
                'data' => $permission
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignSheetPermission(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'sheet_id' => 'required|exists:spreadsheet_sheets,id',
                'permission_level' => 'required|in:read,write'
            ]);

            $permission = UserSheetPermission::updateOrCreate(
                [
                    'user_id' => $validated['user_id'],
                    'spreadsheet_sheet_id' => $validated['sheet_id']
                ],
                [
                    'permission_level' => $validated['permission_level']
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Permission sheet berhasil di-assign!',
                'data' => $permission
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignColumnPermission(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'column_id' => 'required|exists:spreadsheet_columns,id',
                'can_read' => 'required|boolean',
                'can_write' => 'required|boolean'
            ]);

            $permission = UserColumnPermission::updateOrCreate(
                [
                    'user_id' => $validated['user_id'],
                    'spreadsheet_column_id' => $validated['column_id']
                ],
                [
                    'can_read' => $validated['can_read'],
                    'can_write' => $validated['can_write']
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Permission kolom berhasil di-assign!',
                'data' => $permission
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function revokePermission(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:spreadsheet,sheet,column',
                'permission_id' => 'required'
            ]);

            $deleted = match($validated['type']) {
                'spreadsheet' => UserSpreadsheetPermission::where('id', $validated['permission_id'])->delete(),
                'sheet' => UserSheetPermission::where('id', $validated['permission_id'])->delete(),
                'column' => UserColumnPermission::where('id', $validated['permission_id'])->delete(),
            };

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Permission berhasil dicabut!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSpreadsheetHierarchy()
    {
        try {
            $spreadsheets = Spreadsheet::with(['sheets.columns'])
                ->where('is_active', true)
                ->get()
                ->map(function($spreadsheet) {
                    return [
                        'id' => $spreadsheet->id,
                        'name' => $spreadsheet->name,
                        'sheets' => $spreadsheet->sheets->map(function($sheet) {
                            return [
                                'id' => $sheet->id,
                                'name' => $sheet->sheet_name,
                                'display_name' => $sheet->sheet_display_name,
                                'columns' => $sheet->columns->map(function($column) {
                                    return [
                                        'id' => $column->id,
                                        'name' => $column->column_name,
                                        'key' => $column->column_key,
                                        'range' => $column->column_range,
                                        'data_type' => $column->data_type
                                    ];
                                })
                            ];
                        })
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $spreadsheets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data hierarchy: ' . $e->getMessage()
            ], 500);
        }
    }
}
