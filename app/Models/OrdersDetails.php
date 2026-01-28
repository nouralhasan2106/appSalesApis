<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdersDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'item_id',
        'item_variant_id',
        'quantity',
        'unit_price',
        'total_price',
        'notes'
    ];
}
