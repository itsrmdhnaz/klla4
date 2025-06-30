<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_branches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('employee_nip'); // Sesuai dengan primary key employees
            $table->uuid('branch_id'); // Sesuai dengan primary key branches
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('employee_nip')->references('nip')->on('employees')->onDelete('cascade');
            $table->foreign('branch_id')->references('id_branch')->on('branches')->onDelete('cascade');
            
            // Unique constraint
            $table->unique(['employee_nip', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_branches');
    }
};
