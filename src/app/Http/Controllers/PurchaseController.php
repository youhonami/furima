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
            'success_url' => route('purchase.success'),
            'cancel_url' => route('purchase', ['id' => $item->id]),  // 商品購入画面に戻るURLを設定
        ]);

        // StripeのCheckoutページにリダイレクト
        return redirect($session->url);
    }
}
