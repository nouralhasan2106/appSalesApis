<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
class CustomerController extends Controller
{
    public function getCustomers(Request $request){
        $user=$request->user();
        $customers=Customer::where('tenant_id',$user->tenant_id)->get();
        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }
   public function getCustomerById(Request $request,$id){
        $user=$request->user();
        $customer=Customer::where('tenant_id',$user->tenant_id)->find($id);
         if (!$customer) {
        return response()->json([
            'success' => false,
            'message' => 'Customer not found'
        ], 404);
        }
        return response()->json(['status'=>true,'data'=>[
            'customer'=>$customer
        ]]);
    }
     public function addCustomer(Request $request){

        $user=$request->user();

        $data=$request->validate([
           'name'=>'required|string|max:255',
           'current_balance' => 'nullable|numeric|min:0'
            
        ]);

        $customer=Customer::create([
             'tenant_id' => $user->tenant_id,
            'name' => $request->name,
            'current_balance' => $request->current_balance ?? 0.0,
        ]);

         return response()->json([
            'success' => true,
            'message' => 'Customer created successfully',
            'data' => [
                'customer' => $customer
            ]
        ], 201);
    }
     public function updateCustomer(Request $request,$id){
        $user=$request->user();
        $customer=Customer::where('tenant_id',$user->tenant_id)->find($id);
        if(!$customer){
            return response()->json([
                "status"=>false,
                "message"=>"customer not found"
            ],404);
        }

        $data=$request->validate([
             'name' => 'sometimes|required|string|max:255',
             'current_balance' => 'nullable|numeric',
             'is_active' => 'nullable|boolean',
             'tenant_id' => 'sometimes|required|exists:tenants,id',
        ]);

         $customer->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully',
            'data' => [
                'customer' => $customer
            ]
        ]);
    }

     public function deleteCustomer(Request $request,$id){
        $user=$request->user();
        $customer=Customer::where('tenant_id',$user->tenant_id)->find($id);
        if(!$customer){
            return response()->json([
                "status"=>false,
                "message"=>"Customer not found"
            ],404);
        }
       $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully'
        ]);
    }
}
