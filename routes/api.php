<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("login",[AuthController::class,"login"]);
Route::post("register",[AuthController::class,"register"]);
Route::post("logout",[AuthController::class,"logout"])->middleware("auth:sanctum");


Route::group(['middleware' => 'auth:sanctum'],function (){
    Route::post("SendMessage",[ChatController::class,"SendMessage"]);
    Route::get("load",[MessagesController::class,"LoadThePreviousMessages"]);
    Route::delete('messages/{id}', [MessagesController::class, 'deleteMessage']);   
});
