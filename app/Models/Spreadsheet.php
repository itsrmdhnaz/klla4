<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spreadsheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'spreadsheet_id',
        'credentials_path',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get credentials for Google Sheets API
     */
    public function getCredentials(): ?array
    {
        if (empty($this->credentials_path)) {
            return null;
        }

        $credentialsPath = storage_path('app/' . $this->credentials_path);
        
        if (!file_exists($credentialsPath)) {
            return null;
        }

        $credentials = json_decode(file_get_contents($credentialsPath), true);
        
        return $credentials;
    }
}
