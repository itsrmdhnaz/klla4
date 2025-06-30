<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserColumnPermission extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'spreadsheet_column_id',
        'can_read',
        'can_write',
    ];

    protected $casts = [
        'can_read' => 'boolean',
        'can_write' => 'boolean',
    ];

    /**
     * Relationship to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to SpreadsheetColumn
     */
    public function column(): BelongsTo
    {
        return $this->belongsTo(SpreadsheetColumn::class, 'spreadsheet_column_id');
    }
}
