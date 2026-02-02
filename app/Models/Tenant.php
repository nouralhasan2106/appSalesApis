<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;
class Tenant extends Model
{
   protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'logo',
        'currency',
        'tax_rate'
    ];

    public function Branch(){
        return $this->hasMany(Branch::class);
    }

}
