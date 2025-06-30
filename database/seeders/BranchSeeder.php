<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brances = [
            ['branch_name' => 'Bone', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Soppeng', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Sengkang', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Kendari', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Tandean', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Kendari 3', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Kolaka', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Bau Bau', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Palu Metadinata', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Palu Juanda', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Poso', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Luwuk Benggal', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($brances as $branch) {
            Branch::updateOrCreate($branch);
        }
    }
}
