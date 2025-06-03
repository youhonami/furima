<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreChatMessageRequest;

class ChatController extends Controller
{
    public function show($chatId)
    {
        $chat = Chat::with(['item', 'seller', 'buyer', 'messages.user'])->findOrFail($chatId);

        if (!in_array(Auth::id(), [$chat->seller_id, $chat->buyer_id])) {
            abort(403, '権限がありません');
        }

        // 未読メッセージを既読にする
        $chat->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

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


    public function store(StoreChatMessageRequest $request, $chatId)
    {
        $chat = Chat::findOrFail($chatId);

        if (!in_array(Auth::id(), [$chat->seller_id, $chat->buyer_id])) {
            abort(403, '権限がありません');
        }

        $validated = $request->validated();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('messages', 'public');
        }

        Message::create([
            'chat_id' => $chat->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'image_path' => $imagePath,
        ]);

        return redirect()->route('chat.show', $chat->id);
    }

    public function edit($chatId, $messageId)
    {
        $chat = Chat::with('item')->findOrFail($chatId);
        $message = Message::findOrFail($messageId);

        if ($message->user_id !== Auth::id()) {
            abort(403, '権限がありません');
        }

        return view('chat.edit', compact('chat', 'message'));
    }

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

    public function destroy($chatId, $messageId)
    {
        $chat = Chat::findOrFail($chatId);
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
