<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
  public function all(Request $request)
  {
    $id = $request->input('id');
    $limit = $request->input('limit', 6);
    $foodId = $request->input('food_id');
    $status = $request->input('status');

    if ($id) {
      $transaction = Transactions::find($id);
      if ($transaction) {
        return ResponseFormatter::success($transaction, 'Data transaksi berhasil diambil');
      } else {
        return ResponseFormatter::error(null, 'Data transaksi tidak ada', 404);
      }
    }

    $transaction = Transactions::with(['food', 'user'])->where('user_id', Auth::user()->id);
    if ($foodId) {
      $transaction->where('food_id', $foodId);
    }
    if ($status) {
      $transaction->where('status', $status);
    }

    return ResponseFormatter::success($transaction->paginate($limit), 'Data transaksi list berhasil diambil!');
  }

  public function update(Request $request, $id)
  {
    $transaction = Transactions::findOrFail($id);
    $transaction->update($request->all());
    return ResponseFormatter::success($transaction, 'Transaksi berhasil diperbarui');
  }
}
