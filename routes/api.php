<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::get('/',function(){
    return 'ok';
});

Route::post('register',[AuthController::class, 'register'])->name('auth.register');
Route::post('login',[AuthController::class, 'login'])->name('auth.login');

Route::group(['middleware' => 'auth:api','prefix' => 'auth'],function(){
    Route::post('logout',[AuthController::class, 'logout'])->name('auth.logout');
});
