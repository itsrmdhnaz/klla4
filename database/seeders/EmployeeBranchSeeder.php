<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class EmployeeBranchSeeder extends Seeder
{
    public function run(): void
    {
        // Contoh data employee
        $employees = [
            [
                'nama_pegawai' => 'John Doe',
                'email' => 'john@example.com',
                'branches' => ['Bone', 'Soppeng'], // Multiple cabang
                'primary_branch' => 'Bone'
            ],
            [
                'nama_pegawai' => 'Jane Smith',
                'email' => 'jane@example.com',
                'branches' => ['Kendari'],
                'primary_branch' => 'Kendari'
            ],
        ];

        foreach ($employees as $employeeData) {
            $employee = Employee::create([
                'nama_pegawai' => $employeeData['nama_pegawai'],
                'email' => $employeeData['email'],
            ]);

            foreach ($employeeData['branches'] as $branchName) {
                $branch = Branch::where('branch_name', $branchName)->first();
                if ($branch) {
                    $isPrimary = $branchName === $employeeData['primary_branch'];
                    $employee->branches()->attach($branch->id_branch, [
                        'is_primary' => $isPrimary
                    ]);
                }
            }
        }
    }
}
