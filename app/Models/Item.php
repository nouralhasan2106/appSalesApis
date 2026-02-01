<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
     protected $fillable = [
        'tenant_id',
        'branch_id',
        'category_id',
        'name',
        'description',
        'sku',
        'barcode',
        'image',
        'is_ready',
        'cost_price',
        'selling_price',
        'current_stock',
        'min_stock_level',
        'is_active',
    ];
//لما تجيب البيانات من الداتابيز، حوّل القيم دي لنوع معيّن)(boolean)
    protected $casts = [
    'is_ready' => 'boolean',
    'is_active' => 'boolean',
    ];

}
