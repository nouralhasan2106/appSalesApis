<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailsController;
use App\Http\Controllers\CustomerController;
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

//add user API
Route::post('/register', [AuthController::class, 'addUser']);
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


//////////////////////// Order Management /////////////////////////
// api/orders get orders
Route::middleware('auth:sanctum')->get('/orders',[OrderController::class,'getOrders']);
// api/orders/{id} get order by id
Route::middleware('auth:sanctum')->get('/orders/{id}',[OrderController::class,'getOrderById']);
// api/orders add order
Route::middleware('auth:sanctum')->post('/orders',[OrderController::class,'addOrder']);
// api/orders/details add order details
Route::middleware('auth:sanctum')->post('/orders/details',[OrderDetailsController::class,'addOrderDetails']);
// api/orders/details get order details
Route::middleware('auth:sanctum')->get('/order/details',[OrderDetailsController::class,'getOrderDetails']);
// api/order/{id}  update order by id
Route::middleware('auth:sanctum')->put('/orders/{id}',[OrderController::class,'updateOrder']);
// api/order/{id}  delete order by id
Route::middleware('auth:sanctum')->delete('/orders/{id}',[OrderController::class,'deleteOrder']);


///////////////////////// Customers Managment//////////////////////////////////////////////////////////////////
// api/customers get customers
Route::middleware('auth:sanctum')->get('/customers',[CustomerController::class,'getCustomers']);
// api/customers add new customer
Route::middleware('auth:sanctum')->post('/customers',[CustomerController::class,'addCustomer']);
// api/customers/{id}  get customer by id
Route::middleware('auth:sanctum')->get('/customers/{id}',[CustomerController::class,'getCustomerById']);
// api/customers/{id}  update customer by id
Route::middleware('auth:sanctum')->put('/customers/{id}',[CustomerController::class,'updateCustomer']);
// api/customers/{id}  delete customer by id
Route::middleware('auth:sanctum')->delete('/customers/{id}',[CustomerController::class,'deleteCustomer']);
