<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;

class BranchController extends Controller
{
      public function addBranch(Request $request){
      
        $data=$request->validate([
            'tenant_id'=>'required|exists:tenants,id',
            'name'=>'required|string|max:255',
            'email' => 'nullable|email|unique:branches,email',
            'phone' => 'nullable|string|max:50',
            'address'=>'nullable|string',
            'is_active' => 'boolean'
        ]);

        
        $branch=Branch::create($data);

         return response()->json([
            'success' => true,
            'message' => 'Branch created successfully',
            'data' => [
                'branch' => $branch
            ]
        ], 201);
    }
}
