<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Chat;
use App\Models\Rating;  // ← 追加

class UserController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // クエリパラメータでタブを切り替える
        $tab = request('tab', 'listed');
        $listedItems = collect();
        $purchasedItems = collect();
        $inProgressItems = collect();

        // 取引中商品の取得（自分が送信者でも受信者でも）
        $allInProgressItems = Item::whereHas('chats', function ($query) {
            $query->whereHas('messages');
        })
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id) // 出品者
                    ->orWhereHas('chats', function ($query) use ($user) {
                        $query->where('buyer_id', $user->id); // 購入者
                    });
            })
            ->with(['chats.messages' => function ($query) {
                $query->latest('created_at');
            }])
            ->get()
            ->sortByDesc(function ($item) {
                return optional($item->chats->flatMap->messages)->max('created_at');
            });

        // 新着メッセージ合計件数を計算
        $newMessageCount = 0;
        foreach ($allInProgressItems as $item) {
            $chat = $item->chats()->first();
            if ($chat) {
                $unreadCount = $chat->messages()
                    ->where('user_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->count();
                $newMessageCount += $unreadCount;
            }
        }

        // タブごとのデータ
        if ($tab === 'listed') {
            $listedItems = Item::where('user_id', $user->id)->get();
        } elseif ($tab === 'purchased') {
            $purchasedItems = Purchase::where('user_id', $user->id)->with('item')->get();
        } elseif ($tab === 'inprogress') {
            $inProgressItems = $allInProgressItems;
        }

        // 平均評価
        $averageRating = $user->receivedRatings()->avg('rating');
        $averageRating = $averageRating ? round($averageRating) : 0;

        return view('mypage', compact(
            'user',
            'tab',
            'listedItems',
            'purchasedItems',
            'inProgressItems',
            'newMessageCount',
            'averageRating'  // 追加
        ));
    }
}
