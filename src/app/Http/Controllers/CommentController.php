<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{

    public function store(CommentRequest $request, Item $item)
    {
        // ログインしていない場合、ログインページにリダイレクト
        if (!Auth::check()) {
            return redirect()->route('login')->with('message', 'ログインが必要です。');
        }

        // コメントを作成
        $item->comments()->create([
            'content' => $request->input('content'),
            'user_id' => Auth::id(),
        ]);

        // 商品詳細ページにリダイレクト
        return redirect()->route('item.show', $item->id)->with('success', 'コメントを投稿しました。');
    }
}
