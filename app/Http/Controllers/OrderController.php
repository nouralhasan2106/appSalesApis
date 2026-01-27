<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
class OrderController extends Controller
{
    public function getOrders(Request $request){
        $user=$request->user();
        $orders=Order::where('tenant_id',$user->tenant_id)->latest()->paginate(2);
        return response()->json(['status'=>true,'data'=>[
            'order'=>$orders
        ]]);
    }
     public function getOrderById(Request $request,$id){
        $user=$request->user();
        $order=Order::where('tenant_id',$user->tenant_id)->find($id);
         if (!$order) {
        return response()->json([
            'success' => false,
            'message' => 'Order not found'
        ], 404);
    }
        return response()->json(['status'=>true,'data'=>[
            'order'=>$order
        ]]);
    }
     public function addOrder(Request $request){
        $user=$request->user();

        $data=$request->validate([
            'branch_id'=>'required|exists:branches,id',
             'shift_id' => 'nullable',
             'customer_id' => 'nullable',
            'order_type'=>'required|in:dine_in,takeaway,delivery',
           // 'status'=>'required|in:pending,confirmed,preparing,ready,completed,cancelled',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $taxAmount=$data['tax_amount']??0;
        $discountAmount=$data['discount_amount']??0;

        $totalAmount =$data['subtotal']+$taxAmount-$discountAmount;


        $order=Order::create([
            'tenant_id'=>$user->tenant_id,
            'branch_id' => $data['branch_id'],
            'shift_id' => $data['shift_id'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'order_type' => $data['order_type'],
            'status' => Order::STATUS_PENDING,
            'subtotal' => $data['subtotal'],
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'notes' => $data['notes'] ?? null,
            'created_by_user_id' => $user->id,
        ]);

         return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => [
                'order' => $order
            ]
        ], 201);
    }

    public function updateOrder(Request $request,$id){
        $user=$request->user();
        $order=Order::where('tenant_id',$user->tenant_id)->find($id);
        if(!$order){
            return response()->json([
                "status"=>false,
                "message"=>"Order not found"
            ],404);
        }

        $data=$request->validate([
            'branch_id'=>'nullable|exists:branches,id',
            'shift_id' => 'nullable',
            'customer_id' => 'nullable',
            'order_type'=>'nullable|in:dine_in,takeaway,delivery',
            'status'=>'nullable|in:pending,confirmed,preparing,ready,completed,cancelled',
            'subtotal' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);


        if(isset($data['subtotal'])|| isset($data['tax_amount'])||isset($data['discount_amount'])){
            $subtotal=$data['subtotal']??$order->subtotal;
            $tax_amount=$data['tax_amount']??$order->tax_amount;
            $discount_amount=$data['discount_amount']??$order->discount_amount;

             $data['total_amount'] =$subtotal+$tax_amount-$discount_amount;
        }

         $order->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => [
                'order' => $order
            ]
        ]);
    }
}
