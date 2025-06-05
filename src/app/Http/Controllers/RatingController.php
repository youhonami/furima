<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Item;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SellerRatedNotification;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ratee_id' => 'required|exists:users,id',
            'item_id' => 'required|exists:items,id',
            'role' => 'required|in:seller,buyer',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $alreadyRated = Rating::where('rater_id', auth()->id())
            ->where('ratee_id', $request->ratee_id)
            ->where('item_id', $request->item_id)
            ->where('role', $request->role)
            ->exists();

        if ($alreadyRated) {
            return redirect()->back()->with('error', 'この取引はすでに評価済みです。');
        }

        Rating::create([
            'rater_id' => auth()->id(),
            'ratee_id' => $request->ratee_id,
            'item_id' => $request->item_id,
            'role' => $request->role,
            'rating' => $request->rating,
        ]);

        $item = Item::find($request->item_id);
        if ($item && $item->chats()->exists()) {
            $chat = $item->chats()->first();
            Message::create([
                'chat_id' => $chat->id,
                'user_id' => auth()->id(),
                'message' => '評価を完了しました！',
            ]);
        }

        // 【追加部分】購入者が評価をした場合に出品者にメール通知を送る
        if ($request->role === 'seller') {
            $seller = User::findOrFail($request->ratee_id);
            $buyerName = auth()->user()->name;
            $itemName = $item->name;

            Mail::to($seller->email)->send(new SellerRatedNotification($buyerName, $itemName));
        }

        return redirect()->route('items.index')->with('success', '評価を送信しました。');
    }
}
