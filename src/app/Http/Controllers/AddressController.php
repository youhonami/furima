<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function updateAddress(Request $request)
    {
        // バリデーション
        $request->validate([
            'postal_code' => 'required|max:10',
            'address' => 'required|max:255',
            'building' => 'nullable|max:255',
        ]);

        // 空白も保存されるように処理
        $building = $request->building !== null ? trim($request->building) : '';

        // 入力データをセッションに保存
        session([
            'temp_address' => [
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $building, // 修正後の建物名を保存
            ]
        ]);

        // 商品IDを取得（商品購入画面から遷移してきた場合）
        $itemId = session('current_item_id'); // 商品IDを取得

        // 商品IDが存在しない場合の処理（適切なリダイレクトを指定）
        if (!$itemId) {
            return redirect('/')->with('status', '商品が見つかりませんでした。');
        }

        // purchase/{id} に商品IDを渡してリダイレクト
        return redirect()->route('purchase', ['id' => $itemId])->with('status', '配送先を更新しました。');
    }
}
