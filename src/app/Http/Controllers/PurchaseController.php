<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index($id)
    {
        $item = Item::findOrFail($id);
        $profile = Auth::user()->profile;

        // 商品IDをセッションに保存
        session(['current_item_id' => $item->id]);

        return view('purchase', [
            'item' => $item,
            'profile' => $profile
        ]);
    }

    public function store(PurchaseRequest $request)
    {
        // 必要な情報を取得
        $user = Auth::user();
        $itemId = session('current_item_id');
        $profile = $user->profile;
        $tempAddress = session('temp_address');

        // 必要なデータが揃っているか確認
        if (!$itemId || (!$tempAddress && !$profile)) {
            return redirect()->back()->withErrors('必要な情報が不足しています。');
        }

        // 建物名の処理（空文字を明示的に保存する）
        $building = $tempAddress['building'] ?? ($profile->building ?? '');

        // 購入データの保存
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $itemId,
            'payment_method' => $request->input('payment_method'),
            'postal_code' => $tempAddress['postal_code'] ?? $profile->postal_code,
            'address' => $tempAddress['address'] ?? $profile->address,
            'building' => $building,
        ]);

        // トップページにリダイレクト
        return redirect('/')->with('success', '購入が完了しました！');
    }
}
