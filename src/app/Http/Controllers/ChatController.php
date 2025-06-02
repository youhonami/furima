<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * チャットルームを表示
     */
    public function show($chatId)
    {
        $chat = Chat::with(['item', 'seller', 'buyer', 'messages.user'])->findOrFail($chatId);

        // ログインユーザーが参加者であることを確認
        if (!in_array(Auth::id(), [$chat->seller_id, $chat->buyer_id])) {
            abort(403, '権限がありません');
        }

        // チャット相手
        $partner = Auth::id() === $chat->seller_id ? $chat->buyer : $chat->seller;

        // その他の取引中チャット（現在のチャットを除外）
        $otherChats = Chat::with(['item'])
            ->where(function ($query) use ($chat, $partner) {
                $query->where('seller_id', Auth::id())
                    ->orWhere('buyer_id', Auth::id());
            })
            ->where('id', '!=', $chat->id)
            ->get();

        return view('chat.show', [
            'chat' => $chat,
            'item' => $chat->item,
            'partner' => $partner,
            'messages' => $chat->messages()->with('user')->orderBy('created_at')->get(),
            'otherChats' => $otherChats
        ]);
    }

    /**
     * メッセージを送信
     */
    public function store(Request $request, $chatId)
    {
        $chat = Chat::findOrFail($chatId);

        // ログインユーザーが参加者であることを確認
        if (!in_array(Auth::id(), [$chat->seller_id, $chat->buyer_id])) {
            abort(403, '権限がありません');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        Message::create([
            'chat_id' => $chat->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        return redirect()->route('chat.show', $chat->id);
    }
}
