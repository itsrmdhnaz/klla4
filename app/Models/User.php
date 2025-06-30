<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship to Employee
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'id_user', 'id');
    }

    /**
     * Get user's accessible branches via team-based roles
     */
    public function getAccessibleBranches()
    {
        // Use explicit table joins to avoid ambiguity
        $teamIds = \DB::table('roles')
            ->join('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_id', $this->id)
            ->where('model_has_roles.model_type', self::class)
            ->whereNotNull('roles.team_id')
            ->pluck('roles.team_id')
            ->unique();
        
        return Branch::whereIn('id_branch', $teamIds)->get();
    }

    /**
     * Get team ID from session or default
     */
    public function getTeamId(): ?int
    {
        return session('current_team_id') ?? $this->getCurrentTeamId();
    }

    /**
     * Get current team ID (branch ID for this context)
     */
    public function getCurrentTeamId(): ?int
    {
        // Use explicit table joins to avoid ambiguity
        $teamRole = \DB::table('roles')
            ->join('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_id', $this->id)
            ->where('model_has_roles.model_type', self::class)
            ->whereNotNull('roles.team_id')
            ->select('roles.team_id')
            ->first();
            
        return $teamRole ? $teamRole->team_id : null;
    }

    /**
     * Check if user has team-based spreadsheet access
     */
    public function hasSpreadsheetAccess($spreadsheetId, $teamId = null): bool
    {
        $permission = "spreadsheet.{$spreadsheetId}.read";
        
        if ($teamId) {
            // Check team-specific role
            $teamRole = $this->roles()->where('team_id', $teamId)->first();
            if ($teamRole && $teamRole->hasPermissionTo($permission)) {
                return true;
            }
        }
        
        return $this->hasPermissionTo($permission);
    }

    /**
     * Check if user has team-based sheet access
     */
    public function hasSheetAccess($spreadsheetId, $sheetId, $teamId = null): bool
    {
        $permissions = [
            "{$spreadsheetId}.{$sheetId}.read",
            "{$spreadsheetId}.read"
        ];
        
        if ($teamId) {
            $teamRole = $this->roles()->where('team_id', $teamId)->first();
            if ($teamRole) {
                foreach ($permissions as $permission) {
                    if ($teamRole->hasPermissionTo($permission)) {
                        return true;
                    }
                }
            }
        }
        
        foreach ($permissions as $permission) {
            if ($this->hasPermissionTo($permission)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if user has team-based column access
     */
    public function hasColumnAccess($spreadsheetId, $sheetId, $columnKey, $teamId = null): bool
    {
        // Ambil sheet dan column untuk dapatkan range
        $sheet = \App\Models\SpreadsheetSheet::find($sheetId);
        if (!$sheet) return false;

        $column = \App\Models\SpreadsheetColumn::where('spreadsheet_sheet_id', $sheetId)
            ->where('column_key', $columnKey)
            ->first();
        if (!$column) return false;

        $range = $column->column_range;

        // Permission name sesuai struktur terbaru
        $permission = "{$spreadsheetId}.{$sheetId}." . strtolower($columnKey) . ".{$range}.read";

        if ($teamId) {
            $teamRole = $this->roles()->where('team_id', $teamId)->first();
            if ($teamRole && $teamRole->hasPermissionTo($permission)) {
                return true;
            }
        }

        return $this->hasPermissionTo($permission);
    }

    /**
     * Get user's team roles
     */
    public function getTeamRoles()
    {
        return $this->roles()->whereNotNull('team_id')->get();
    }

    /**
     * Get accessible teams for user
     */
    public function getAccessibleTeams()
    {
        return $this->roles()
            ->whereNotNull('team_id')
            ->with('permissions')
            ->get()
            ->groupBy('team_id');
    }

    /**
     * Get all sheets user can access
     */
    public function getAccessibleSheetIds(): array
    {
        $permissions = $this->getAllPermissions();
        $sheetIds = [];

        foreach ($permissions as $permission) {
            // Match pattern: sheet.branch_name.read or sheet.branch_name.column.key.read
            if (preg_match('/^sheet\.([^.]+)\.(read|write|admin)$/', $permission->name, $matches)) {
                $branchName = $matches[1];
                $branch = \App\Models\Branch::whereRaw('LOWER(REPLACE(branch_name, " ", "_")) = ?', [$branchName])->first();
                
                if ($branch) {
                    $sheets = \App\Models\SpreadsheetSheet::where('branch_id', $branch->id_branch)->pluck('id')->toArray();
                    $sheetIds = array_merge($sheetIds, $sheets);
                }
            }
        }

        return array_unique($sheetIds);
    }

    /**
     * Get all sheets user can access for a given spreadsheet and branch
     */
    public function getAccessibleSheetsForSpreadsheetBranch($spreadsheetId, $branchId)
    {
        // Get all sheet IDs for this spreadsheet and branch
        $sheets = \App\Models\SpreadsheetSheet::where('spreadsheet_id', $spreadsheetId)
            ->where('branch_id', $branchId)
            ->pluck('id')
            ->toArray();

        // Filter sheets by user access
        $accessible = [];
        foreach ($sheets as $sheetId) {
            if ($this->hasSheetAccess($spreadsheetId, $sheetId, $branchId)) {
                $accessible[] = $sheetId;
            }
        }
        return $accessible;
    }
}
