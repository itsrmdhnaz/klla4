<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spreadsheet_columns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('spreadsheet_sheet_id');
            $table->string('column_key'); // Key untuk identifikasi (contoh: "mpv_low")
            $table->string('column_name'); // Display name (contoh: "MPV Low")
            $table->string('column_range'); // Range di spreadsheet (contoh: "D10" atau "T10:T21")
            $table->enum('data_type', ['single', 'range', 'array'])->default('single');
            $table->text('description')->nullable();
            $table->json('validation_rules')->nullable(); // Rules untuk validasi data
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('spreadsheet_sheet_id')->references('id')->on('spreadsheet_sheets')->onDelete('cascade');
            $table->unique(['spreadsheet_sheet_id', 'column_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spreadsheet_columns');
    }
};
