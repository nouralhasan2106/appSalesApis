<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    protected $fillable = [
        'tenant_id',
        'branch_id',
        'name',
        'description',
        'unit',
        'current_quantity',
        'min_quantity',
        'cost_per_unit',
        'expiry_date',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];
}
