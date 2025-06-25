<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'branches';
    protected $primaryKey = 'id_branch';
    protected $guarded = [];
    public $timestamps = true;
}
