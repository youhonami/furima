<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item; // 商品モデルをインポート
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index($id)
    {
        // 商品情報をデータベースから取得
        $item = Item::findOrFail($id);

        // ログイン中のユーザーのプロファイルを取得
        $profile = Auth::user()->profile;

        // purchase.blade.php を表示し、商品情報とプロファイル情報を渡す
        return view('purchase', [
            'item' => $item,
            'profile' => $profile
        ]);
    }
}
