<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Item;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SellerRatedNotification;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        // バリデーション（named error bag）
        $validator = Validator::make($request->all(), [
            'ratee_id' => 'required|exists:users,id',
            'item_id' => 'required|exists:items,id',
            'role' => 'required|in:seller,buyer',
            'rating' => 'required|integer|min:1|max:5',
        ], [
            'ratee_id.required' => '評価対象者が不明です。',
            'item_id.required' => '商品が不明です。',
            'role.required' => '評価対象の役割が不明です。',
            'rating.required' => '評価を選択してください。',
            'rating.integer' => '評価は数字で入力してください。',
            'rating.min' => '評価は1以上を選択してください（0は選べません）。',
            'rating.max' => '評価は5以下にしてください。',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'ratingErrors');
        }

        // 二重評価防止
        $alreadyRated = Rating::where('rater_id', auth()->id())
            ->where('ratee_id', $request->ratee_id)
            ->where('item_id', $request->item_id)
            ->where('role', $request->role)
            ->exists();

        if ($alreadyRated) {
            return redirect()->back()->with('error', 'この取引はすでに評価済みです。');
        }

        // 評価を保存
        Rating::create([
            'rater_id' => auth()->id(),
            'ratee_id' => $request->ratee_id,
            'item_id' => $request->item_id,
            'role' => $request->role,
            'rating' => $request->rating,
        ]);

        // 評価完了メッセージ送信
        $item = Item::find($request->item_id);
        if ($item && $item->chats()->exists()) {
            $chat = $item->chats()->first();
            Message::create([
                'chat_id' => $chat->id,
                'user_id' => auth()->id(),
                'message' => '評価を完了しました！',
            ]);
        }

        // 出品者にメール通知
        if ($request->role === 'seller') {
            $seller = User::findOrFail($request->ratee_id);
            $buyerName = auth()->user()->name;
            $itemName = $item->name;

            Mail::to($seller->email)->send(new SellerRatedNotification($buyerName, $itemName));
        }

        return redirect()->route('items.index')->with('success', '評価を送信しました。');
    }
}
