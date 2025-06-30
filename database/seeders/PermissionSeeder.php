<?php

namespace Database\Seeders;

use App\Jobs\SeedBranchSheetJob;
use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Spreadsheet;
use App\Models\SpreadsheetSheet;
use App\Models\SpreadsheetColumn;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        foreach (Branch::all() as $branch) {
            SeedBranchSheetJob::dispatch($branch->id_branch);
        }

        // $spreadsheet = Spreadsheet::first();
        // if (!$spreadsheet) {
        //     throw new \Exception('No spreadsheet found. Please create a spreadsheet first.');
        // }

        // $branches = Branch::all();
        // $adminRole = Role::firstOrCreate(['name' => 'admin']);
        // $userRole = Role::firstOrCreate(['name' => 'user']);

        // foreach ($branches as $branch) {
        //     $sheet = SpreadsheetSheet::firstOrCreate([
        //         'spreadsheet_id' => $spreadsheet->id,
        //         'branch_id' => $branch->id_branch,
        //         'sheet_name' => $branch->branch_name,
        //         'sheet_display_name' => $branch->branch_name,
        //         'description' => "Sheet for {$branch->branch_name}",
        //         'is_active' => true,
        //     ]);

        //     foreach ($this->adminColumns() as $col) {
        //         $this->createColumnWithPermission($spreadsheet, $sheet, $col['key'], $col['name'], $col['range'], $adminRole);
        //     }

        //     foreach ($this->userColumns() as $col) {
        //         $this->createColumnWithPermission($spreadsheet, $sheet, $col['key'], $col['name'], $col['range'], $userRole, $adminRole);
        //     }
        // }

        DB::commit();
    }

    public function adminColumns()
    {
        return array_merge(
            $this->marketCompositionColumns(),
            $this->adminSheetColumns(),
            $this->marketAdminColumns(),
            $this->weeklyAdminColumns(),
            $this->progressColumns()
        );
    }

    public function userColumns()
    {
        return array_merge(
            $this->outstandingColumns(),
            $this->marketUserColumns(),
            $this->weeklyUserColumns()
        );
    }

    public function marketCompositionColumns()
    {
        return [
            ['key' => 'sum_1', 'name' => 'sum_1', 'range' => 'K9'],
            ['key' => 'list_1', 'name' => 'list_1', 'range' => 'K10:K21'],
            ['key' => 'sum_2', 'name' => 'sum_2', 'range' => 'K22'],
            ['key' => 'list_2', 'name' => 'list_2', 'range' => 'K23:K27'],
        ];
    }

    public function adminSheetColumns()
    {
        return [
            ['key' => 'kontribusi_dealer', 'name' => 'Kontribusi Dealer', 'range' => 'D3'],
            ['key' => 'kontribusi_cabang', 'name' => 'Kontribusi Cabang', 'range' => 'D4'],
            ['key' => 'mpv_low', 'name' => 'MPV Low', 'range' => 'D10'],
            ['key' => 'mpv_entry', 'name' => 'MPV Entry', 'range' => 'D11'],
            ['key' => 'mpv_medium', 'name' => 'MPV Medium', 'range' => 'D12'],
            ['key' => 'hb_entry', 'name' => 'HB Entry', 'range' => 'D13'],
            ['key' => 'hb_low', 'name' => 'HB Low', 'range' => 'D14'],
            ['key' => 'hb_medium', 'name' => 'HB Medium', 'range' => 'D15'],
            ['key' => 'suv_low', 'name' => 'SUV Low', 'range' => 'D16'],
            ['key' => 'suv_med_7_seat', 'name' => 'SUV Med 7 Seat', 'range' => 'D17'],
            ['key' => 'suv_med_5_seat', 'name' => 'SUV Med 5 Seat', 'range' => 'D18'],
            ['key' => 'suv_high', 'name' => 'SUV High', 'range' => 'D19'],
            ['key' => 'suv_high_2', 'name' => 'SUV High', 'range' => 'D20'],
        ];
    }

    public function outstandingColumns()
    {
        return [
            ['key' => 'firm_passenger', 'name' => 'FIRM Passenger', 'range' => 'F10:F21'],
            ['key' => 'end_stock_passenger', 'name' => 'END OF STOCK Passenger', 'range' => 'G10:G21'],
            ['key' => 'supply_mdp_passenger', 'name' => 'SUPPLY( MDP ) Passenger', 'range' => 'H10:H21'],
            ['key' => 'total_stock_passenger', 'name' => 'TOTAL STOCK Passenger', 'range' => 'I10:I21'],
            ['key' => 'firm_commercial', 'name' => 'FIRM Commercial', 'range' => 'F23:F27'],
            ['key' => 'end_stock_commercial', 'name' => 'END OF STOCK Commercial', 'range' => 'G23:G27'],
            ['key' => 'supply_mdp_commercial', 'name' => 'SUPPLY( MDP ) Commercial', 'range' => 'H23:H27'],
            ['key' => 'total_stock_commercial', 'name' => 'TOTAL STOCK Commercial', 'range' => 'I23:I27'],
        ];
    }

    public function marketAdminColumns()
    {
        return [
            ['key' => 'market', 'name' => 'Market', 'range' => 'M7'],
            ['key' => 'rundown_maret', 'name' => 'Rundown Maret', 'range' => 'N7'],
            ['key' => 'cr_percent', 'name' => 'CR %', 'range' => 'O7'],
            ['key' => 'regpol_rs_cr', 'name' => 'Regpol[RS / CR]', 'range' => 'P7'],
            ['key' => 'market_share', 'name' => 'Market Share[Regpol / Market]', 'range' => 'Q7'],

            ['key' => 'market_sum_1', 'name' => 'Market_sum_1', 'range' => 'M9'],
            ['key' => 'rundown_maret_sum_1', 'name' => 'Rundown Maret_sum_1', 'range' => 'N9'],
            ['key' => 'cr_percent_sum_1', 'name' => 'CR %_sum_1', 'range' => 'O9'],
            ['key' => 'regpol_rs_cr_sum_1', 'name' => 'Regpol[RS / CR]_sum_1', 'range' => 'P9'],
            ['key' => 'market_share_sum_1', 'name' => 'Market Share[Regpol / Market]_sum_1', 'range' => 'Q9'],

            ['key' => 'market_sum_2', 'name' => 'Market_sum_2', 'range' => 'M22'],
            ['key' => 'rundown_maret_sum_2', 'name' => 'Rundown Maret_sum_2', 'range' => 'N22'],
            ['key' => 'cr_percent_sum_2', 'name' => 'CR %_sum_2', 'range' => 'O22'],
            ['key' => 'regpol_rs_cr_sum_2', 'name' => 'Regpol[RS / CR]_sum_2', 'range' => 'P22'],
            ['key' => 'market_share_sum_2', 'name' => 'Market Share[Regpol / Market]_sum_2', 'range' => 'Q22'],

            ['key' => 'market_list_1', 'name' => 'Market_list_1', 'range' => 'M10:M21'],
            ['key' => 'cr_percent_list_1', 'name' => 'CR %_list_1', 'range' => 'O10:O21'],
            ['key' => 'regpol_rs_cr_list_1', 'name' => 'Regpol[RS / CR]_list_1', 'range' => 'P10:P21'],
            ['key' => 'market_share_list_1', 'name' => 'Market Share[Regpol / Market]_list_1', 'range' => 'Q10:Q21'],

            ['key' => 'market_list_2', 'name' => 'Market_list_2', 'range' => 'M23:M27'],
            ['key' => 'cr_percent_list_2', 'name' => 'CR %_list_2', 'range' => 'O23:O27'],
            ['key' => 'regpol_rs_cr_list_2', 'name' => 'Regpol[RS / CR]_list_2', 'range' => 'P23:P27'],
            ['key' => 'market_share_list_2', 'name' => 'Market Share[Regpol / Market]_list_2', 'range' => 'Q23:Q27'],
        ];
    }

    public function marketUserColumns()
    {
        return [
            ['key' => 'market', 'name' => 'Market', 'range' => 'M7'],
            ['key' => 'rundown_maret_list_1', 'name' => 'Rundown Maret_list_1', 'range' => 'N10:N21'],
            ['key' => 'rundown_maret_list_2', 'name' => 'Rundown Maret_list_2', 'range' => 'N23:N27'],
        ];
    }

    public function progressColumns()
    {
        return [
            ['key' => 'progress_do_mingguan_1', 'name' => 'Progress DO Mingguan 1', 'range' => 'Z29'],
            ['key' => 'progress_do_mingguan_2', 'name' => 'Progress DO Mingguan 2', 'range' => 'AH29'],
            ['key' => 'progress_do_mingguan_3', 'name' => 'Progress DO Mingguan 3', 'range' => 'AP29'],
            ['key' => 'progress_do_mingguan_4', 'name' => 'Progress DO Mingguan 4', 'range' => 'AX29'],
        ];
    }

    public function weeklyAdminColumns()
    {
        $cols = [];
        $colStart = ['Z', 'AH', 'AP', 'AX'];
        $weeklyBase = [
            ['key' => 'target_weekly', 'name' => 'Target Weekly'],
            ['key' => 'hot_prospek', 'name' => 'Hot Prospek'],
            ['key' => 'spk', 'name' => 'SPK'],
            ['key' => 'tunggu_dp', 'name' => 'Tunggu DP'],
            ['key' => 'tunggu_unit', 'name' => 'Tunggu Unit'],
            ['key' => 'tunggu_po', 'name' => 'Tunggu PO'],
            ['key' => 'do_aktual', 'name' => 'DO Aktual'],
        ];

        foreach (range(1, 4) as $i) {
            foreach ($weeklyBase as $idx => $base) {
                $colLetter = $this->excelCol($colStart[$i - 1], $idx);

                $cols[] = [
                    'key' => strtolower("{$base['key']}_minggu{$i}"),
                    'name' => "{$base['name']}_minggu{$i}",
                    'range' => "{$colLetter}7",
                ];
                $cols[] = [
                    'key' => strtolower("{$base['key']}_minggu{$i}_sum_1"),
                    'name' => "{$base['name']}_minggu{$i}_sum_1",
                    'range' => "{$colLetter}9",
                ];
                $cols[] = [
                    'key' => strtolower("{$base['key']}_minggu{$i}_sum_2"),
                    'name' => "{$base['name']}_minggu{$i}_sum_2",
                    'range' => "{$colLetter}22",
                ];
            }
        }

        return $cols;
    }

    public function weeklyUserColumns()
    {
        $cols = [];
        $colStart = ['Z', 'AH', 'AP', 'AX'];
        $weeklyBase = [
            ['key' => 'target_weekly', 'name' => 'Target Weekly'],
            ['key' => 'hot_prospek', 'name' => 'Hot Prospek'],
            ['key' => 'spk', 'name' => 'SPK'],
            ['key' => 'tunggu_dp', 'name' => 'Tunggu DP'],
            ['key' => 'tunggu_unit', 'name' => 'Tunggu Unit'],
            ['key' => 'tunggu_po', 'name' => 'Tunggu PO'],
            ['key' => 'do_aktual', 'name' => 'DO Aktual'],
        ];

        foreach (range(1, 4) as $i) {
            foreach ($weeklyBase as $idx => $base) {
                $colLetter = $this->excelCol($colStart[$i - 1], $idx);

                $cols[] = [
                    'key' => strtolower("{$base['key']}_minggu{$i}_list_1"),
                    'name' => "{$base['name']}_minggu{$i}_list_1",
                    'range' => "{$colLetter}10:{$colLetter}21",
                ];
                $cols[] = [
                    'key' => strtolower("{$base['key']}_minggu{$i}_list_2"),
                    'name' => "{$base['name']}_minggu{$i}_list_2",
                    'range' => "{$colLetter}23:{$colLetter}27",
                ];
            }
        }

        return $cols;
    }

    public function excelCol($start, $offset)
    {
        $letters = range('A', 'Z');

        if (strlen($start) === 1) {
            $startIdx = array_search($start, $letters);
            $idx = $startIdx + $offset;
            if ($idx < 26) return $letters[$idx];
            return $letters[intdiv($idx, 26) - 1] . $letters[$idx % 26];
        }

        $first = $start[0];
        $second = $start[1];
        $startIdx = (ord($first) - 65 + 1) * 26 + (ord($second) - 65);
        $idx = $startIdx + $offset;
        $firstLetter = $letters[intdiv($idx, 26) - 1];
        $secondLetter = $letters[$idx % 26];

        return $firstLetter . $secondLetter;
    }

    public function createColumnWithPermission($spreadsheet, $sheet, $key, $name, $range, ...$roles)
    {
        $dataType = (strpos($range, ':') !== false) ? SpreadsheetColumn::TYPE_RANGE : SpreadsheetColumn::TYPE_SINGLE;

        SpreadsheetColumn::firstOrCreate([
            'spreadsheet_sheet_id' => $sheet->id,
            'column_key' => strtolower($key),
            'column_name' => $name,
            'column_range' => $range,
        ], [
            'data_type' => $dataType,
            'description' => $name,
            'validation_rules' => [],
            'is_active' => true,
        ]);

        $permissionName = "{$spreadsheet->id}.{$sheet->id}." . strtolower($key) . ".{$range}.read";
        Permission::firstOrCreate(['name' => $permissionName]);

        foreach ($roles as $role) {
            $role->givePermissionTo($permissionName);
        }
    }
}
