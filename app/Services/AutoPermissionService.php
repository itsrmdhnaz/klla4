<?php

namespace App\Services;

use App\Models\Spreadsheet;
use App\Models\SpreadsheetSheet;
use App\Models\SpreadsheetColumn;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AutoPermissionService
{
    /**
     * Auto-create permissions when spreadsheet is created
     */
    public function createSpreadsheetPermissions(Spreadsheet $spreadsheet): void
    {
        $permissions = [
            "spreadsheet.{$spreadsheet->id}.read",
            "spreadsheet.{$spreadsheet->id}.write",
            "spreadsheet.{$spreadsheet->id}.admin"
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign to super admin automatically
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }
    }

    /**
     * Auto-create permissions when sheet is created
     */
    public function createSheetPermissions(SpreadsheetSheet $sheet): void
    {
        $branchName = $sheet->branch ? strtolower(str_replace(' ', '_', $sheet->branch->branch_name)) : 'general';
        
        $permissions = [
            "sheet.{$sheet->id}.read",
            "sheet.{$sheet->id}.write", 
            "sheet.{$sheet->id}.admin",
            "sheet.{$branchName}.read",
            "sheet.{$branchName}.write",
            "sheet.{$branchName}.admin"
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Auto-assign to team roles if sheet has branch
        if ($sheet->branch_id) {
            $this->assignSheetToTeamRoles($sheet, $branchName);
        }

        // Assign to super admin automatically
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }
    }

    /**
     * Auto-create permissions when column is created
     */
    public function createColumnPermissions(SpreadsheetColumn $column): void
    {
        $sheet = $column->sheet;
        $branchName = $sheet->branch ? strtolower(str_replace(' ', '_', $sheet->branch->branch_name)) : 'general';
        $columnKey = $column->column_key;
        
        $permissions = [
            "sheet.{$sheet->id}.column.{$columnKey}.read",
            "sheet.{$sheet->id}.column.{$columnKey}.write",
            "sheet.{$branchName}.column.{$columnKey}.read", 
            "sheet.{$branchName}.column.{$columnKey}.write",
            "column.{$columnKey}.read",
            "column.{$columnKey}.write"
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Auto-assign to team roles if sheet has branch
        if ($sheet->branch_id) {
            $this->assignColumnToTeamRoles($column, $branchName);
        }

        // Assign to super admin automatically
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }
    }

    /**
     * Generate permission name based on hierarchy
     */
    public function generatePermissionName($type, $ids, $action = 'read'): string
    {
        switch ($type) {
            case 'spreadsheet':
                return "spreadsheet.{$ids['spreadsheet_id']}.{$action}";
            
            case 'sheet':
                return "sheet.{$ids['sheet_id']}.{$action}";
            
            case 'sheet_branch':
                return "sheet.{$ids['branch_name']}.{$action}";
            
            case 'column':
                return "sheet.{$ids['sheet_id']}.column.{$ids['column_key']}.{$action}";
            
            case 'column_branch':
                return "sheet.{$ids['branch_name']}.column.{$ids['column_key']}.{$action}";
            
            default:
                throw new \InvalidArgumentException("Unknown permission type: {$type}");
        }
    }

    /**
     * Generate multiple permissions for hierarchy selection
     */
    public function generateHierarchyPermissions($data): array
    {
        $permissions = [];
        
        // If spreadsheet selected
        if (!empty($data['spreadsheet_id'])) {
            $permissions[] = $this->generatePermissionName('spreadsheet', [
                'spreadsheet_id' => $data['spreadsheet_id']
            ], $data['action'] ?? 'read');
        }
        
        // If sheets selected
        if (!empty($data['sheet_ids'])) {
            foreach ($data['sheet_ids'] as $sheetId) {
                $permissions[] = $this->generatePermissionName('sheet', [
                    'sheet_id' => $sheetId
                ], $data['action'] ?? 'read');
                
                // Add branch-based permission if branch exists
                $sheet = SpreadsheetSheet::find($sheetId);
                if ($sheet && $sheet->branch) {
                    $branchName = strtolower(str_replace(' ', '_', $sheet->branch->branch_name));
                    $permissions[] = $this->generatePermissionName('sheet_branch', [
                        'branch_name' => $branchName
                    ], $data['action'] ?? 'read');
                }
            }
        }
        
        // If columns selected
        if (!empty($data['column_ids'])) {
            foreach ($data['column_ids'] as $columnId) {
                $column = SpreadsheetColumn::with('sheet.branch')->find($columnId);
                if ($column) {
                    $permissions[] = $this->generatePermissionName('column', [
                        'sheet_id' => $column->sheet->id,
                        'column_key' => $column->column_key
                    ], $data['action'] ?? 'read');
                    
                    // Add branch-based column permission
                    if ($column->sheet->branch) {
                        $branchName = strtolower(str_replace(' ', '_', $column->sheet->branch->branch_name));
                        $permissions[] = $this->generatePermissionName('column_branch', [
                            'branch_name' => $branchName,
                            'column_key' => $column->column_key
                        ], $data['action'] ?? 'read');
                    }
                }
            }
        }
        
        return array_unique($permissions);
    }

    private function assignSheetToTeamRoles(SpreadsheetSheet $sheet, string $branchName): void
    {
        $teamId = $sheet->branch_id;
        
        // Get team roles
        $teamUserRole = Role::where('name', 'team-user')->where('team_id', $teamId)->first();
        $teamAdminRole = Role::where('name', 'team-admin')->where('team_id', $teamId)->first();
        
        if ($teamUserRole) {
            $teamUserRole->givePermissionTo([
                "sheet.{$sheet->id}.read",
                "sheet.{$branchName}.read"
            ]);
        }
        
        if ($teamAdminRole) {
            $teamAdminRole->givePermissionTo([
                "sheet.{$sheet->id}.read",
                "sheet.{$sheet->id}.write",
                "sheet.{$branchName}.read",
                "sheet.{$branchName}.write"
            ]);
        }
    }

    private function assignColumnToTeamRoles(SpreadsheetColumn $column, string $branchName): void
    {
        $sheet = $column->sheet;
        $teamId = $sheet->branch_id;
        $columnKey = $column->column_key;
        
        // Get team roles
        $teamUserRole = Role::where('name', 'team-user')->where('team_id', $teamId)->first();
        $teamAdminRole = Role::where('name', 'team-admin')->where('team_id', $teamId)->first();
        
        if ($teamUserRole) {
            $teamUserRole->givePermissionTo([
                "sheet.{$sheet->id}.column.{$columnKey}.read",
                "sheet.{$branchName}.column.{$columnKey}.read"
            ]);
        }
        
        if ($teamAdminRole) {
            $teamAdminRole->givePermissionTo([
                "sheet.{$sheet->id}.column.{$columnKey}.read",
                "sheet.{$sheet->id}.column.{$columnKey}.write",
                "sheet.{$branchName}.column.{$columnKey}.read",
                "sheet.{$branchName}.column.{$columnKey}.write"
            ]);
        }
    }
}
