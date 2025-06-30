<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SpreadsheetColumn extends Model
{
    use HasUuids;

    protected $fillable = [
        'spreadsheet_sheet_id',
        'column_key',
        'column_name',
        'column_range',
        'data_type',
        'description',
        'validation_rules',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'validation_rules' => 'array',
    ];

    /**
     * Data type constants
     */
    const TYPE_SINGLE = 'single';
    const TYPE_RANGE = 'range';
    const TYPE_ARRAY = 'array';

    /**
     * Get the sheet that owns this column
     */
    public function sheet(): BelongsTo
    {
        return $this->belongsTo(SpreadsheetSheet::class, 'spreadsheet_sheet_id');
    }

    /**
     * Get column permissions for users
     */
    public function userColumnPermissions(): HasMany
    {
        return $this->hasMany(UserColumnPermission::class);
    }

    /**
     * Check if user can read this column
     */
    public function userCanRead($userId): bool
    {
        // Check if user has access to parent sheet first
        if (!$this->sheet->userHasAccess($userId)) {
            return false;
        }

        // Check column-level permission
        $permission = $this->userColumnPermissions()->where('user_id', $userId)->first();
        
        return $permission ? $permission->can_read : false;
    }

    /**
     * Get column value using GoogleSheetsService
     */
    public function getValue($userId, callable $processor = null)
    {
        if (!$this->userCanRead($userId)) {
            throw new \Exception('Access denied to column: ' . $this->column_name);
        }

        $service = new \App\Services\GoogleSheetsService($this->sheet->spreadsheet);
        
        $data = match($this->data_type) {
            self::TYPE_SINGLE => $service->getCellValue($this->sheet->sheet_name, $this->column_range, $userId),
            self::TYPE_RANGE, self::TYPE_ARRAY => $service->readRange($this->sheet->sheet_name, $this->column_range, $userId),
            default => null
        };

        // Apply processor if provided
        if ($processor && is_callable($processor)) {
            $allData = $service->readRange($this->sheet->sheet_name, 'A:Z', $userId); // Get all data for context
            return $processor($data, $allData);
        }

        return $data;
    }
}
