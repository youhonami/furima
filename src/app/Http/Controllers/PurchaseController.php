<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

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

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
        ]);

        $user = Auth::user();
        $itemId = session('current_item_id');
        $item = Item::findOrFail($itemId);

        // Stripe APIキー設定
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // データベースに購入情報を保存
        $purchase = new Purchase();
        $purchase->user_id = $user->id;
        $purchase->item_id = $item->id;
        $purchase->payment_method = $request->payment_method;

        $profile = $user->profile;
        if ($profile) {
            $purchase->postal_code = $profile->postal_code;
            $purchase->address = $profile->address;
            $purchase->building = $profile->building;
        } elseif (session('temp_address')) {
            $purchase->postal_code = session('temp_address.postal_code');
            $purchase->address = session('temp_address.address');
            $purchase->building = session('temp_address.building');
        } else {
            return back()->withErrors(['address' => '配送先が登録されていません。']);
        }

        $purchase->save();

        // Stripeセッションの作成
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('item.index'), // 決済成功時
            'cancel_url' => route('purchase.cancel', ['id' => $item->id]), // ← ここでキャンセルURLを設定
        ]);

        return redirect($session->url);
    }

    public function cancel($id)
    {
        $user = Auth::user();

        // 対象の購入データを取得
        $purchase = Purchase::where('user_id', $user->id)
            ->where('item_id', $id)
            ->latest()
            ->first();

        if ($purchase) {
            $purchase->delete();
        }

        return redirect()->route('purchase', ['id' => $id])->with('message', '購入がキャンセルされました。');
    }
}
