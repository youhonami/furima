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

        if ($filter === 'mylist') {
            $items = Auth::user()->likedItems; // ユーザーが「いいね」した商品のみ取得
        } else {
            $items = Item::all(); // 全商品を取得（おすすめ用）
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
