<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserSheetPermission extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'spreadsheet_sheet_id',
        'permission_level',
    ];

    /**
     * Relationship to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to SpreadsheetSheet
     */
    public function sheet(): BelongsTo
    {
        return $this->belongsTo(SpreadsheetSheet::class, 'spreadsheet_sheet_id');
    }
}
