<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spreadsheets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // Nama spreadsheet (contoh: "KLLA Reports Q1")
            $table->string('spreadsheet_id'); // Google Spreadsheet ID
            $table->string('spreadsheet_url')->nullable(); // URL lengkap spreadsheet
            $table->text('description')->nullable();
            $table->string('credentials_path')->nullable(); // Path ke file credentials JSON
            $table->string('service_account_email')->nullable(); // Email service account untuk tracking
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('spreadsheet_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spreadsheets');
    }
};
