<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Item $item)
    {
        // ログインしていない場合、ログインページにリダイレクト
        if (!Auth::check()) {
            return redirect()->route('login')->with('message', 'ログインが必要です。');
        }

        // バリデーション
        $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        // コメントを作成
        $item->comments()->create([
            'content' => $request->input('comment'),
            'user_id' => Auth::id(),
        ]);

        // 商品詳細ページにリダイレクト
        return redirect()->route('item.show', $item->id)->with('success', 'コメントを投稿しました。');
    }
}
