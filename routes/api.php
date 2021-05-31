<?php

use App\Http\Controllers\API\FoodController;
use App\Http\Controllers\API\MidtransController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// route untuk yang sudah login
Route::middleware('auth:sanctum')->group(function () {
  Route::get('/user', [UserController::class, 'fetch']);
  Route::post('/user', [UserController::class, 'updateProfile']);
  Route::post('/user/photo', [UserController::class, 'updatePhoto']);
  Route::post('/logout', [UserController::class, 'logout']);

  // route untuk mengambil data transaksi
  Route::get('/transaction', [TransactionController::class, 'all']);
  Route::post('/transaction/{id}', [TransactionController::class, 'update']);
  Route::post('/checkout', [TransactionController::class, 'checkout']);
});

// route untuk yang belum login atau register
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

// route untuk food
Route::get('/food', [FoodController::class, 'all']);

ROute::post('/midtrans/callback', [MidtransController::class, 'callback']);
