<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Storage;

class Spreadsheet extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'spreadsheet_id',
        'spreadsheet_url',
        'description',
        'credentials_path',
        'service_account_email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get Google API credentials from secure storage
     */
    public function getCredentials(): ?array
    {
        if (!$this->credentials_path || !Storage::disk('credentials')->exists($this->credentials_path)) {
            return null;
        }

        $credentials = Storage::disk('credentials')->get($this->credentials_path);
        return json_decode($credentials, true);
    }

    /**
     * Check if credentials file exists and is valid
     */
    public function hasValidCredentials(): bool
    {
        $credentials = $this->getCredentials();
        return $credentials && 
               isset($credentials['type']) && 
               $credentials['type'] === 'service_account';
    }

    /**
     * Store credentials securely
     */
    public function storeCredentials(array $credentials, string $filename = null): bool
    {
        if (!isset($credentials['type']) || $credentials['type'] !== 'service_account') {
            return false;
        }

        $filename = $filename ?: "spreadsheet-{$this->id}-credentials.json";
        
        $stored = Storage::disk('credentials')->put($filename, json_encode($credentials, JSON_PRETTY_PRINT));
        
        if ($stored) {
            $this->update(['credentials_path' => $filename]);
            return true;
        }
        
        return false;
    }

    /**
     * Get secure credential URL for authenticated users
     */
    public function getCredentialUrl(): ?string
    {
        if (!$this->credentials_path) {
            return null;
        }
        
        return route('admin.spreadsheet.credential', $this->id);
    }

    /**
     * Get all sheets for this spreadsheet
     */
    public function sheets(): HasMany
    {
        return $this->hasMany(SpreadsheetSheet::class);
    }

    /**
     * Get active sheets only
     */
    public function activeSheets(): HasMany
    {
        return $this->sheets()->where('is_active', true);
    }

    /**
     * Users who have permission to this spreadsheet
     */
    public function authorizedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_spreadsheet_permissions')
                    ->withPivot('permission_level')
                    ->withTimestamps();
    }

    /**
     * Get access logs for this spreadsheet
     */
    public function accessLogs(): HasMany
    {
        return $this->hasMany(SpreadsheetAccessLog::class);
    }

    /**
     * Check if user has access to this spreadsheet
     */
    public function userHasAccess($userId, $requiredLevel = 'read'): bool
    {
        $permission = $this->authorizedUsers()->where('user_id', $userId)->first();
        
        if (!$permission) {
            return false;
        }

        $levels = ['read' => 1, 'write' => 2, 'admin' => 3];
        $userLevel = $levels[$permission->pivot->permission_level] ?? 0;
        $required = $levels[$requiredLevel] ?? 1;

        return $userLevel >= $required;
    }
}
