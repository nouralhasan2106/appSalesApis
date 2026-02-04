<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrdersDetails;
class Order extends Model
{
    use HasFactory;
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY = 'ready';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    
    protected $fillable = [
        'tenant_id',
        'branch_id',
        'shift_id',
        'customer_id',
        'order_type',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'notes',
        'created_by_user_id',
    ];


    public function orderDetails(){
        return $this->hasMany(OrdersDetails::class,'order_id');
    }
}
