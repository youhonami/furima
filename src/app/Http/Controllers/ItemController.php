<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'recommended');

        // ログインユーザーが出品した商品を除外
        $query = Item::query();
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id()); // ログインユーザーが出品した商品を除外
        }

        // フィルタリング条件
        if ($filter === 'mylist') {
            // ログインしていない場合、マイリストは空にする
            if (Auth::check()) {
                $items = Auth::user()->likedItems; // ユーザーが「いいね」した商品のみ取得
            } else {
                $items = collect(); // ログインしていない場合は空のコレクション
            }
        } else {
            $items = $query->get(); // フィルターなしの場合、除外した商品のリストを取得
        }

        return view('index', compact('items'));
    }


    // 商品詳細ページ
    public function show($id)
    {
        $item = Item::findOrFail($id);
        return view('item', compact('item'));
    }

    public function store(Request $request)
    {
        $item = Item::create($request->only(['name', 'price', 'description', 'img', 'condition_id']));

        // 選択されたカテゴリを関連付ける
        $item->categories()->sync($request->input('category_ids'));
    }
}
