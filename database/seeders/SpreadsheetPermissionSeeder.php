<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Branch;
use App\Models\Spreadsheet;
use App\Models\SpreadsheetSheet;
use App\Models\SpreadsheetColumn;

class SpreadsheetPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create spreadsheet and sheet data first
        $this->createSpreadsheetData();
        
        // Create hierarchical permissions based on sheet.cabang.column pattern
        $this->createHierarchicalPermissions();
        
        // Create team-based roles for each branch
        $this->createTeamBasedRoles();
    }

    private function createSpreadsheetData()
    {
        // Create main spreadsheet
        $spreadsheet = Spreadsheet::firstOrCreate([
            'name' => 'KLLA Monthly Report',
            'spreadsheet_id' => '1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms', // Example ID
            'description' => 'Monthly sales and inventory report for all branches'
        ]);

        // Get all branches
        $branches = Branch::all();

        foreach ($branches as $branch) {
            // Create sheet for each branch
            $sheet = SpreadsheetSheet::firstOrCreate([
                'spreadsheet_id' => $spreadsheet->id,
                'sheet_name' => $branch->branch_name,
            ], [
                'branch_id' => $branch->id_branch,
                'sheet_display_name' => "Data {$branch->branch_name}",
                'description' => "Monthly report data for {$branch->branch_name} branch"
            ]);

            // Create columns based on the structure you provided
            $this->createColumnData($sheet);
        }
    }

    private function createColumnData($sheet)
    {
        $branchName = strtolower(str_replace(' ', '_', $sheet->sheet_name));
        
        // Admin-only columns (non-yellow/non-user accessible)
        $adminColumns = [
            // MPV Data (D10:D20)
            ['key' => 'mpv_low', 'name' => 'MPV Low', 'range' => 'D10', 'type' => 'single'],
            ['key' => 'mpv_entry', 'name' => 'MPV Entry', 'range' => 'D11', 'type' => 'single'],
            ['key' => 'mpv_medium', 'name' => 'MPV Medium', 'range' => 'D12', 'type' => 'single'],
            ['key' => 'hb_entry', 'name' => 'HB Entry', 'range' => 'D13', 'type' => 'single'],
            ['key' => 'hb_low', 'name' => 'HB Low', 'range' => 'D14', 'type' => 'single'],
            ['key' => 'hb_medium', 'name' => 'HB Medium', 'range' => 'D15', 'type' => 'single'],
            ['key' => 'suv_low', 'name' => 'SUV Low', 'range' => 'D16', 'type' => 'single'],
            ['key' => 'suv_med_7_seat', 'name' => 'SUV Med 7 Seat', 'range' => 'D17', 'type' => 'single'],
            ['key' => 'suv_med_5_seat', 'name' => 'SUV Med 5 Seat', 'range' => 'D18', 'type' => 'single'],
            ['key' => 'suv_high', 'name' => 'SUV High', 'range' => 'D19', 'type' => 'single'],
            ['key' => 'suv_high_2', 'name' => 'SUV High 2', 'range' => 'D20', 'type' => 'single'],

            // Commercial Data (D23:D26)
            ['key' => '2_ton', 'name' => '2 Ton', 'range' => 'D23', 'type' => 'single'],
            ['key' => 'pu_4x4', 'name' => 'PU 4x4', 'range' => 'D24', 'type' => 'single'],
            ['key' => 'pu_4x2_medium', 'name' => 'PU 4x2 Medium', 'range' => 'D25', 'type' => 'single'],
            ['key' => 'pu_4x2_low', 'name' => 'PU 4x2 Low', 'range' => 'D26', 'type' => 'single'],

            // Market Composition (K9-K27)
            ['key' => 'market_composition_sum_1', 'name' => 'Market Composition Sum 1', 'range' => 'K9', 'type' => 'single'],
            ['key' => 'market_composition_items_1', 'name' => 'Market Composition Items 1', 'range' => 'K10:K21', 'type' => 'range'],
            ['key' => 'market_composition_sum_2', 'name' => 'Market Composition Sum 2', 'range' => 'K22', 'type' => 'single'],
            ['key' => 'market_composition_items_2', 'name' => 'Market Composition Items 2', 'range' => 'K23:K27', 'type' => 'range'],

            // Market data admin only (M9, M10:M21, M22, M23:M27)
            ['key' => 'market_sum_1_admin', 'name' => 'Market Sum 1 (Admin)', 'range' => 'M9', 'type' => 'single'],
            ['key' => 'market_item_1_admin', 'name' => 'Market Item 1 (Admin)', 'range' => 'M10:M21', 'type' => 'range'],
            ['key' => 'market_sum_2_admin', 'name' => 'Market Sum 2 (Admin)', 'range' => 'M22', 'type' => 'single'],
            ['key' => 'market_item_2_admin', 'name' => 'Market Item 2 (Admin)', 'range' => 'M23:M27', 'type' => 'range'],

            // Rundown columns for admin (N8:Q27)
            ['key' => 'rundown_cr_percent', 'name' => 'CR %', 'range' => 'N8:N27', 'type' => 'range'],
            ['key' => 'regpol_rs_cr', 'name' => 'Regpol [RS / CR]', 'range' => 'O8:O27', 'type' => 'range'],
            ['key' => 'market_share_regpol', 'name' => 'Market Share [Regpol / Market]', 'range' => 'P8:P27', 'type' => 'range'],

            // Weekly target columns for admin (R8:X27)
            ['key' => 'target_weekly', 'name' => 'Target Weekly', 'range' => 'R8:R27', 'type' => 'range'],
            ['key' => 'hot_prospek', 'name' => 'Hot Prospek', 'range' => 'S8:S27', 'type' => 'range'],
            ['key' => 'spk', 'name' => 'SPK', 'range' => 'T8:T27', 'type' => 'range'],
            ['key' => 'tunggu_dp', 'name' => 'Tunggu DP', 'range' => 'U8:U27', 'type' => 'range'],
            ['key' => 'tunggu_unit', 'name' => 'Tunggu Unit', 'range' => 'V8:V27', 'type' => 'range'],
            ['key' => 'tunggu_po', 'name' => 'Tunggu PO', 'range' => 'W8:W27', 'type' => 'range'],
            ['key' => 'do_aktual', 'name' => 'DO Aktual', 'range' => 'X8:X27', 'type' => 'range'],
        ];

        // User-accessible columns (yellow background - user can read)
        $userColumns = [
            // Passenger data (F10:I21)
            ['key' => 'firm_passenger', 'name' => 'Firm Passenger', 'range' => 'F10:F21', 'type' => 'range'],
            ['key' => 'end_of_stock_passenger', 'name' => 'End of Stock Passenger', 'range' => 'G10:G21', 'type' => 'range'],
            ['key' => 'supply_mdp_passenger', 'name' => 'Supply (MDP) Passenger', 'range' => 'H10:H21', 'type' => 'range'],
            ['key' => 'total_stock_passenger', 'name' => 'Total Stock Passenger', 'range' => 'I10:I21', 'type' => 'range'],

            // Commercial data (F23:I27)
            ['key' => 'firm_commercial', 'name' => 'Firm Commercial', 'range' => 'F23:F27', 'type' => 'range'],
            ['key' => 'end_of_stock_commercial', 'name' => 'End of Stock Commercial', 'range' => 'G23:G27', 'type' => 'range'],
            ['key' => 'supply_mdp_commercial', 'name' => 'Supply (MDP) Commercial', 'range' => 'H23:H27', 'type' => 'range'],
            ['key' => 'total_stock_commercial', 'name' => 'Total Stock Commercial', 'range' => 'I23:I27', 'type' => 'range'],

            // Market value for user (M7)
            ['key' => 'market_value_user', 'name' => 'Market Value (User)', 'range' => 'M7', 'type' => 'single'],

            // Rundown Maret for user (N7)
            ['key' => 'rundown_maret_user', 'name' => 'Rundown Maret (User)', 'range' => 'N7', 'type' => 'single'],
        ];

        // Create admin columns
        foreach ($adminColumns as $columnData) {
            SpreadsheetColumn::firstOrCreate([
                'spreadsheet_sheet_id' => $sheet->id,
                'column_key' => $columnData['key'],
            ], [
                'column_name' => $columnData['name'],
                'column_range' => $columnData['range'],
                'data_type' => $columnData['type'],
                'description' => "Admin access only - {$columnData['name']} data"
            ]);
        }

        // Create user columns
        foreach ($userColumns as $columnData) {
            SpreadsheetColumn::firstOrCreate([
                'spreadsheet_sheet_id' => $sheet->id,
                'column_key' => $columnData['key'],
            ], [
                'column_name' => $columnData['name'],
                'column_range' => $columnData['range'],
                'data_type' => $columnData['type'],
                'description' => "User accessible - {$columnData['name']} data (yellow highlighted)"
            ]);
        }
    }

    private function createHierarchicalPermissions()
    {
        $sheets = SpreadsheetSheet::with(['branch', 'columns'])->get();
        
        foreach ($sheets as $sheet) {
            $branchName = strtolower(str_replace(' ', '_', $sheet->sheet_name));
            
            // Create sheet-level permissions
            Permission::firstOrCreate(['name' => "sheet.{$branchName}.read"]);
            Permission::firstOrCreate(['name' => "sheet.{$branchName}.write"]);
            Permission::firstOrCreate(['name' => "sheet.{$branchName}.admin"]);
            
            // Create column-level permissions
            foreach ($sheet->columns as $column) {
                $columnKey = $column->column_key;
                
                // Create permission: sheet.cabang.column.key.read/write
                Permission::firstOrCreate(['name' => "sheet.{$branchName}.column.{$columnKey}.read"]);
                Permission::firstOrCreate(['name' => "sheet.{$branchName}.column.{$columnKey}.write"]);
            }
        }

        // Create general permissions
        $generalPermissions = [
            'system.admin',
            'system.manage-users',
            'system.manage-roles',
            'system.view-reports',
            'system.export-data',
            'sheet.read',
            'sheet.write',
            'sheet.admin',
        ];

        foreach ($generalPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }

    private function createTeamBasedRoles()
    {
        $branches = Branch::all();
        
        foreach ($branches as $branch) {
            $branchName = strtolower(str_replace(' ', '_', $branch->branch_name));
            $teamId = $branch->id_branch;
            
            // Create team-admin role for this branch
            $teamAdminRole = Role::firstOrCreate([
                'name' => 'team-admin',
                'team_id' => $teamId,
                'guard_name' => 'web'
            ]);

            // Create team-user role for this branch
            $teamUserRole = Role::firstOrCreate([
                'name' => 'team-user',
                'team_id' => $teamId,
                'guard_name' => 'web'
            ]);

            // Assign permissions to team-admin (full access to their branch)
            $adminPermissions = [
                "sheet.{$branchName}.read",
                "sheet.{$branchName}.write",
                "sheet.{$branchName}.admin",
                'system.view-reports',
                'system.export-data',
            ];

            // Add all column permissions for admin
            $sheet = SpreadsheetSheet::where('branch_id', $teamId)->first();
            if ($sheet) {
                foreach ($sheet->columns as $column) {
                    $columnKey = $column->column_key;
                    $adminPermissions[] = "sheet.{$branchName}.column.{$columnKey}.read";
                    $adminPermissions[] = "sheet.{$branchName}.column.{$columnKey}.write";
                }
            }

            $teamAdminRole->syncPermissions($adminPermissions);

            // Assign permissions to team-user (only yellow/user-accessible columns)
            $userPermissions = [
                "sheet.{$branchName}.read",
                'system.view-reports',
            ];

            // Add only user-accessible column permissions (yellow highlighted)
            if ($sheet) {
                $userAccessibleColumns = [
                    'firm_passenger', 'end_of_stock_passenger', 'supply_mdp_passenger', 'total_stock_passenger',
                    'firm_commercial', 'end_of_stock_commercial', 'supply_mdp_commercial', 'total_stock_commercial',
                    'market_value_user', 'rundown_maret_user'
                ];

                foreach ($sheet->columns as $column) {
                    $columnKey = $column->column_key;
                    if (in_array($columnKey, $userAccessibleColumns)) {
                        $userPermissions[] = "sheet.{$branchName}.column.{$columnKey}.read";
                    }
                }
            }

            $teamUserRole->syncPermissions($userPermissions);
        }

        // Create system-wide roles
        $this->createSystemRoles();
    }

    private function createSystemRoles()
    {
        // Super Admin (access to everything, no team restriction)
        $superAdmin = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web'
        ]);
        $superAdmin->givePermissionTo(Permission::all());

        // System Admin (cross-team access)
        $systemAdmin = Role::firstOrCreate([
            'name' => 'system-admin',
            'guard_name' => 'web'
        ]);
        $systemAdmin->givePermissionTo([
            'system.admin',
            'system.manage-users',
            'system.manage-roles',
            'system.view-reports',
            'system.export-data',
            'sheet.read',
            'sheet.write',
            'sheet.admin',
        ]);

        // Multi-branch user (can access multiple branches)
        $multiBranchUser = Role::firstOrCreate([
            'name' => 'multi-branch-user',
            'guard_name' => 'web'
        ]);
        $multiBranchUser->givePermissionTo([
            'sheet.read',
            'system.view-reports',
        ]);
    }
}
