<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrdersDetails;
class OrderDetailsController extends Controller
{
     public function getOrderDetails(Request $request){
        $user=$request->user();
            $orders = Order::where('tenant_id', $user->tenant_id);
                           // ->find($order_id);
          
        if (!$orders) {
            return response()->json([
                'success' =>  $user->tenant_id,
                'message' => 'Orders not found'
            ], 404);
        }
      
         return response()->json([
            'success' => $user->tenant_id,
            'message' => 'order data retrieved successfully',
            'data' => [
                'orders' => $orders
            ]
        ], 201); 
    }
     public function addOrderDetails(Request $request){
        $user=$request->user();

          $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'item_id' => 'required',
            'item_variant_id' => 'required',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        $order_id=$data['order_id'];
        $order = Order::where('tenant_id', $user->tenant_id)
                            ->find($order_id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or does not belong to your tenant'
            ], 404);
        }
        $data['total_price'] = $data['quantity'] * $data['unit_price'];
       
        $orderDeails=OrdersDetails::create($data);

         return response()->json([
            'success' => true,
            'message' => 'order Deails added successfully',
            'data' => [
                'orderDeails' => $orderDeails
            ]
        ], 201);
    }
}
