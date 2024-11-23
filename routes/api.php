<?php

use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\TravelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\TourController as AdminTourController;
use App\Http\Controllers\Api\Admin\TravelController as AdminTravelController;
use App\Http\Controllers\Api\Auth\LoginController;
use Illuminate\Routing\Controllers\Middleware;

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
Route::get('travel',[TravelController::class,'index']);
Route::get('tour/{travel:slug}',[TourController::class,'index']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('admin')->middleware(['auth:sanctum']) ->group(function(){
       
    Route::middleware('role:admin')->group(function(){

             Route::post('travel',[AdminTravelController::class,'store']);
             Route::post('tour/{travel}',[AdminTourController::class,'store']);
});
       Route::post('travel/{travel}',[AdminTravelController::class,'update']);
});

Route::post('login',LoginController::class);


    