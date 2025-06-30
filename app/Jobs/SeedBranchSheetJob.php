<?php

namespace App\Jobs;

use App\Models\Branch;
use App\Models\Spreadsheet;
use App\Models\SpreadsheetSheet;
use App\Models\SpreadsheetColumn;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SeedBranchSheetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $branchId;

    public function __construct($branchId)
    {
        $this->branchId = $branchId;
    }

    public function handle(): void
    {
        DB::beginTransaction();
        $spreadsheet = Spreadsheet::first();
        $branch = Branch::find($this->branchId);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $sheet = SpreadsheetSheet::firstOrCreate([
            'spreadsheet_id' => $spreadsheet->id,
            'branch_id' => $branch->id_branch,
            'sheet_name' => $branch->branch_name,
            'sheet_display_name' => $branch->branch_name,
            'description' => "Sheet for {$branch->branch_name}",
            'is_active' => true,
        ]);

        $seeder = new \Database\Seeders\PermissionSeeder();
        
        foreach ($seeder->adminColumns() as $col) {
            $seeder->createColumnWithPermission($spreadsheet, $sheet, $col['key'], $col['name'], $col['range'], $adminRole);
        }

        foreach ($seeder->userColumns() as $col) {
            $seeder->createColumnWithPermission($spreadsheet, $sheet, $col['key'], $col['name'], $col['range'], $userRole, $adminRole);
        }

        DB::commit();
    }
}
