<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant;
class Branch extends Model
{
     protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'address',
        "is_active"
    ];

    public function Tenant(){
        return $this->belongsTo(Tenant::class,'tenant_id');
    }

}
