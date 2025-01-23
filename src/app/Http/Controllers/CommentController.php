<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Item;

class CommentController extends Controller
{
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        $item->comments()->create([
            'content' => $request->input('comment'),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('item.show', $item->id)->with('success', 'コメントを投稿しました。');
    }
}
