<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

  use PasswordValidationRules;

  public function login(Request $request)
  {
    try {
      // validasi input
      $request->validate([
        'email' => 'email|required',
        'password' => 'required'
      ]);

      // mengecek crendentials
      $credentials = Request(['email', 'password']);
      if (!Auth::attempt($credentials)) {
        return ResponseFormatter::error([
          'message' => 'Unauthorized'
        ], 'Authentication Failed', 500);
      }

      // jika hash tidak sesuai, beri informasi error
      $user = User::where('email', $request->email)->first();
      if (!Hash::check($request->password, $user->password, [])) {
        throw new \Exception('Invalid credential');
      }

      // Jika berhasil, maka loginkan
      $tokenResult = $user->createToken('authToken')->plainTextToken; // mendapatkan token
      return ResponseFormatter::success([  // menampilkan hasil register apakah berhasil atau tidak
        'access_token' => $tokenResult,
        'token_type' => 'Bearer',
        'user' => $user
      ], 'Authenticated');
    } catch (Exception $error) {
      return ResponseFormatter::error([
        'message' => 'Something went wrong!',
        'error' => $error
      ], 'Authentication Failed', 500);
    }
  }

  public function register(Request $request)
  {
    try {
      // validasi input
      $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|max:255|email|unique:users,email',
        'password' => $this->passwordRules()
      ]);

      // save data
      User::create([
        'name' => $request->name,
        'email' => $request->email,
        'address' => $request->address,
        'house_number' => $request->house_number,
        'phone_number' => $request->phone_number,
        'city' => $request->city,
        'password' => Hash::make($request->password),
      ]);

      $user = User::where('email', $request->email)->first();
      $tokenResult = $user->createToken('authToken')->plainTextToken; // mendapatkan token
      return ResponseFormatter::success([  // menampilkan hasil register apakah berhasil atau tidak
        'token_result' => $tokenResult,
        'token_type' => 'Bearer',
        'user' => $user
      ]);
    } catch (Exception $error) {
      return ResponseFormatter::error([
        'message' => 'Something went wrong!',
        'error' => $error
      ], 'Authentication Failed', 500);
    }
  }

  public function logout(Request $request)
  {
    $token = $request->user()->currentAccessToken()->delete();
    return ResponseFormatter::success($token, 'Token Revoked');
  }

  public function fetch(Request $request)
  {
    return ResponseFormatter::success($request->user(), 'Data profile user berhasil diambil');
  }

  public function updateProfile(Request $request)
  {
    $data = $request->all();
    $user = Auth::user();
    $user->update($data);

    return ResponseFormatter::success($user, 'Profile Updated');
  }

  public function updatePhoto(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'file' => 'required|image|max:2048'
    ]);

    if ($validator->fails()) {
      return ResponseFormatter::error([
        'error' => $validator->errors(),
      ], 'Upload photo failed!', 401);
    }

    if ($request->file('file')) {
      $file = $request->file->store('assets/user', 'public');
      $user = Auth::user();
      $user->profile_photo_path = $file; // simpan data path photo ke database
      $user->update();

      return ResponseFormatter::success([$file], 'File successfully uploaded!');
    }
  }
}
