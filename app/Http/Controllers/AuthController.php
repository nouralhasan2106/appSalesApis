<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{

   public function addUser(Request $request){
    $data=$request->validate([
        "tenant_id"=>'required|exists:tenants,id',
        "branch_id"=>'nullable|exists:branches,id',
        "name"=>'required|string|max:255',
        "email"=>'required|email|unique:users,email',
        "password"=>'required|min:6',
        "role"=>['required',Rule::in([ 'super_admin','tenant_owner','branch_manager','cashier','accountant'])]
     ]);

     $user=User::create([
        'tenant_id'=>$data['tenant_id'],
        'branch_id'=>$data['branch_id']??null,
        'name'=>$data['name'],
        'email'=>$data['email'],
        'password'=>Hash::make($data['password']),
         'role'=>$data['role'],
         'is_active' => true,
     ]);
      return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => $user
        ], 201);
   }
   public function login(Request $request){
    //validate data entered by user
    $credentials=$request->validate([
        'email'=>'required|email',
        'password'=>'required'
    ]);

    //Attemp to login
    if(!Auth::attempt($credentials)){
        return response()->json([
            'success'=>false,
            'message'=>'Wrong user name or password'
        ],401);
    }


        //if success get user
        $user=Auth::user();
        //create token using laravel scantum
        $token=$user->createToken('auth_token')->plainTextToken;
        return response()->json(['success'=>true,
                'data'=>[
                    'user'=>[
                        'id'=>$user->id,
                        'name'=>$user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'tenant_id' => $user->tenant_id,
                        'branch_id' => $user->branch_id
                    ],
                    'token'=>$token,
                    'expires_in' => 3600

                ]]);
      
   }
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Client logged out successfully']);
    }

    public function me(Request $request){
        return response()->json(['sucess'=>true,'user'=>$request->user()]);
    }

    public function refresh(Request $request){
        $user=$request->user();
        $user->currentAccessToken()->delete();
        $newToken=$user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'sucsess'=>true,
            'token'=>$newToken,
            'user'=>$user]);
    }

}
