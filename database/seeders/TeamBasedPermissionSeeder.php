<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Branch;
use App\Models\SpreadsheetSheet;
use App\Models\SpreadsheetColumn;

class TeamBasedPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create hierarchical permissions
        $this->createHierarchicalPermissions();
        
        // Create team-based roles for each branch
        $this->createTeamBasedRoles();
        
        // Create global system roles
        $this->createSystemRoles();
    }

    private function createHierarchicalPermissions()
    {
        // Base permissions - hierarchical structure
        $basePermissions = [
            // Sheet level permissions
            'sheet.read',
            'sheet.write',
            'sheet.admin',
            'sheet.*',
            
            // Column level permissions  
            'sheet.column.read',
            'sheet.column.write',
            'sheet.column.*',
            
            // System permissions
            'system.admin',
            'system.manage-users',
            'system.manage-roles',
            'system.view-reports',
            'system.export-data',
        ];

        foreach ($basePermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create specific permissions for existing sheets and columns
        $this->createSpecificPermissions();
    }

    private function createSpecificPermissions()
    {
        // Create permissions for each sheet
        $sheets = SpreadsheetSheet::with(['columns', 'branch'])->get();
        
        foreach ($sheets as $sheet) {
            // Sheet-specific permissions
            $sheetPermissions = [
                "sheet.{$sheet->id}.read",
                "sheet.{$sheet->id}.write",
                "sheet.{$sheet->id}.admin",
            ];

            foreach ($sheetPermissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }

            // Column-specific permissions
            foreach ($sheet->columns as $column) {
                $columnPermissions = [
                    "sheet.{$sheet->id}.column.{$column->column_key}.read",
                    "sheet.{$sheet->id}.column.{$column->column_key}.write",
                    "sheet.{$sheet->id}.column.{$column->column_key}.*",
                ];

                foreach ($columnPermissions as $permission) {
                    Permission::firstOrCreate(['name' => $permission]);
                }

                // Range-specific permissions if needed
                $range = str_replace([':', '.'], '_', $column->column_range);
                Permission::firstOrCreate([
                    'name' => "sheet.{$sheet->id}.column.{$column->column_key}.range.{$range}"
                ]);
            }
        }
    }

    private function createTeamBasedRoles()
    {
        $branches = Branch::all();
        
        foreach ($branches as $branch) {
            $teamId = $branch->id_branch;
            
            // Create Branch User Role (team-scoped)
            $branchUserRole = Role::create([
                'name' => 'branch-user',
                'guard_name' => 'web',
                'team_id' => $teamId
            ]);

            // Create Branch Admin Role (team-scoped)
            $branchAdminRole = Role::create([
                'name' => 'branch-admin', 
                'guard_name' => 'web',
                'team_id' => $teamId
            ]);

            // Assign permissions to branch user (read-only)
            $userPermissions = [
                'sheet.read',
                'sheet.column.read',
            ];
            
            // Add specific sheet permissions for this branch
            $branchSheets = SpreadsheetSheet::where('branch_id', $teamId)->get();
            foreach ($branchSheets as $sheet) {
                $userPermissions[] = "sheet.{$sheet->id}.read";
                
                foreach ($sheet->columns as $column) {
                    $userPermissions[] = "sheet.{$sheet->id}.column.{$column->column_key}.read";
                }
            }

            $branchUserRole->givePermissionTo($userPermissions);

            // Assign permissions to branch admin (read-write)
            $adminPermissions = [
                'sheet.read',
                'sheet.write', 
                'sheet.column.read',
                'sheet.column.write',
                'system.view-reports',
                'system.export-data',
            ];

            // Add specific sheet permissions for this branch
            foreach ($branchSheets as $sheet) {
                $adminPermissions[] = "sheet.{$sheet->id}.read";
                $adminPermissions[] = "sheet.{$sheet->id}.write";
                
                foreach ($sheet->columns as $column) {
                    $adminPermissions[] = "sheet.{$sheet->id}.column.{$column->column_key}.read";
                    $adminPermissions[] = "sheet.{$sheet->id}.column.{$column->column_key}.write";
                }
            }

            $branchAdminRole->givePermissionTo($adminPermissions);
        }
    }

    private function createSystemRoles()
    {
        // Super Admin (no team restriction)
        $superAdmin = Role::create([
            'name' => 'super-admin',
            'guard_name' => 'web'
        ]);
        $superAdmin->givePermissionTo(Permission::all());

        // System Admin (no team restriction)
        $systemAdmin = Role::create([
            'name' => 'system-admin',
            'guard_name' => 'web'
        ]);
        $systemAdmin->givePermissionTo([
            'system.admin',
            'system.manage-users',
            'system.manage-roles',
            'system.view-reports',
            'system.export-data',
            'sheet.*',
        ]);

        // Data Viewer (cross-team read access)
        $dataViewer = Role::create([
            'name' => 'data-viewer',
            'guard_name' => 'web'
        ]);
        $dataViewer->givePermissionTo([
            'sheet.read',
            'sheet.column.read',
            'system.view-reports',
        ]);
    }
}
