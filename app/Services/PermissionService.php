<?php

namespace App\Services;

use App\Models\User;
use App\Models\SpreadsheetSheet;
use App\Models\SpreadsheetColumn;
use App\Models\Branch;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    /**
     * Create permissions when new sheet is added
     */
    public function createSheetPermissions(SpreadsheetSheet $sheet): void
    {
        $branchName = $sheet->branch ? str_replace(' ', '_', strtolower($sheet->branch->branch_name)) : 'general';
        
        // Create base sheet permissions
        $this->createPermission("sheet.{$sheet->id}.read");
        $this->createPermission("sheet.{$sheet->id}.write");
        $this->createPermission("sheet.{$sheet->id}.{$branchName}.read");
        $this->createPermission("sheet.{$sheet->id}.{$branchName}.write");
        
        // Create sheet group role
        $sheetRole = Role::firstOrCreate([
            'name' => "group.sheet_{$sheet->id}",
            'guard_name' => 'web'
        ]);
        
        $sheetRole->givePermissionTo([
            "sheet.{$sheet->id}.read",
            "sheet.{$sheet->id}.write",
            "sheet.{$sheet->id}.{$branchName}.read",
            "sheet.{$sheet->id}.{$branchName}.write"
        ]);
        
        // Add to branch group if exists
        if ($sheet->branch) {
            $branchRole = Role::where('name', "group.branch_{$sheet->branch_id}")->first();
            if ($branchRole) {
                $branchRole->givePermissionTo([
                    "sheet.{$sheet->id}.{$branchName}.read",
                    "sheet.{$sheet->id}.{$branchName}.write"
                ]);
            }
        }
    }

    /**
     * Create permissions when new column is added
     */
    public function createColumnPermissions(SpreadsheetColumn $column): void
    {
        $sheet = $column->sheet;
        $branchName = $sheet->branch ? str_replace(' ', '_', strtolower($sheet->branch->branch_name)) : 'general';
        $columnKey = $column->column_key;
        $range = str_replace(':', '_', $column->column_range);
        
        // Create column permissions
        $permissions = [
            "sheet.{$sheet->id}.{$branchName}.column.{$columnKey}.read",
            "sheet.{$sheet->id}.{$branchName}.column.{$columnKey}.write",
            "sheet.{$sheet->id}.{$branchName}.column.{$columnKey}.range.{$range}"
        ];
        
        foreach ($permissions as $permission) {
            $this->createPermission($permission);
        }
        
        // Add to relevant group roles
        $sheetRole = Role::where('name', "group.sheet_{$sheet->id}")->first();
        if ($sheetRole) {
            $sheetRole->givePermissionTo($permissions);
        }
        
        if ($sheet->branch) {
            $branchRole = Role::where('name', "group.branch_{$sheet->branch_id}")->first();
            if ($branchRole) {
                $branchRole->givePermissionTo($permissions);
            }
        }
        
        // Create column-specific group role
        $columnRole = Role::firstOrCreate([
            'name' => "group.column_{$column->id}",
            'guard_name' => 'web'
        ]);
        $columnRole->givePermissionTo($permissions);
    }

    /**
     * Create branch group role with all related permissions
     */
    public function createBranchRole(Branch $branch, string $type = 'user'): Role
    {
        $roleName = $type === 'admin' ? "group.branch_admin_{$branch->id_branch}" : "group.branch_{$branch->id_branch}";
        
        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web'
        ]);
        
        // Get all sheets for this branch
        $sheets = SpreadsheetSheet::where('branch_id', $branch->id_branch)->with('columns')->get();
        $branchSlug = str_replace(' ', '_', strtolower($branch->branch_name));
        
        $permissions = [];
        foreach ($sheets as $sheet) {
            // Add sheet permissions based on type
            if ($type === 'admin') {
                $permissions[] = "sheet.{$sheet->id}.{$branchSlug}.read";
                $permissions[] = "sheet.{$sheet->id}.{$branchSlug}.write";
            } else {
                $permissions[] = "sheet.{$sheet->id}.{$branchSlug}.read";
            }
            
            // Add column permissions
            foreach ($sheet->columns as $column) {
                $columnKey = $column->column_key;
                $permissions[] = "sheet.{$sheet->id}.{$branchSlug}.column.{$columnKey}.read";
                
                if ($type === 'admin') {
                    $permissions[] = "sheet.{$sheet->id}.{$branchSlug}.column.{$columnKey}.write";
                }
            }
        }
        
        // Create permissions if they don't exist and assign to role
        foreach ($permissions as $permission) {
            $this->createPermission($permission);
        }
        
        $existingPermissions = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($existingPermissions);
        
        return $role;
    }

    /**
     * Check if user can access specific sheet
     */
    public function canAccessSheet(User $user, $sheetId, $access = 'read'): bool
    {
        $sheet = SpreadsheetSheet::find($sheetId);
        if (!$sheet) return false;
        
        $branchName = $sheet->branch ? str_replace(' ', '_', strtolower($sheet->branch->branch_name)) : 'general';
        
        // Check direct permission
        if ($user->can("sheet.{$sheetId}.{$access}")) return true;
        if ($user->can("sheet.{$sheetId}.{$branchName}.{$access}")) return true;
        
        return false;
    }

    /**
     * Check if user can access specific column
     */
    public function canAccessColumn(User $user, $columnId, $access = 'read'): bool
    {
        $column = SpreadsheetColumn::with('sheet.branch')->find($columnId);
        if (!$column) return false;
        
        $sheet = $column->sheet;
        $branchName = $sheet->branch ? str_replace(' ', '_', strtolower($sheet->branch->branch_name)) : 'general';
        $columnKey = $column->column_key;
        
        // Check column permission
        if ($user->can("sheet.{$sheet->id}.{$branchName}.column.{$columnKey}.{$access}")) return true;
        
        // Check sheet permission (inherit)
        if ($user->can("sheet.{$sheet->id}.{$branchName}.{$access}")) return true;
        
        return false;
    }

    /**
     * Get user's accessible sheets
     */
    public function getUserAccessibleSheets(User $user): array
    {
        $allPermissions = $user->getAllPermissions();
        $sheetIds = [];
        
        foreach ($allPermissions as $permission) {
            if (preg_match('/^sheet\.(\d+)\./', $permission->name, $matches)) {
                $sheetIds[] = $matches[1];
            }
        }
        
        return array_unique($sheetIds);
    }

    /**
     * Assign user to group role
     */
    public function assignUserToGroup(User $user, string $groupType, $groupId): void
    {
        $roleName = "group.{$groupType}_{$groupId}";
        $role = Role::where('name', $roleName)->first();
        
        if ($role) {
            $user->assignRole($role);
        }
    }

    private function createPermission(string $name): Permission
    {
        return Permission::firstOrCreate([
            'name' => $name,
            'guard_name' => 'web'
        ]);
    }
}
