<?php

use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// route untuk yang sudah login
Route::middleware('auth:sanctum')->group(function () {
  Route::get('user', [UserController::class, 'fetch']);
  Route::post('user', [UserController::class, 'updateProfile']);
  Route::post('user/photo', [UserController::class, 'updatePhoto']);
  Route::post('logout', [UserController::class, 'logout']);
});

// route untuk yang belum login atau register
Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
