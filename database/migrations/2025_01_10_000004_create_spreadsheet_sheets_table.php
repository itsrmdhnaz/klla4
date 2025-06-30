<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spreadsheet_sheets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('spreadsheet_id');
            $table->uuid('branch_id')->nullable(); // Link ke branch jika sheet represent cabang
            $table->string('sheet_name'); // Nama sheet di Google Spreadsheet (contoh: "Bone")
            $table->string('sheet_display_name')->nullable(); // Display name yang lebih friendly
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('spreadsheet_id')->references('id')->on('spreadsheets')->onDelete('cascade');
            $table->foreign('branch_id')->references('id_branch')->on('branches')->onDelete('set null');
            $table->unique(['spreadsheet_id', 'sheet_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spreadsheet_sheets');
    }
};
