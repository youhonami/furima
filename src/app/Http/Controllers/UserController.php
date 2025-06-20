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
        $tab  = request('tab', 'listed');

        // 「取引中」＋「購入済み」をどちらも取得
        $allInProgressItems = Item::whereHas('purchases', function ($query) use ($user) {
            $query->where('user_id', $user->id); // 自分が購入者
        })->orWhere(function ($query) use ($user) {
            $query->where('user_id', $user->id) // 自分が出品者で
                ->whereHas('purchases');     // 購入された商品
        })
            ->with(['chats.messages' => fn($q) => $q->latest('created_at')])
            ->get()
            ->sortByDesc(function ($item) {
                return optional($item->chats->flatMap->messages)->max('created_at') ?? $item->updated_at;
            });


        // 新着メッセージ数
        $newMessageCount = $allInProgressItems->reduce(function ($carry, $item) use ($user) {
            $chat = $item->chats()->first();
            if ($chat) {
                $carry += $chat->messages()
                    ->where('user_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->count();
            }
            return $carry;
        }, 0);

        // タブ別データ
        $listedItems    = $tab === 'listed'    ? Item::where('user_id', $user->id)->get()           : collect();
        $purchasedItems = $tab === 'purchased' ? Purchase::where('user_id', $user->id)->with('item')->get() : collect();
        $inProgressItems = $tab === 'inprogress' ? $allInProgressItems                                 : collect();

        // 平均評価
        $averageRating  = round($user->receivedRatings()->avg('rating') ?? 0);

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
