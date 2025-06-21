<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Rating;

class UserController extends Controller
{
    // マイページ表示
    public function show()
    {
        $user = Auth::user();
        $tab  = request('tab', 'listed');

        //自分が購入者として購入した商品
        $buyerItems = Item::whereHas('purchases', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });

        //自分が出品者として売れた商品
        $sellerSoldItems = Item::where('user_id', $user->id)
            ->whereHas('purchases');

        //自分が seller or buyer のどちらでも、チャットが存在する商品
        $messagedItems = Item::whereHas('chats', function ($q) use ($user) {
            $q->where(function ($qq) use ($user) {
                $qq->where('seller_id', $user->id)
                    ->orWhere('buyer_id', $user->id);
            })->whereHas('messages');
        });

        //全取引中アイテム（重複除外 + リレーション読み込み）
        $baseItems = $buyerItems
            ->union($sellerSoldItems)
            ->union($messagedItems)
            ->with(['purchases', 'chats.messages'])
            ->get()
            ->unique('id');

        //最新アクティビティが新しい順（左）に並び替え
        $allInProgressItems = $baseItems
            ->map(function ($item) {
                $lastMsg     = optional($item->chats->flatMap->messages)->max('created_at');
                $lastPurchase = optional($item->purchases)->max('created_at');

                $item->last_activity = collect([$lastMsg, $lastPurchase])
                    ->filter()
                    ->max();

                return $item;
            })
            ->filter(fn($item) => $item->last_activity) // nullを除外
            ->sortByDesc('last_activity')
            ->values();

        $inProgressItems = $tab === 'inprogress' ? $allInProgressItems : collect();

        //新着メッセージ数
        $newMessageCount = $allInProgressItems->reduce(function ($carry, $item) use ($user) {
            $chat = $item->chats->first();
            if ($chat) {
                $carry += $chat->messages
                    ->where('user_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->count();
            }
            return $carry;
        }, 0);

        //タブごとのデータ取得
        $listedItems = $tab === 'listed'
            ? Item::where('user_id', $user->id)->get()
            : collect();

        $purchasedItems = $tab === 'purchased'
            ? Purchase::where('user_id', $user->id)
            ->with('item')
            ->orderBy('created_at', 'desc')
            ->get()
            : collect();

        //ユーザー平均評価
        $averageRating = round($user->receivedRatings()->avg('rating') ?? 0);

        //画面描画
        return view('mypage', compact(
            'user',
            'tab',
            'listedItems',
            'purchasedItems',
            'inProgressItems',
            'newMessageCount',
            'averageRating'
        ));
    }
}
