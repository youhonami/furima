<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Item;
use App\Models\Message;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreChatMessageRequest;

class ChatController extends Controller
{
    //チャット画面表示
    public function show($chatId)
    {
        $chat = Chat::with(['item', 'seller', 'buyer', 'messages.user'])->findOrFail($chatId);

        // 閲覧権限チェック
        if (!in_array(Auth::id(), [$chat->seller_id, $chat->buyer_id])) {
            abort(403, '権限がありません');
        }

        // 未読→既読
        $chat->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $user    = Auth::user();
        $partner = Auth::id() === $chat->seller_id ? $chat->buyer : $chat->seller;
        $item    = $chat->item;

        //自分が BUYER
        $buyerItems = Item::whereHas('purchases', fn($q) => $q->where('user_id', $user->id))->get();

        //自分が SELLER で売れた商品
        $sellerSoldItems = Item::where('user_id', $user->id)
            ->whereHas('purchases')
            ->get();

        //自分が関与するメッセージ付き商品
        $messagedItems = Item::whereHas('chats', function ($q) use ($user) {
            $q->where(fn($qq) => $qq->where('seller_id', $user->id)
                ->orWhere('buyer_id',  $user->id))
                ->whereHas('messages');
        })->get();

        // 結合 + 重複除外 + 今表示中の商品を除外
        $otherItems = $buyerItems
            ->merge($sellerSoldItems)
            ->merge($messagedItems)
            ->unique('id')
            ->where('id', '!=', $item->id);

        //評価関連フラグ
        $hasBuyerRated = Rating::where('rater_id', $user->id)
            ->where('ratee_id', $partner->id)
            ->where('item_id', $item->id)
            ->where('role', 'seller')
            ->exists();

        $hasSellerRated = Rating::where('rater_id', $user->id)
            ->where('ratee_id', $partner->id)
            ->where('item_id', $item->id)
            ->where('role', 'buyer')
            ->exists();

        $receivedCompleteMessage = Message::where('chat_id', $chat->id)
            ->where('user_id', '!=', $user->id)
            ->where('message', '評価を完了しました！')
            ->exists();

        return view('chat.show', [
            'chat'                   => $chat,
            'item'                   => $item,
            'partner'                => $partner,
            'user'                   => $user,
            'messages'               => $chat->messages()->with('user')->orderBy('created_at')->get(),
            'otherItems'             => $otherItems,
            'hasBuyerRated'          => $hasBuyerRated,
            'hasSellerRated'         => $hasSellerRated,
            'receivedCompleteMessage' => $receivedCompleteMessage,
        ]);
    }

    //メッセージ送信
    public function store(StoreChatMessageRequest $request, $chatId)
    {
        $chat = Chat::findOrFail($chatId);
        if (!in_array(Auth::id(), [$chat->seller_id, $chat->buyer_id])) {
            abort(403, '権限がありません');
        }

        $validated = $request->validated();
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('messages', 'public')
            : null;

        Message::create([
            'chat_id'    => $chat->id,
            'user_id'    => Auth::id(),
            'message'    => $validated['message'],
            'image_path' => $imagePath,
        ]);

        return redirect()->route('chat.show', $chat->id);
    }

    //メッセージ編集
    public function edit($chatId, $messageId)
    {
        $chat    = Chat::with('item')->findOrFail($chatId);
        $message = Message::findOrFail($messageId);
        if ($message->user_id !== Auth::id()) {
            abort(403, '権限がありません');
        }
        return view('chat.edit', compact('chat', 'message'));
    }

    public function update(Request $request, $chatId, $messageId)
    {
        $chat    = Chat::findOrFail($chatId);
        $message = Message::findOrFail($messageId);
        if ($message->user_id !== Auth::id()) {
            abort(403, '権限がありません');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'image'   => 'nullable|image|max:2048',
        ]);

        $message->message = $validated['message'];

        // 画像再アップロード
        if ($request->hasFile('image')) {
            if ($message->image_path && Storage::disk('public')->exists($message->image_path)) {
                Storage::disk('public')->delete($message->image_path);
            }
            $message->image_path = $request->file('image')->store('messages', 'public');
        }

        $message->save();
        return redirect()->route('chat.show', $chat->id)->with('success', 'メッセージを更新しました');
    }

    //メッセージ削除
    public function destroy($chatId, $messageId)
    {
        $chat    = Chat::findOrFail($chatId);
        $message = Message::findOrFail($messageId);
        if ($message->user_id !== Auth::id()) {
            abort(403, '権限がありません');
        }

        if ($message->image_path) {
            Storage::disk('public')->delete($message->image_path);
        }

        $message->delete();
        return redirect()->route('chat.show', $chat->id)->with('success', 'メッセージを削除しました');
    }
}
