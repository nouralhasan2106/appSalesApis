<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

}
