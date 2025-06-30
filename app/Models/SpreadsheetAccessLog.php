<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SpreadsheetAccessLog extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'spreadsheet_id',
        'spreadsheet_sheet_id',
        'spreadsheet_column_id',
        'action',
        'request_data',
        'response_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    /**
     * Relationship to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to Spreadsheet
     */
    public function spreadsheet(): BelongsTo
    {
        return $this->belongsTo(Spreadsheet::class);
    }

    /**
     * Relationship to SpreadsheetSheet
     */
    public function sheet(): BelongsTo
    {
        return $this->belongsTo(SpreadsheetSheet::class, 'spreadsheet_sheet_id');
    }

    /**
     * Relationship to SpreadsheetColumn
     */
    public function column(): BelongsTo
    {
        return $this->belongsTo(SpreadsheetColumn::class, 'spreadsheet_column_id');
    }
}
