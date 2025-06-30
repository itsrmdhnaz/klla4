<?php

namespace App\Services;

use App\Models\User;
use App\Models\Branch;
use App\Models\SpreadsheetSheet;
use App\Models\SpreadsheetColumn;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TeamPermissionService
{
    /**
     * Assign user to team-based role
     */
    public function assignUserToTeam(User $user, $branchId, $roleType = 'team-user'): void
    {
        $role = Role::where('name', $roleType)
                   ->where('team_id', $branchId)
                   ->first();
        
        if ($role) {
            $user->assignRole($role);
        }
    }

    /**
     * Remove user from team
     */
    public function removeUserFromTeam(User $user, $branchId): void
    {
        $teamRoles = Role::where('team_id', $branchId)->get();
        foreach ($teamRoles as $role) {
            $user->removeRole($role);
        }
    }

    /**
     * Create permissions for new sheet with team context
     */
    public function createSheetPermissions(SpreadsheetSheet $sheet): void
    {
        $permissions = [
            "sheet.{$sheet->id}.read",
            "sheet.{$sheet->id}.write",
            "sheet.{$sheet->id}.admin",
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Auto-assign to relevant team roles if sheet belongs to a branch
        if ($sheet->branch_id) {
            $this->assignSheetToTeamRoles($sheet);
        }
    }

    /**
     * Create permissions for new column with team context
     */
    public function createColumnPermissions(SpreadsheetColumn $column): void
    {
        $sheet = $column->sheet;
        $permissions = [
            "sheet.{$sheet->id}.column.{$column->column_key}.read",
            "sheet.{$sheet->id}.column.{$column->column_key}.write",
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Auto-assign to relevant team roles
        if ($sheet->branch_id) {
            $this->assignColumnToTeamRoles($column);
        }
    }

    /**
     * Assign permissions to team role based on role type
     */
    public function assignPermissionsToTeamRole(Role $role, string $roleType): void
    {
        $teamId = $role->team_id;
        
        if (!$teamId) {
            return;
        }

        // Get all sheets for this team
        $relatedSheets = SpreadsheetSheet::where('branch_id', $teamId)->with('columns')->get();
        
        $permissions = [];
        
        foreach ($relatedSheets as $sheet) {
            // Add sheet permissions
            $permissions[] = "sheet.{$sheet->id}.read";
            if ($roleType === 'admin') {
                $permissions[] = "sheet.{$sheet->id}.write";
            }
            
            // Add column permissions
            foreach ($sheet->columns as $column) {
                $permissions[] = "sheet.{$sheet->id}.column.{$column->column_key}.read";
                if ($roleType === 'admin') {
                    $permissions[] = "sheet.{$sheet->id}.column.{$column->column_key}.write";
                }
            }
        }
        
        // Create permissions if they don't exist
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }
        
        // Assign permissions to role
        $existingPermissions = Permission::whereIn('name', $permissions)->get();
        $role->syncPermissions($existingPermissions);
    }

    /**
     * Check if user can access sheet in team context
     */
    public function canAccessSheet(User $user, $sheetId, $access = 'read', $teamId = null): bool
    {
        return $user->hasSheetAccess($sheetId, $access, $teamId);
    }

    /**
     * Check if user can access column in team context  
     */
    public function canAccessColumn(User $user, $columnId, $access = 'read', $teamId = null): bool
    {
        return $user->hasColumnAccess($columnId, $access, $teamId);
    }

    /**
     * Get user's permissions in specific team context
     */
    public function getUserTeamPermissions(User $user, $teamId): array
    {
        // Use explicit query to avoid ambiguity
        $permissions = \DB::table('permissions')
            ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->join('roles', 'role_has_permissions.role_id', '=', 'roles.id')
            ->join('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', User::class)
            ->where('roles.team_id', $teamId)
            ->select('permissions.name')
            ->distinct()
            ->pluck('name')
            ->toArray();
            
        return $permissions;
    }

    private function assignSheetToTeamRoles(SpreadsheetSheet $sheet): void
    {
        $teamId = $sheet->branch_id;
        
        // Get team roles
        $userRole = Role::where('name', 'team-user')->where('team_id', $teamId)->first();
        $adminRole = Role::where('name', 'team-admin')->where('team_id', $teamId)->first();
        
        if ($userRole) {
            $userRole->givePermissionTo("sheet.{$sheet->id}.read");
        }
        
        if ($adminRole) {
            $adminRole->givePermissionTo([
                "sheet.{$sheet->id}.read",
                "sheet.{$sheet->id}.write"
            ]);
        }
    }

    private function assignColumnToTeamRoles(SpreadsheetColumn $column): void
    {
        $sheet = $column->sheet;
        $teamId = $sheet->branch_id;
        
        if (!$teamId) return;
        
        // Get team roles
        $userRole = Role::where('name', 'team-user')->where('team_id', $teamId)->first();
        $adminRole = Role::where('name', 'team-admin')->where('team_id', $teamId)->first();
        
        if ($userRole) {
            $userRole->givePermissionTo("sheet.{$sheet->id}.column.{$column->column_key}.read");
        }
        
        if ($adminRole) {
            $adminRole->givePermissionTo([
                "sheet.{$sheet->id}.column.{$column->column_key}.read",
                "sheet.{$sheet->id}.column.{$column->column_key}.write"
            ]);
        }
    }
}
