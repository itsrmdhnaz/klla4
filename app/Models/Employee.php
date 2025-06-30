<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Employee extends Model
{
    use HasUuids;

    protected $primaryKey = 'nip';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nip',
        'nama_pegawai',
        'email',
        'id_user',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'id_user' => 'integer',
    ];

    /**
     * Relationship ke User (setelah akun dibuat)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    /**
     * Relationship many-to-many ke Branch
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'employee_branches', 'employee_nip', 'branch_id')
                    ->withTimestamps();
    }

    /**
     * Check if employee has access to specific branch
     */
    public function hasAccessToBranch($branchId): bool
    {
        return $this->branches()->where('branch_id', $branchId)->exists();
    }
}
