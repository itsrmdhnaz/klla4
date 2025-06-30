<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spreadsheet_access_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->uuid('spreadsheet_id');
            $table->uuid('spreadsheet_sheet_id')->nullable();
            $table->uuid('spreadsheet_column_id')->nullable();
            $table->enum('action', ['read', 'write', 'update', 'delete'])->default('read');
            $table->json('request_data')->nullable(); // Data yang diminta
            $table->json('response_data')->nullable(); // Data yang dikembalikan
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('spreadsheet_id')->references('id')->on('spreadsheets')->onDelete('cascade');
            $table->foreign('spreadsheet_sheet_id')->references('id')->on('spreadsheet_sheets')->onDelete('set null');
            $table->foreign('spreadsheet_column_id')->references('id')->on('spreadsheet_columns')->onDelete('set null');
            
            $table->index(['user_id', 'created_at']);
            $table->index(['spreadsheet_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spreadsheet_access_logs');
    }
};
