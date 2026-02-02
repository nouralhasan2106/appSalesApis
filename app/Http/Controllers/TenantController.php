<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    public function addTenant(Request $request){
      
        $data=$request->validate([
            'name'=>'required|string|max:255',
            'email' => 'nullable|email|unique:tenants,email',
            'phone' => 'nullable|string|max:50',
            'address'=>'nullable|string',
            'logo' => 'nullable|string|max:255',
            'currency' => 'nullable|string|size:3',
            'tax_rate' => 'nullable|numeric|min:0'
        ]);

        
        $tenant=Tenant::create($data);

         return response()->json([
            'success' => true,
            'message' => 'Tenant created successfully',
            'data' => [
                'tenant' => $tenant
            ]
        ], 201);
    }
    //
    public function getSettings(Request $request){
        $user=$request->user();
        $tenant=Tenant::find($user->tenant_id);

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

     public function updateSettings(Request $request){
        $user=$request->user();
        $tenant=Tenant::find($user->tenant_id);

        if(!$tenant){
            return response()->json([
                'success'=>false,
                'message'=>'Tenant not found'
            ],404);
        }

        $data=$request->validate([
            'name'=>'string|max:255',
            'email'=>'nullable|email',
            'phone'=>'nullable|string|max:50',
            'address'=>'nullable|string',
            'logo'=>'nullable|image|mimes:jpg,png,jpeg',
            'currency'  => 'nullable|string|max:3',
            'tax_rate'=>'nullable|numeric|min:0|max:100|regex:/^\d{1,3}(\.\d{1,2})?$/'
        ]);
       
        //upload logo
        if($request->hasFile('logo')){
            if($tenant->logo){
                //delete the old logo
                Storage::delete(
                    //replace storage/logos/tenant_1_logo.jpg by storage/ → public/ to be public/logos/tenant_1_logo.jpg
                    str_replace('storage/', 'public/', $tenant->logo)
                );
            }

                //upload the new logo
                //store logo in storage/app/public/logos
                $path=$request->file('logo')->store('logos','public');

                //store path in db
                $data['logo']='storage/'. $path;
        }

        $tenant->update($data);
         return response()->json([
            'success' => true,
            'message' => 'Tenant settings updated successfully',
            'data' => [
                'tenant' => $tenant
            ]
        ]);
    }
     public function uploadLogo(Request $request){
        $user=$request->user();
        $tenant=Tenant::find($user->tenant_id);

        if(!$tenant){
            return response()->json([
                'success'=>false,
                'message'=>'Tenant not found'
            ],404);
        }

        $data=$request->validate([
           
            'logo'=>'nullable|image|mimes:jpg,png,jpeg'
        ]);
       
        //upload logo
        if($request->hasFile('logo')){
            if($tenant->logo){
                //delete the old logo
                Storage::delete(
                    //replace storage/logos/tenant_1_logo.jpg by storage/ → public/ to be public/logos/tenant_1_logo.jpg
                    str_replace('storage/', 'public/', $tenant->logo)
                );
            }

                //upload the new logo
                //store logo in storage/app/public/logos
                $path=$request->file('logo')->store('logos','public');

                //store path in db
                $data['logo']='storage/'. $path;
        }

        $tenant->update($data);
         return response()->json([
            'success' => true,
            'message' => 'Logo updated successfully',
            'data' => [
                'tenant' => $tenant
            ]
        ]);
    }
}
