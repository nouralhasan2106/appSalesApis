<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Branch;
use App\Models\OrdersDetails;
use App\Models\Tenant;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function getOrders(Request $request){
        $user=$request->user();
        $orders=Order::with('orderDetails')->where('tenant_id',$user->tenant_id)->latest()->paginate(2);
        return response()->json(['status'=>true,'data'=>[
            'order'=>$orders
        ]]);
    }
     public function getOrderById(Request $request,$id){
        $user=$request->user();
        $order=Order::with("orderDetails")->where('tenant_id',$user->tenant_id)->find($id);
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
            'branch_id'=>['required',Rule::exists('branches','id')->where(function($query) use ($user){
                $query->where('tenant_id',$user->tenant_id);
            })], //'required|exists:branches,id',
            'shift_id' => 'nullable',
            'customer_id' =>['nullable',Rule::exists('customers','id')->where(function($query) use($user){
                $query->where('tenant_id',$user->tenant_id);
            })] ,//'nullable',
            'order_type'=>'required|in:dine_in,takeaway,delivery',
            'items'=>'required|array|min:1',
            'items.*.item_id'=>['required',Rule::exists('items','id')->where(function($query) use($user){
                $query->where('tenant_id',$user->tenant_id);
            })],
            'items.*.item_variant_id'=>'nullable',//'nullable|exists:item_variants,id',//نشوف الاول لو موجود جدول اسمه item_variants
            'items.*.quantity'=>'required|integer|min:1',
            'items.*.unit_price'=>'required|numeric|min:0',//نتأكد الاول انه بيتبعت في الريكويست
            'items.*.notes'=>'nullable|string',
           // 'status'=>'required|in:pending,confirmed,preparing,ready,completed,cancelled',
           // 'subtotal' => 'required|numeric|min:0',
           // 'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function() use($data,$user){
            $subtotal = 0;
            $orderDetails = [];
            foreach ($data['items'] as $item) {
               $total1=$item['unit_price']*$item['quantity'];
               $subtotal +=$total1;
               $orderDetails[]=[
                'item_id'=>$item['item_id'],
                'item_variant_id'=>$item['item_variant_id'] ?? null,
                'quantity'=>$item['quantity'],
                'unit_price'=>$item['unit_price'], // السعر جاي من request
                'total_price'=>$total1,
                'notes'=>$item['notes'],
                'created_at'=>now(),
                'updated_at'=>now(),
               ];
            }
            //get tax_rate
            $tenant=Tenant::find($user->tenant_id);
            $tax_amount=$subtotal * ($tenant->tax_rate / 100);
            $discount = $data['discount_amount'] ?? 0;
            $total = $subtotal + $tax_amount - $discount;

            //create Order
             $order=Order::create([
            'tenant_id'=>$user->tenant_id,
            'branch_id' => $data['branch_id'],
            'shift_id' => $data['shift_id'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'order_type' => $data['order_type'],
            'status' => Order::STATUS_PENDING,
            'subtotal' => $subtotal,
            'tax_amount' => $tax_amount,
          //  'discount_amount' => $discount,
            'total_amount' => $total,
            'paid_amount' => 0,
            'notes' => $data['notes'] ?? null,
            'created_by_user_id' => $user->id,
            ]);

            foreach ($orderDetails as &$detail) {
                $detail['order_id']=$order->id;
            }
            OrdersDetails::insert($orderDetails);
            return response()->json([
                'success'=>true,
                'message'=>'Order created successfully',
                'data'=>[
                    'order'=>$order
                ]
            ],201);

        });

    }
     public function addOrder1(Request $request){

        $user=$request->user();
        
        $data=$request->validate([
            'branch_id'=>['required',Rule::exists('branches','id')->where(function($query) use ($user){
                $query->where('tenant_id',$user->tenant_id);
            })], //'required|exists:branches,id',
            'shift_id' => 'nullable',
            'customer_id' =>['nullable',Rule::exists('customers','id')->where(function($query) use($user){
                $query->where('tenant_id',$user->tenant_id);
            })] ,//'nullable',
            'order_type'=>'required|in:dine_in,takeaway,delivery',
           // 'status'=>'required|in:pending,confirmed,preparing,ready,completed,cancelled',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
       /* //check if branch belongs to tenant
            if(!empty($data['branch_id'])){
                $branch=Branch::where('id',$data['branch_id'])->where('tenant_id',$user->tenant_id)->first();
                if (!$branch) {
                    throw ValidationException::withMessages([
                        'branch_id' => 'This branch does not belong to the selected tenant.'
                    ]);
                }
            }
        //check if customer belongs to tenant
            if(!empty($data['customer_id'])){
                $branch=Customer::where('id',$data['customer_id'])->where('tenant_id',$user->tenant_id)->first();
                if (!$branch) {
                    throw ValidationException::withMessages([
                        'customer_id' => 'This customer does not belong to the selected tenant.'
                    ]);
                }
            }*/
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
        $order=Order::where('tenant_id',$user->tenant_id)->findOrFail($id);
        if(in_array($order->status ,[Order::STATUS_COMPLETED,Order::STATUS_CANCELLED])){
             return response()->json([
            'status'=>false,
            'message'=>'This order cannot be modified'
            ],422);
        }
        $data = $request->validate([
            'branch_id'=>['sometimes',Rule::exists('branches','id')->where(function($query) use ($user){
                $query->where('tenant_id',$user->tenant_id);
            })], 
            'shift_id' => 'sometimes|nullable',
            'customer_id' =>['nullable',Rule::exists('customers','id')->where(function($query) use($user){
                $query->where('tenant_id',$user->tenant_id);
            })] ,
            'order_type'=>'sometimes|nullable|in:dine_in,takeaway,delivery',
            'status'=>'sometimes|nullable|in:pending,confirmed,preparing,ready,completed,cancelled',
           // 'subtotal' => 'sometimes|nullable|numeric|min:0',
           // 'tax_amount' => 'sometimes|nullable|numeric|min:0',
            'discount_amount' => 'sometimes|nullable|numeric|min:0',
            'notes' => 'sometimes|nullable|string',
            'items' => 'sometimes|array',
            'items.*.id' => 'sometimes|integer|exists:orders_details,id',
            'items.*.item_id' => 'required_without:items.*.id|integer|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',

        ]);

        DB::transaction(function() use($order,$data){
            $order->update($data);
            if(isset($data['items'])){
                //items ids from request
                $incomingIds =collect($data['items'])->pluck('id')->filter()->toArray();
                //items ids from db
                $dbIds=$order->orderDetails()->pluck('id')->toArray();
                //get ids of remaining items
                $toDelete=array_diff($dbIds,$incomingIds);

                //delete these items from db
                $order->orderDetails()->whereIn('id',$toDelete)->delete();

                foreach ($data['items'] as $item) {
                    $totalPrice = $item['quantity'] * $item['unit_price'];

                    //update
                    if(isset($item['id'])){
                        $updateData=[
                            'quantity'=>$item['quantity'],
                            'unit_price'=>$item['unit_price'], // السعر جاي من request
                            'total_price'=>$totalPrice ];
                        if(isset($item['notes'])){
                            $updateData['notes'] = $item['notes'];
                        }           
                        $order->orderDetails()->where('id',$item['id'])->update($updateData);
                    }
                    else //create new item
                    {
                         $order->orderDetails()->create([
                            'item_id'=>$item['item_id'],
                            'item_variant_id'=>$item['item_variant_id'] ?? null,
                            'quantity'=>$item['quantity'],
                            'unit_price'=>$item['unit_price'], // السعر جاي من request
                            'total_price'=>$totalPrice,
                            'notes' => $item['notes'] ?? null,
                            ]);
                    }

                }

            
                //recount the bill
                $subtotal=$order->orderDetails()->sum('total_price');
                $order->update([
                    'subtotal'=>$subtotal,
                    'total_amount'=>$subtotal + $order->tax_amount - $order->discount_amount
                ]);
                 
            }
        });
         return response()->json([
        'success' => true,
        'message' => 'Order updated successfully',
        'data' => [
            'order' => $order->load('orderDetails')
        ]
        ], 200);
    }

     public function updateOrder1(Request $request,$id){
        $user=$request->user();
        $order=Order::where('tenant_id',$user->tenant_id)->find($id);
        if(!$order){
            return response()->json([
                "status"=>false,
                "message"=>"Order not found"
            ],404);
        }

            $data = $request->validate([
            'branch_id'=>'sometimes|nullable|exists:branches,id',
            'shift_id' => 'sometimes|nullable',
            'customer_id' => 'sometimes|nullable',
            'order_type'=>'sometimes|nullable|in:dine_in,takeaway,delivery',
            'status'=>'sometimes|nullable|in:pending,confirmed,preparing,ready,completed,cancelled',
            'subtotal' => 'sometimes|nullable|numeric|min:0',
            'tax_amount' => 'sometimes|nullable|numeric|min:0',
            'discount_amount' => 'sometimes|nullable|numeric|min:0',
            'notes' => 'sometimes|nullable|string',
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
    public function deleteOrder(Request $request,$id){
        $user=$request->user();
        $order=Order::where('tenant_id',$user->tenant_id)->find($id);
        if(!$order){
            return response()->json([
                "status"=>false,
                "message"=>"Order not found"
            ],404);
        }
        $order->orderDetails()->delete();
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    }
}
