<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Item;
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

    public function Order(){
        return $this->belongsTo(Order::class,"order_id");
    }
    public function Item(){
        return $this->belongsTo(Item::class,"item_id");
    }
}
