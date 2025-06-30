<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use App\Models\Spreadsheet;
use App\Models\SpreadsheetSheet;
use App\Models\SpreadsheetColumn;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RolePermissionController extends Controller
{
    public function index()
    {
        $users = User::all();
        $roles = Role::all();
        $branches = Branch::all();
        
        // Get available teams from sheets
        $availableTeams = $this->getAvailableTeams();
        
        return view('admin.system.roles-permissions', compact('users', 'roles', 'branches', 'availableTeams'));
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = User::with(['roles', 'employee']);

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
                ->addColumn('roles_display', function($user) {
                    if ($user->roles->count() > 0) {
                        return $user->roles->map(function($role) {
                            $badgeClass = $this->getRoleBadgeClass($role->name);
                            $teamInfo = $role->team_id ? " (Team)" : '';
                            return '<span class="badge ' . $badgeClass . '">' . e($role->name) . $teamInfo . '</span>';
                        })->implode(' ');
                    }
                    return '<span class="text-gray-500">No Roles</span>';
                })
                ->addColumn('permissions_count', function($user) {
                    $allPermissions = $user->getAllPermissions();
                    $directPermissions = $user->permissions->count();
                    $rolePermissions = $user->getPermissionsViaRoles()->count();
                    
                    return '<div class="text-sm">
                                <span class="badge badge-info">' . $allPermissions->count() . ' Total</span><br>
                                <small class="text-gray-500">' . $directPermissions . ' Direct + ' . $rolePermissions . ' via Roles</small>
                            </div>';
                })
                ->addColumn('team_access', function($user) {
                    $teamRoles = $user->roles()->whereNotNull('roles.team_id')->get();
                    
                    if ($teamRoles->count() > 0) {
                        return $teamRoles->map(function($role) {
                            return '<span class="badge badge-secondary">' . e($role->team_id) . '</span>';
                        })->implode(' ');
                    }
                    return '<span class="text-gray-500">No Team Access</span>';
                })
                ->addColumn('action', function($user) {
                    return '
                        <div class="d-flex gap-1">
                            <button type="button" 
                                    class="btn-action btn-edit" 
                                    onclick="manageUserRoles(' . $user->id . ')"
                                    data-bs-toggle="tooltip" 
                                    title="Manage Roles">
                                <i class="fas fa-user-shield"></i>
                            </button>
                            <button type="button" 
                                    class="btn-action btn-secondary" 
                                    onclick="viewUserPermissions(' . $user->id . ')"
                                    data-bs-toggle="tooltip" 
                                    title="View Permissions">
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
                ->rawColumns(['name', 'employee_info', 'roles_display', 'permissions_count', 'team_access', 'action'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    private function getAvailableTeams()
    {
        $sheets = SpreadsheetSheet::with('spreadsheet')->where('is_active', true)->get();
        
        return $sheets->map(function($sheet) {
            return [
                'id' => $sheet->sheet_name, // Use sheet name as team ID
                'name' => $sheet->sheet_display_name ?: $sheet->sheet_name,
                'spreadsheet' => $sheet->spreadsheet->name,
                'description' => "Team for {$sheet->sheet_name} sheet"
            ];
        })->toArray();
    }

    public function assignRole(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'role_id' => 'required|exists:roles,id'
            ]);

            $user = User::findOrFail($validated['user_id']);
            $role = Role::findOrFail($validated['role_id']);
            
            $user->assignRole($role);

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil di-assign ke user!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignUserToTeam(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'team_name' => 'required|string'
            ]);

            $user = User::findOrFail($validated['user_id']);
            $teamName = $validated['team_name'];
            
            // Create or find team role
            $roleName = "team-{$teamName}";
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'team_id' => $teamName,
                'guard_name' => 'web'
            ]);
            
            // Create default permissions for this team
            $this->createTeamPermissions($teamName, $role);
            
            $user->assignRole($role);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil di-assign ke team!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createRole(Request $request)
    {
        try {
            $validated = $request->validate([
                'role_name' => 'required|string|max:255|unique:roles,name',
                'permissions' => 'nullable|array',
                'permissions.*' => 'string'
            ]);

            $role = Role::create([
                'name' => $validated['role_name'],
                'guard_name' => 'web'
            ]);

            // Assign permissions if provided
            if (!empty($validated['permissions'])) {
                foreach ($validated['permissions'] as $permissionName) {
                    Permission::firstOrCreate(['name' => $permissionName]);
                }
                $role->givePermissionTo($validated['permissions']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil dibuat!',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions_count' => $role->permissions->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createTeamRole(Request $request)
    {
        try {
            $validated = $request->validate([
                'team_name' => 'required|string|max:255',
                'role_name' => 'required|string|max:255|unique:roles,name',
                'custom_permissions' => 'nullable|string'
            ]);

            $teamName = $validated['team_name'];
            $roleName = $validated['role_name'];
            $customPermissions = $validated['custom_permissions'] ? json_decode($validated['custom_permissions'], true) : [];

            // team_id harus nullable/integer di DB, jadi simpan di kolom lain (misal: team_name atau gunakan json/meta jika perlu string)
            // Solusi: simpan team_name di kolom name saja, dan biarkan team_id null (atau gunakan kolom string baru jika ingin simpan team_name)
            $role = \Spatie\Permission\Models\Role::create([
                'name' => $roleName,
                // 'team_id' => $teamName, // HAPUS baris ini, karena team_id di DB integer
                'guard_name' => 'web'
            ]);

            // Assign permissions
            if (!empty($customPermissions)) {
                foreach ($customPermissions as $permissionName) {
                    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permissionName]);
                }
                $role->givePermissionTo($customPermissions);
            } else {
                // Default: team level permission
                \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $teamName]);
                $role->givePermissionTo([$teamName]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Team role created!',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions_count' => $role->permissions->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function createTeamPermissions($teamName, $role)
    {
        // Find sheet that matches team name
        $sheet = SpreadsheetSheet::where('sheet_name', $teamName)
                                ->orWhere('sheet_display_name', $teamName)
                                ->first();
        
        if (!$sheet) return;

        $permissions = [];
        
        // Spreadsheet level permission
        $permissions[] = "spreadsheet.{$sheet->spreadsheet->name}.read";
        
        // Sheet level permission
        $permissions[] = "spreadsheet.{$sheet->sheet_name}.read";
        
        // Column range permissions
        $columns = SpreadsheetColumn::where('spreadsheet_sheet_id', $sheet->id)
                                  ->where('is_active', true)
                                  ->get();
        
        foreach ($columns as $column) {
            $permissions[] = "spreadsheet.{$sheet->sheet_name}.{$column->column_range}.read";
        }

        // Create permissions and assign to role
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }
        
        $role->givePermissionTo($permissions);
    }

    public function removeRole(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'role_id' => 'required|exists:roles,id'
            ]);

            $user = User::findOrFail($validated['user_id']);
            $role = Role::findOrFail($validated['role_id']);
            
            $user->removeRole($role);

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil dihapus dari user!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUserPermissions($userId)
    {
        try {
            $user = User::with(['roles', 'permissions', 'employee'])->findOrFail($userId);
            
            $roles = $user->roles->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'team_id' => $role->team_id,
                    'permissions_count' => $role->permissions->count()
                ];
            });

            $allPermissions = $user->getAllPermissions()->map(function($permission) use ($user) {
                $viaRole = $user->hasPermissionTo($permission->name) && 
                          $user->getPermissionsViaRoles()->contains('name', $permission->name);
                return [
                    'name' => $permission->name,
                    'via_role' => $viaRole
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'roles' => $roles,
                    'all_permissions' => $allPermissions
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }
    }

    public function getAvailableTeamsAPI()
    {
        try {
            $teams = $this->getAvailableTeams();
            
            return response()->json([
                'success' => true,
                'data' => $teams
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading teams: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPermissionHierarchy()
    {
        try {
            $spreadsheets = Spreadsheet::with(['sheets.columns'])->where('is_active', true)->get();

            $hierarchy = $spreadsheets->map(function($spreadsheet) {
                return [
                    'id' => $spreadsheet->id,
                    'name' => $spreadsheet->name,
                    'permission_base' => "spreadsheet.{$spreadsheet->name}",
                    'sheets' => $spreadsheet->sheets->where('is_active', true)->map(function($sheet) use ($spreadsheet) {
                        return [
                            'id' => $sheet->id,
                            'name' => $sheet->sheet_name,
                            'display_name' => $sheet->sheet_display_name,
                            'permission_base' => "spreadsheet.{$sheet->sheet_name}",
                            'columns' => $sheet->columns->where('is_active', true)->map(function($column) use ($sheet) {
                                return [
                                    'id' => $column->id,
                                    'name' => $column->column_name,
                                    'range' => $column->column_range,
                                    'permission_base' => "spreadsheet.{$sheet->sheet_name}.{$column->column_range}"
                                ];
                            })
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $hierarchy
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading hierarchy: ' . $e->getMessage()
            ], 500);
        }
    }

    public function hierarchy()
    {
        $spreadsheets = \App\Models\Spreadsheet::with(['sheets.columns'])->where('is_active', true)->get();

        $data = $spreadsheets->map(function($spreadsheet) {
            return [
                'id' => $spreadsheet->id,
                'name' => $spreadsheet->name,
                'sheets' => $spreadsheet->sheets->where('is_active', true)->map(function($sheet) {
                    return [
                        'id' => $sheet->id,
                        'name' => $sheet->sheet_name,
                        'display_name' => $sheet->sheet_display_name,
                        'columns' => $sheet->columns->where('is_active', true)->map(function($col) {
                            return [
                                'id' => $col->id,
                                'name' => $col->column_name,
                                'range' => $col->column_range,
                                'key' => $col->column_key,
                            ];
                        })->values(),
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    private function getRoleBadgeClass($roleName)
    {
        if (str_contains($roleName, 'super-admin')) return 'badge-danger';
        if (str_contains($roleName, 'admin')) return 'badge-warning';
        if (str_contains($roleName, 'team-')) return 'badge-info';
        return 'badge-secondary';
    }
}
