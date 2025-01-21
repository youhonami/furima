<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return view('index', compact('items'));
    }

    public function item()
    {
        // アイテムを取得して渡す
        $item = Item::first();  // ここは必要に応じて修正
        return view('item', compact('item'));
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
