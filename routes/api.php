<?php

use App\Events\Message;
use App\Events\SendMessage;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Jobs\MessageJob;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
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
    return event(new SendMessage(['receiver_id'=>1]));
});

Route::post('register',[AuthController::class, 'register'])->name('auth.register');
Route::post('login',[AuthController::class, 'login'])->name('auth.login');

Route::group(['middleware' => 'auth:api'],function(){
    Route::post('/chat/{user:id}',[ChatController::class, 'store']);
    Route::get('/user',[AuthController::class, 'user'])->name('auth.user');
    Route::get('/chat/{user:id}',[ChatController::class, 'index']);
});

Route::prefix('/auth')->group(function(){
    Route::post('/logout',[AuthController::class, 'logout'])->name('auth.logout');
});

Route::post('/arifin',function(Request $request){
    $expiresAt = new \DateTime('+3 months');
    $imageReference = app('firebase.storage')->getBucket()->object("Chat/154-playstation-black-logo-video-games-wallpaper-preview-eKFG.jpeg");
    $image = $imageReference->signedUrl($expiresAt);
    return $image;
});
