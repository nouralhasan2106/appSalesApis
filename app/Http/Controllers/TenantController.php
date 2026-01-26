<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\tenants;

class TenantController extends Controller
{
    //
    public function getSettings(Request $request){
        $user=$request->user();
        $tenant=tenants::find($user->tenant_id);

        if(!$tenant){
            return response()->json([
                'success'=>false,
                'message'=>'Tenant not found'
            ],404);
        }

        return response()->json([
            'success'=>true,
            'data'=>[
                'tenant'=>$tenant
            ]
        ]);
    }
}
