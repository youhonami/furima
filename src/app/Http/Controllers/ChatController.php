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

        if (!in_array(Auth::id(), [$chat->seller_id, $chat->buyer_id])) {
            abort(403, '権限がありません');
        }

        $partner = Auth::id() === $chat->seller_id ? $chat->buyer : $chat->seller;

        $otherChats = Chat::with(['item'])
            ->where(function ($query) use ($chat) {
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

    /**
     * メッセージ編集画面を表示
     */
    public function edit($chatId, $messageId)
    {
        $chat = Chat::with('item')->findOrFail($chatId);
        $message = Message::findOrFail($messageId);

        if ($message->user_id !== Auth::id()) {
            abort(403, '権限がありません');
        }

        return view('chat.edit', compact('chat', 'message'));
    }

    /**
     * メッセージを更新
     */
    public function update(Request $request, $chatId, $messageId)
    {
        $chat = Chat::findOrFail($chatId);
        $message = Message::findOrFail($messageId);

        if ($message->user_id !== Auth::id()) {
            abort(403, '権限がありません');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message->update([
            'message' => $validated['message'],
        ]);

        return redirect()->route('chat.show', $chat->id)->with('success', 'メッセージを更新しました');
    }

    /**
     * メッセージを削除
     */
    public function destroy($chatId, $messageId)
    {
        $chat = Chat::findOrFail($chatId);
        $message = Message::findOrFail($messageId);

        if ($message->user_id !== Auth::id()) {
            abort(403, '権限がありません');
        }

        $message->delete();

        return redirect()->route('chat.show', $chat->id)->with('success', 'メッセージを削除しました');
    }
}
