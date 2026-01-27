<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TenantController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Login API
Route::post('auth/login', [AuthController::class, 'login']);

//Logout API  //'auth:sanctum'--->علشان محدش يقدر يعمل logout بدون token
//هذا Middleware بيتأكد إن الطلب فيه Token صحيح.
Route::middleware('auth:sanctum')->post('/logout',[AuthController::class, 'logout']);
//api/auth/me api
Route::middleware('auth:sanctum')->get('auth/me',[AuthController::class,'me']);
//api/auth/refresh api
Route::middleware('auth:sanctum')->post('/auth/refresh',[AuthController::class,'refresh']);


/////////////////TENANT Management//////////////////////////////////////////
//api//tenant/settings get tenant settings
Route::middleware('auth:sanctum')->get('/tenant/settings',[TenantController::class,'getSettings']);
//api//tenant/settings update tenant settings
Route::middleware('auth:sanctum')->post('/tenant/settings',[TenantController::class,'updateSettings']);
//api//tenant/upload-logo upload logo
Route::middleware('auth:sanctum')->post('/tenant/upload-logo',[TenantController::class,'uploadLogo']);