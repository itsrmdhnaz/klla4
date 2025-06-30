<?php

namespace App\Services;

use App\Models\User;
use App\Models\Branch;
use App\Models\SpreadsheetSheet;
use App\Models\SpreadsheetColumn;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SpreadsheetPermissionService
{
    /**
     * Check if user can read specific column based on yellow highlighting rule
     */
    public function canUserReadColumn(User $user, $sheetId, $columnKey): bool
    {
        $sheet = SpreadsheetSheet::with('branch')->find($sheetId);
        if (!$sheet) return false;

        $branchName = strtolower(str_replace(' ', '_', $sheet->sheet_name));
        
        // Check if user has permission for this specific column
        $permission = "sheet.{$branchName}.column.{$columnKey}.read";
        
        return $user->hasPermissionTo($permission);
    }

    /**
     * Get all accessible columns for user in a sheet
     */
    public function getUserAccessibleColumns(User $user, $sheetId): array
    {
        $sheet = SpreadsheetSheet::with(['branch', 'columns'])->find($sheetId);
        if (!$sheet) return [];

        $branchName = strtolower(str_replace(' ', '_', $sheet->sheet_name));
        $accessibleColumns = [];

        foreach ($sheet->columns as $column) {
            $permission = "sheet.{$branchName}.column.{$column->column_key}.read";
            
            if ($user->hasPermissionTo($permission)) {
                $accessibleColumns[] = [
                    'id' => $column->id,
                    'key' => $column->column_key,
                    'name' => $column->column_name,
                    'range' => $column->column_range,
                    'data_type' => $column->data_type,
                    'can_write' => $user->hasPermissionTo("sheet.{$branchName}.column.{$column->column_key}.write"),
                    'description' => $column->description
                ];
            }
        }

        return $accessibleColumns;
    }

    /**
     * Assign user to multiple branches
     */
    public function assignUserToMultipleBranches(User $user, array $branchIds, string $roleType = 'team-user'): void
    {
        foreach ($branchIds as $branchId) {
            $this->assignUserToBranch($user, $branchId, $roleType);
        }
    }

    /**
     * Assign user to specific branch team
     */
    public function assignUserToBranch(User $user, $branchId, string $roleType = 'team-user'): void
    {
        $role = Role::where('name', $roleType)
                   ->where('team_id', $branchId)
                   ->first();
        
        if ($role) {
            $user->assignRole($role);
        }
    }

    /**
     * Check if user is admin for any branch
     */
    public function isUserBranchAdmin(User $user): bool
    {
        return $user->hasRole('super-admin') || 
               $user->hasRole('system-admin') || 
               $user->roles()->where('name', 'team-admin')->exists();
    }

    /**
     * Get user's branch admin status per branch
     */
    public function getUserBranchAdminStatus(User $user): array
    {
        $adminRoles = $user->roles()->where('name', 'team-admin')->get();
        $status = [];

        foreach ($adminRoles as $role) {
            if ($role->team_id) {
                $branch = Branch::find($role->team_id);
                if ($branch) {
                    $status[$role->team_id] = [
                        'branch_name' => $branch->branch_name,
                        'is_admin' => true
                    ];
                }
            }
        }

        return $status;
    }

    /**
     * Create permissions when new sheet is added
     */
    public function createSheetPermissions(SpreadsheetSheet $sheet): void
    {
        $branchName = strtolower(str_replace(' ', '_', $sheet->sheet_name));
        
        // Create sheet-level permissions
        Permission::firstOrCreate(['name' => "sheet.{$branchName}.read"]);
        Permission::firstOrCreate(['name' => "sheet.{$branchName}.write"]);
        Permission::firstOrCreate(['name' => "sheet.{$branchName}.admin"]);
    }

    /**
     * Create permissions when new column is added
     */
    public function createColumnPermissions(SpreadsheetColumn $column): void
    {
        $sheet = $column->sheet;
        $branchName = strtolower(str_replace(' ', '_', $sheet->sheet_name));
        $columnKey = $column->column_key;
        
        // Create column-level permissions
        Permission::firstOrCreate(['name' => "sheet.{$branchName}.column.{$columnKey}.read"]);
        Permission::firstOrCreate(['name' => "sheet.{$branchName}.column.{$columnKey}.write"]);

        // Auto-assign to team roles based on column accessibility
        $this->autoAssignColumnPermissions($column);
    }

    /**
     * Auto-assign column permissions based on whether it's user-accessible (yellow)
     */
    private function autoAssignColumnPermissions(SpreadsheetColumn $column): void
    {
        $sheet = $column->sheet;
        $branchName = strtolower(str_replace(' ', '_', $sheet->sheet_name));
        $columnKey = $column->column_key;
        $teamId = $sheet->branch_id;

        if (!$teamId) return;

        // Get team roles
        $teamAdminRole = Role::where('name', 'team-admin')->where('team_id', $teamId)->first();
        $teamUserRole = Role::where('name', 'team-user')->where('team_id', $teamId)->first();

        // Admin always gets both read and write
        if ($teamAdminRole) {
            $teamAdminRole->givePermissionTo([
                "sheet.{$branchName}.column.{$columnKey}.read",
                "sheet.{$branchName}.column.{$columnKey}.write"
            ]);
        }

        // Check if this column is user-accessible (yellow highlighted)
        $userAccessibleColumns = [
            'firm_passenger', 'end_of_stock_passenger', 'supply_mdp_passenger', 'total_stock_passenger',
            'firm_commercial', 'end_of_stock_commercial', 'supply_mdp_commercial', 'total_stock_commercial',
            'market_value_user', 'rundown_maret_user'
        ];

        if (in_array($columnKey, $userAccessibleColumns) && $teamUserRole) {
            $teamUserRole->givePermissionTo("sheet.{$branchName}.column.{$columnKey}.read");
        }
    }
}
