<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SpreadsheetSheet extends Model
{
    use HasUuids;

    protected $guarded = [

    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship to Spreadsheet
     */
    public function spreadsheet(): BelongsTo
    {
        return $this->belongsTo(Spreadsheet::class);
    }

    /**
     * Relationship to Branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id_branch');
    }

    /**
     * Relationship to SpreadsheetColumns
     */
    public function columns(): HasMany
    {
        return $this->hasMany(SpreadsheetColumn::class);
    }

    /**
     * Relationship to UserSheetPermissions
     */
    public function userPermissions(): HasMany
    {
        return $this->hasMany(UserSheetPermission::class);
    }
}
