<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // We're now using Spatie Permission exclusively
        // This migration is kept for reference but tables are not created
        // All permissions are managed through spatie permission tables
        
        // Permission structure will be:
        // - sheet.{sheet_id}.read
        // - sheet.{sheet_id}.write
        // - sheet.{sheet_id}.{branch_name}.read
        // - sheet.{sheet_id}.{branch_name}.write
        // - sheet.{sheet_id}.{branch_name}.column.{column_key}.read
        // - sheet.{sheet_id}.{branch_name}.column.{column_key}.write
        // - sheet.{sheet_id}.{branch_name}.column.{column_key}.range.{range}
        
        // Group permissions:
        // - group.branch_{branch_id} (contains all permissions for a branch)
        // - group.sheet_{sheet_id} (contains all permissions for a sheet)
        // - group.column_{column_id} (contains all permissions for a column)
    }

    public function down(): void
    {
        // Nothing to drop since we're using Spatie Permission
    }
};