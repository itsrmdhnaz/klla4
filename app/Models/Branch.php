<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Branch extends Model
{
    use HasUuids;

    protected $primaryKey = 'id_branch';

    protected $fillable = [
        'branch_name',
    ];

    /**
     * Relationship many-to-many ke Employee
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_branches', 'branch_id', 'employee_nip')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }
}
